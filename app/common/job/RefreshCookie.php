<?php
namespace app\common\job;

use app\common\model\WebSite;
use app\common\model\WebSiteCookie;
use Jobs\queue\Job;
use think\facade\Env;

class RefreshCookie
{

    public function fire(Job $job, $data)
    {
        foreach (WebSiteCookie::where('status', '=', 1)->select() as $cookie) {
            $site       = WebSite::where('site_id', '=', $cookie['site_id'])->find();
            $cookie_jar = Env::get('runtime_path') . 'site_cookie/cookie_' . $cookie->cookie_id;
            if (is_file($cookie_jar)) {
                $header = [
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
                    'Accept-Encoding: gzip, deflate, br',
                    'Accept-Language: zh-CN,zh;q=0.9',
                    'Cache-Control: max-age=0',
                    'Connection: keep-alive',
                    'Host: ' . str_replace(['http://', 'https://'], '', rtrim($site['url'], '/')),
                    'Referer: ' . $site['url'],
                    'Upgrade-Insecure-Requests: 1',
                ];
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $site['url']);
                curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie_jar);
                curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_jar);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
                curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36');
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_NOBODY, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                $html = curl_exec($curl);
                curl_close($curl);
                if (preg_match('/charset=(\'*|\"*)gbk(\'*|\"*)/', $html) !== 0) {
                    $html = iconv('gbk', 'utf-8', $html);
                }
                file_put_contents(Env::get('runtime_path') . 'site_cookie/cookie_' . $cookie->cookie_id . '.debug', $html);

            }
        }

    }

    public function failed($data)
    {
    }

}
