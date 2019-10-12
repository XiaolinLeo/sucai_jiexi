<?php

namespace app\common\model;

use think\facade\Env;

class HttpCurl
{
    public $request_url;
    public $request_method;
    public $request_header;
    public $request_postfields;
    public $request_gzip;
    public $request_curlopts;
    public $cookie_file;
    public $timeout = 10;
    public $response_code;

    private $response_data;
    private $response_header;
    private $response_body;

    public function __construct($url = '', $method, $postfields = null, $headers = [], $gzip = false, $cookie_id = 0)
    {
        $this->request_url = $url;
        $this->request_method = $method;
        $this->request_header = array_merge([
            'Accept-Encoding' => 'gzip, deflate',
            'Accept-Language' => 'zh-CN,zh;q=0.9',
            'Connection' => 'keep-alive',
            'Upgrade-Insecure-Requests' => '1',
        ], $headers);
        $this->request_postfields = $postfields;
        $this->request_gzip = $gzip;
        $cookie_file = Env::get('runtime_path') . 'site_cookie/cookie_' . ($cookie_id ?? 0);
        if (is_file($cookie_file)) {
            $this->cookie_file = $cookie_file;
        }
    }

    public function request_url($url = '')
    {
        $this->request_url = $url;
        return $this;
    }

    public function request_method($method = '')
    {
        $this->request_method = $method;
        return $this;
    }

    public function request_header($headers = '')
    {
        $this->request_header = $headers;
        return $this;
    }

    public function request_postfields($postfields = null)
    {
        $this->request_postfields = $postfields;
        return $this;
    }

    public function request_gzip($gzip = '')
    {
        $this->request_gzip = $gzip;
        return $this;
    }

    public function request_curlopts($curlopts = '')
    {
        $this->request_curlopts = $curlopts;
        return $this;
    }

    public function request_cookie_file($cookie_file = '')
    {
        $this->cookie_file = $cookie_file;
        return $this;
    }

    public function timeout($time = 10)
    {
        $this->timeout = $time;
        return $this;
    }

    private function prep_request()
    {
        $header = [];
        foreach ($this->request_header as $key => $value) {
            if (!empty($this->cookie_file) && is_file($this->cookie_file) && $key == 'Cookie') {
                continue;
            }
            $header[] = $key . ': ' . $value;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($curl, CURLOPT_URL, $this->request_url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (!empty($this->cookie_file) && is_file($this->cookie_file)) {
            curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookie_file);
            curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookie_file);
        }
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36");
        switch ($this->request_method) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true);
                if (!empty($this->request_postfields)) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, is_array($this->request_postfields) ? http_build_query($this->request_postfields) : $this->request_postfields);
                }
                break;
            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->request_method);
                break;
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        if ($this->request_gzip == true) {
            curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if (isset($this->request_curlopts) && sizeof($this->request_curlopts) > 0) {
            foreach ($this->request_curlopts as $k => $v) {
                curl_setopt($curl, $k, $v);
            }
        }
        return $curl;
    }

    public function send_request()
    {
        $curl = $this->prep_request();
        try {
            $this->response_data = curl_exec($curl);
            if (is_resource($curl)) {
                $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
                $this->response_header = substr($this->response_data, 0, $header_size);
                $this->response_body = substr($this->response_data, $header_size);
                $this->response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $this->response_effective_url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
                $this->response_info = curl_getinfo($curl);
                $this->response_header = explode("\r\n\r\n", trim($this->response_header));
                $this->response_header = array_pop($this->response_header);
                $this->response_header = explode("\r\n", $this->response_header);
                array_shift($this->response_header);

                $header_assoc = [];
                foreach ($this->response_header as $header) {
                    $kv = explode(': ', $header);
                    $header_assoc[strtolower($kv[0])] = isset($kv[1]) ? $kv[1] : '';
                }

                $this->response_header = $header_assoc;
                $this->response_header['info'] = $this->response_info;
                $this->response_header['info']['method'] = $this->request_method;
                $this->response_header['info']['effective_url'] = $this->response_effective_url;

            }

            curl_close($curl);
        } catch (\Exception $e) {

        }
        return $this;
    }

    public function get_response_data()
    {
        return $this->response_data;
    }

    public function get_response_header($header = null)
    {
        if ($header) {
            return $this->response_header[strtolower($header)];
        }
        return $this->response_header;
    }

    public function get_response_body()
    {
        return $this->response_body;
    }

    public function get_response_property($name = '')
    {
        if (!$name) {
            return '';
        }
        if ($this->$name) {
            return $this->$name;
        }

    }
}
