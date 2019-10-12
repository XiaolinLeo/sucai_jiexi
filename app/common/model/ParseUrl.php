<?php

namespace app\common\model;

use think\facade\Debug;
use think\facade\Env;
use think\facade\Request;

class ParseUrl
{
    private $link;
    private $site;
    public $cookie;

    public function __construct($link, $site)
    {
        $this->link = $link;
        $this->site = $site;
        $this->cookie = $this->get_cookie();
    }

    private function get_cookie($out_id = [])
    {
        $map = [
            ['site_id', '=', $this->site['site_id']],
            ['status', '=', 1],
        ];
        if (!empty($out_id)) {
            $map[] = ['cookie_id', 'not in', $out_id];
        }
        $cookie = WebSiteCookie::where($map)->find();
        if (empty($cookie)) {
            return false;
        }
        if ($cookie['times'] > 0 && $cookie['used_times'] >= $cookie['times']) {
            $out_id[] = $cookie['cookie_id'];
            return $this->get_cookie($out_id);
        }
        return $cookie;
    }

    private function get_cache($site_code = '', $site_code_type = '')
    {
        $query = Attach::where('site_id', $this->site['site_id'])->where('site_code_type', $site_code_type)->where('site_code', $site_code)->where('status', '>', 1)->select();
        if (!$query->isEmpty()) {
            $download = [];
            foreach ($query as $attach) {
                $download[$attach['button_name']] = url('index/download/index', ['attach_id' => $attach['attach_id']]);
            }
            if (!empty($download)) {
                return ['code' => 1, 'has_attach' => 1, 'msg' => $download];
            }
        }
        return false;
    }

    public function get_51yuansu_com()
    {
        preg_match('/\/(\w+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }
        $header = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'Host' => 'www.51yuansu.com',
        ];
        $http_curl = new HttpCurl($this->link, 'GET', null, $header, true, $this->cookie['cookie_id']);
        $html = $http_curl->send_request()->get_response_body();

        $header['Accept'] = 'application/json, text/javascript, */*; q=0.01';
        $header['Referer'] = $this->link;
        $header['X-Requested-With'] = 'XMLHttpRequest';
        $download = [];
        if (preg_match('/data-id="' . $site_code['1'] . '".*?class="p-down-operate /', $html) > 0) {
            $http_curl = new HttpCurl('http://www.51yuansu.com/index.php?m=ajax&a=down&id=' . $site_code['1'], 'GET', null, $header, true, $this->cookie['cookie_id']);
            $response = $http_curl->send_request()->get_response_body();
            $response = json_decode($response, true);
            if (!empty($response['url'])) {
                $download['PNG下载'] = $response['url'];
            }
        }
        if (preg_match('/data-id="' . $site_code['1'] . '".*?class="p-down-operate-zip /', $html) > 0) {
            $http_curl = new HttpCurl('http://www.51yuansu.com/index.php?m=ajax&a=downPsd&id=' . $site_code['1'], 'GET', null, $header, true, $this->cookie['cookie_id']);
            $response = $http_curl->send_request()->get_response_body();
            $response = json_decode($response, true);
            if (!empty($response['url'])) {
                $download['PSD下载'] = $response['url'];
            }
        }
        if (preg_match('/data-id="' . $site_code['1'] . '".*?class="b-down-operate-zip /', $html) > 0) {
            $http_curl = new HttpCurl('http://www.51yuansu.com/index.php?m=ajax&a=bdownPsd&id=' . $site_code['1'], 'GET', null, $header, true, $this->cookie['cookie_id']);
            $response = $http_curl->send_request()->get_response_body();
            $response = json_decode($response, true);
            if (!empty($response['url'])) {
                $download['背景PSD下载'] = $response['url'];
            }
        }
        if (preg_match('/data-id="' . $site_code['1'] . '".*?class="b-down-operate /', $html) > 0) {
            $http_curl = new HttpCurl('http://www.51yuansu.com/index.php?m=ajax&a=bdown&id=' . $site_code['1'], 'GET', null, $header, true, $this->cookie['cookie_id']);
            $response = $http_curl->send_request()->get_response_body();
            $response = json_decode($response, true);
            if (!empty($response['url'])) {
                $download['背景JPG下载'] = $response['url'];
            }
        }
        if (!empty($download)) {
            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => $download];
        }
        return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
    }

    public function get_ooopic_com()
    {
        preg_match('/\/pic_([0-9]+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }
        $header = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'Cache-Control' => 'max-age=0',
            'Host' => 'www.ooopic.com',
            'Referer' => 'https://www.ooopic.com/home-80-422---.html',
        ];
        $http_curl = new HttpCurl('https://downloads.ooopic.com/down_newfreevip.php?id=' . $site_code['1'], 'GET', null, $header, true, $this->cookie['cookie_id']);
        $html = $http_curl->send_request()->get_response_body();

        $html = iconv('GBK', 'UTF-8', $html);
        preg_match('/name=\"token\".*?value=\"(.*?)\"/', $html, $match);
        if (empty($match['1'])) {
            return ['code' => 0, 'msg' => '资源解析失败，请联系管理员'];
        }

        $http_curl = new HttpCurl('https://downloads.ooopic.com/down_newfreevip.php?action=down&id=' . $site_code['1'] . '&token=' . $match['1'], 'GET', null, $header, true, $this->cookie['cookie_id']);
        $response_header = $http_curl->request_curlopts([
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0,
        ])->send_request()->get_response_header();
        if (!empty($response_header['location'])) {
            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $response_header['location']]];
        } else if (!empty($response_header['info']['redirect_url'])) {
            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $response_header['info']['redirect_url']]];
        }
        return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
    }

    public function get_58pic_com($verify = '')
    {
        preg_match('/\/([0-9]+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }
        if (!empty($verify)) {
            $header = [
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Content-Length' => strlen('code=' . $verify),
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Host' => 'www.58pic.com',
                'Origin' => 'https://www.58pic.com',
                'Referer' => $this->link,
                'X-Requested-With' => 'XMLHttpRequest',
            ];
            $http_curl = new HttpCurl('https://www.58pic.com/index.php?m=showLimit&a=ajaxVerifyCode', 'POST', 'code=' . $verify, $header, true, $this->cookie['cookie_id']);
            $v_result = $http_curl->send_request()->get_response_body();
            $v_result = json_decode($v_result, true);
            if (!empty($v_result) && $v_result['sucess'] == 1) {
                return $this->get_58pic_com();
            }
        }
        $header = [

            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Cache-Control' => 'max-age=0',
            'Host' => 'dl.58pic.com',
        ];
        $http_curl = new HttpCurl('https://dl.58pic.com/' . $site_code['1'] . '.html', 'GET', null, $header, true, $this->cookie['cookie_id']);
        $html = $http_curl->send_request()->get_response_body();
        $html = iconv('gbk', 'utf-8', $html);
        if (preg_match('/<h3>为了预防过度疲劳，请先休息片刻。<\/h3>/', $html) > 0) {
            preg_match('/<div class="content_div verifyCodeImgBox">(.*?)<\/div>/s', $html, $verify);
            if (empty($verify['1'])) {
                return ['code' => 0, 'msg' => '验证码解析失败，请联系管理员'];
            }
            preg_match('/<img src="(.*?)" width="/', $verify['1'], $verify_src);
            if (empty($verify_src['1'])) {
                return ['code' => 0, 'msg' => '验证码生成失败，请联系管理员'];
            }
            $header = [
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Cache-Control' => 'max-age=0',
                'Host' => 'www.58pic.com',
                'Pragma' => 'no-cache',
                'Referer' => $this->link,
            ];
            $http_curl = new HttpCurl('https://www.58pic.com/?m=showLimit&a=getVerifyCode&r=0', 'GET', null, $header, true, $this->cookie['cookie_id']);
            $result = $http_curl->send_request();
            return ['code' => 'verify', 'area' => '300px', 'title' => '请输入验证码', 'content' => '<div class="px-3 py-5" style="background-color: #393D49; color: #e2e2e2;overflow: auto;"><div class="text-center"><img style="display: inline;width:130px;height:40px;" src="data:image/jpg/png/gif;base64,' . chunk_split(base64_encode($result->get_response_body())) . '"></div><input class="form-control mt-3" type="text" id="verify_code"></div>'];
        }
        preg_match('/<input type="hidden" id="identity" value=\'(.*?)\'\/>/', $html, $identity);
        if (isset($identity['1']) && $identity['1'] == 0) {
            $header2 = [
                'Accept' => 'application/json, text/plain, */*',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Content-Length' => strlen('page=' . $i . '&time=1'),
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Host' => 'www.58pic.com',
                'Origin' => 'https://www.58pic.com',
            ];
            $picids = [];
            for ($i = 0; $i < 5; $i++) {
                $http_curl = new HttpCurl('https://www.58pic.com/index.php?m=userHomePage&a=getUserDlRecord', 'POST', 'page=' . $i . '&time=1', $header2, true, $this->cookie['cookie_id']);
                $result = $http_curl->send_request()->get_response_body();
                $result = json_decode($result, true);
                if (!empty($result['data']['pics']) && is_array($result['data']['pics'])) {
                    foreach ($result['data']['pics'] as $pic) {
                        $picids[] = $pic['picid'];
                    }
                }
            }
            $picids = array_unique($picids);
            $header3 = [
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Host' => 'dl.58pic.com',
                'Origin' => 'https://dl.58pic.com',
                'Referer' => $this->link,
                'X-Requested-With' => 'XMLHttpRequest',
            ];
            $http_curl = new HttpCurl('https://dl.58pic.com/index.php?m=riskControlSystem&a=checkIdentityForDl', 'POST', null, $header3, true, $this->cookie['cookie_id']);
            $result = $http_curl->send_request()->get_response_body();
            $result = json_decode($result, true);
            $check = [];
            foreach ($result['data'] as $chk) {
                foreach ($chk as $val) {
                    if (in_array($val['id'], $picids) && count($check) < 3) {
                        $check[] = $val['id'];
                    }
                }
            }
            $post_data = http_build_query([
                'riskIndex' => 1,
                'answer' => $check,
            ]);
            $header = [
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Content-Length' => strlen($post_data),
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                'Host' => 'dl.58pic.com',
                'Origin' => 'https://dl.58pic.com',
                'Referer' => 'https://dl.58pic.com/' . $site_code['1'] . '.html',
                'X-Requested-With' => 'XMLHttpRequest',
            ];
            $http_curl = new HttpCurl('https://dl.58pic.com/index.php?m=riskControlSystem&a=checkVerifyDlPic1', 'POST', $post_data, $header, true, $this->cookie['cookie_id']);
            $http_curl->send_request();

        }
        preg_match('/attr-type=\"a1\".*?href=\"(.*?)\"/', $html, $href);

        if (!empty($href['1'])) {
            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $href['1']]];
        } else {
            preg_match('/<a href=\"(.*?)\" class=\"text-green\" one-site>/', $html, $href);
            if (!empty($href['1'])) {
                return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $href['1']]];
            }
        }
        return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
    }

    public function get_nipic_com($verify, $access)
    {
        preg_match('/\/([0-9]+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }
        $header = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'Cache-Control' => 'max-age=0',
            'Host' => 'www.nipic.com',
        ];

        $http_curl = new HttpCurl($this->link, 'GET', null, $header, true, $this->cookie['cookie_id']);
        $html = $http_curl->send_request()->get_response_body();
        preg_match('/<div class="fr works-img-price mt5 align-center" style="width:65px;">(.*?)<\/div>/s', $html, $score);
        $score['1'] = intval(str_replace('共享分', '', strip_tags($score['1'])));
        if ($access['day'] > 0 && ($score['1'] > ($access['day'] - $access['day_used']))) {
            return ['code' => 0, 'msg' => '您今日所剩解析次数不足，本次解析需：' . $score['1'] . '次数'];
        }
        if ($access['all'] > 0 && ($score['1'] > ($access['all'] - $access['max_used']))) {
            return ['code' => 0, 'msg' => '您在昵图网的总解析次数不足，本次解析需：' . $score['1'] . '次数'];
        }
        $header['Host'] = 'down.nipic.com';
        $http_curl = new HttpCurl('http://down.nipic.com/download?id=' . $site_code['1'] . '#showMore', 'GET', null, $header, true, $this->cookie['cookie_id']);
        $html = $http_curl->send_request()->get_response_body();

        preg_match('/<input name="__RequestVerificationToken" type="hidden" value="(.*?)"/', $html, $token);

        preg_match_all('/<a href="(.*?)">/', $html, $line);
        $download = [];
        if (!empty($token['1']) && !empty($line['1'])) {
            foreach ($line['1'] as $key => $kid) {
                $postfields = 'id=' . $site_code['1'] . '&kid=' . $kid . '&__RequestVerificationToken=' . $token['1'];
                $header2 = [
                    'Accept' => 'application/json, text/javascript, */*; q=0.01',
                    'Content-Length' => strlen($postfields),
                    'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                    'Host' => 'down.nipic.com',
                    'Origin' => 'http://down.nipic.com',
                    'Referer' => 'http://down.nipic.com/download?id=' . $site_code['1'],
                    'X-Requested-With' => 'XMLHttpRequest',
                ];
                $http_curl = new HttpCurl('http://down.nipic.com/ajax/download_go', 'POST',
                    $postfields,
                    $header2, true, $this->cookie['cookie_id']);
                $result = $http_curl->send_request()->get_response_body();
                $result = json_decode($result, 1);
                if (!empty($result['data']['url'])) {
                    $Host = str_replace(['http://', 'https://'], '', $result['data']['url']);
                    $Host = substr($Host, 0, stripos($Host, '/'));
                    $header3 = [
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
                        'Referer' => 'http://down.nipic.com/download?id=' . $site_code['1'],
                        'Host' => $Host,
                    ];

                    $http_curl = new HttpCurl(str_replace(' ', '%20', trim($result['data']['url'])), 'GET', null, $header3, true, $this->cookie['cookie_id']);
                    if ($http_curl->request_curlopts) {
                        $response_header = $http_curl->request_curlopts([
                            CURLOPT_FOLLOWLOCATION => false,
                            CURLOPT_MAXREDIRS => 0,
                        ])->send_request()->get_response_header();
                    } else {
                        $response_header = $http_curl->send_request()->get_response_data();
                    }

                    if ($http_curl->request_curlopts) {
                        if (!empty($response_header['location'])) {
                            $download[$line['2'][$key]] = $response_header['location'];
                        }
                    } else {
                        preg_match_all('/<a href="(.*?)">/', $response_header, $niTuZip);
                        $download['下载源文件'] = $niTuZip['1'];
                    }
                }
            }
        }
        if (!empty($download)) {
            return ['code' => 1, 'site_code_type' => '', 'use_times' => $score['1'], 'site_code' => $site_code['1'], 'msg' => $download];
        }
        return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
    }

    public function get_90sheji_com()
    {
        preg_match('/\/([0-9]+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }
        $header = [
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
            'Content-Length' => strlen('id=' . $site_code['1']),
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'Host' => '90sheji.com',
            'Origin' => 'http://90sheji.com',
            'Referer' => 'http://90sheji.com/?m=Inspire&a=download&id=' . $site_code['1'],
            'X-Requested-With' => 'XMLHttpRequest',
        ];
        $http_curl = new HttpCurl('http://90sheji.com/index.php?m=inspireAjax&a=getDownloadLink', 'POST', 'id=' . $site_code['1'], $header, true, $this->cookie['cookie_id']);
        $result = $http_curl->send_request()->get_response_body();
        $result = json_decode($result, 1);
        if (!empty($result['link'])) {
            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $result['link']]];
        }
        return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
    }

    public function get_588ku_com()
    {
        $site_code = [];
        $url_type = '';
        //免扣元素 背景图库 设计模板 摄影图库 艺术字 UI设计 商用插画 办公文档 视频 字体库
        foreach (['ycpng', 'ycbeijing', 'moban', 'sheyingtu', 'ycwordart', 'uiweb', 'ichahua', 'office', 'video', 'font', 'ycaudio'] as $type) {
            preg_match('/' . $type . '\/([0-9]+).html/', $this->link, $match);
            if (!empty($match[1])) {
                $url_type = $type;
                $site_code = $match;
                break;
            }
        }
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1'], $url_type);
        if ($cache !== false) {
            return $cache;
        }
        $header = [
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
            'Host' => '588ku.com',
            'Referer' => $this->link,
            'X-Requested-With' => 'XMLHttpRequest',
        ];
        $header2 = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'Cache-Control' => 'max-age=0',
            'Host' => 'dl.588ku.com',
        ];
        $download = [];
        switch ($url_type) {
            case 'ycpng':
                //PNG
                $curl = new HttpCurl('https://588ku.com/?m=element&a=down&id=' . $site_code['1'], 'GET', null, $header, true, $this->cookie['cookie_id']);
                $result = $curl->send_request()->get_response_body();
                $result = json_decode($result, true);
                if (!empty($result['url'])) {
                    $download['下载PNG'] = $result['url'];
                }
                //PSD
                $curl = new HttpCurl('http://dl.588ku.com/down/rar?callback=handleResponse&type=1&picid=' . $site_code['1'], 'GET', null, $header2, true, $this->cookie['cookie_id']);
                $result = str_replace(['handleResponse(', ');'], '', $curl->send_request()->get_response_body());
                $result = json_decode($result, true);
                if (!empty($result['data']['url'])) {
                    $download['下载源文件'] = $result['data']['url'];
                }
                break;
            case 'ycbeijing':
                //JPG
                $curl = new HttpCurl('https://588ku.com/?m=back&a=down&id=' . $site_code['1'], 'GET', null, $header, true, $this->cookie['cookie_id']);
                $result = $curl->send_request()->get_response_body();
                $result = json_decode($result, true);
                if (!empty($result['url'])) {
                    $download['下载JPG'] = $result['url'];
                }
                //PSD
                $curl = new HttpCurl('https://588ku.com/?m=back&a=downpsd&id=' . $site_code['1'], 'GET', null, $header, true, $this->cookie['cookie_id']);
                $result = $curl->send_request()->get_response_body();
                $result = json_decode($result, true);
                if (!empty($result['url'])) {
                    $download['下载源文件'] = $result['url'];
                }
                break;
            case 'moban':
                //PNG
                $curl = new HttpCurl('http://dl.588ku.com/down/pic?callback=handleResponse&type=3&picid=' . $site_code['1'], 'GET', null, $header2, true, $this->cookie['cookie_id']);
                $result = str_replace(['handleResponse(', ');'], '', $curl->send_request()->get_response_body());
                $result = json_decode($result, true);
                if (!empty($result['data']['url'])) {
                    $download['下载JPG'] = $result['data']['url'];
                }

                //源文件
                $curl = new HttpCurl('http://dl.588ku.com/down/rar?callback=handleResponse&type=3&picid=' . $site_code['1'], 'GET', null, $header2, true, $this->cookie['cookie_id']);
                $result = str_replace(['handleResponse(', ');'], '', $curl->send_request()->get_response_body());
                $result = json_decode($result, true);
                if (!empty($result['data']['url'])) {
                    $download['下载源文件'] = $result['data']['url'];
                }
                break;
            case 'sheyingtu':

                $curl = new HttpCurl('http://dl.588ku.com/down/pic?callback=handleResponse&type=10&picid=' . $site_code['1'], 'GET', null, $header2, true, $this->cookie['cookie_id']);
                $result = str_replace(['handleResponse(', ');'], '', $curl->send_request()->get_response_body());
                $result = json_decode($result, true);
                if (!empty($result['data']['url'])) {
                    $download['下载JPG'] = $result['data']['url'];
                }
                break;
            case 'ycwordart':
                //PNG
                $curl = new HttpCurl('http://dl.588ku.com/down/pic?callback=handleResponse&type=6&picid=' . $site_code['1'], 'GET', null, $header2, true, $this->cookie['cookie_id']);
                $result = str_replace(['handleResponse(', ');'], '', $curl->send_request()->get_response_body());
                $result = json_decode($result, true);
                if (!empty($result['data']['url'])) {
                    $download['下载JPG'] = $result['data']['url'];
                }

                //源文件
                $curl = new HttpCurl('http://dl.588ku.com/down/rar?callback=handleResponse&type=6&picid=' . $site_code['1'], 'GET', null, $header2, true, $this->cookie['cookie_id']);
                $result = str_replace(['handleResponse(', ');'], '', $curl->send_request()->get_response_body());
                $result = json_decode($result, true);
                if (!empty($result['data']['url'])) {
                    $download['下载源文件'] = $result['data']['url'];
                }
                break;
            case 'uiweb':

                //源文件
                $curl = new HttpCurl('http://dl.588ku.com/down/rar?callback=handleResponse&type=9&picid=' . $site_code['1'], 'GET', null, $header2, true, $this->cookie['cookie_id']);
                $result = str_replace(['handleResponse(', ');'], '', $curl->send_request()->get_response_body());
                $result = json_decode($result, true);
                if (!empty($result['data']['url'])) {
                    $download['下载源文件'] = $result['data']['url'];
                }
                break;
            case 'ichahua':
                //PNG
                $curl = new HttpCurl('http://dl.588ku.com/down/pic?callback=handleResponse&type=7&picid=' . $site_code['1'], 'GET', null, $header2, true, $this->cookie['cookie_id']);
                $result = str_replace(['handleResponse(', ');'], '', $curl->send_request()->get_response_body());
                $result = json_decode($result, true);
                if (!empty($result['data']['url'])) {
                    $download['下载JPG'] = $result['data']['url'];
                }

                //源文件
                $curl = new HttpCurl('http://dl.588ku.com/down/rar?callback=handleResponse&type=7&picid=' . $site_code['1'], 'GET', null, $header2, true, $this->cookie['cookie_id']);
                $result = str_replace(['handleResponse(', ');'], '', $curl->send_request()->get_response_body());
                $result = json_decode($result, true);
                if (!empty($result['data']['url'])) {
                    $download['下载源文件'] = $result['data']['url'];
                }
                break;
            case 'office':
                //源文件
                $curl = new HttpCurl('http://dl.588ku.com/down/rar?callback=handleResponse&type=4&picid=' . $site_code['1'], 'GET', null, $header2, true, $this->cookie['cookie_id']);
                $result = str_replace(['handleResponse(', ');'], '', $curl->send_request()->get_response_body());
                $result = json_decode($result, true);
                if (!empty($result['data']['url'])) {
                    $download['下载源文件'] = $result['data']['url'];
                }
                break;
            case 'video':
                //源文件
                $curl = new HttpCurl('http://dl.588ku.com/down/rar?callback=handleResponse&type=5&picid=' . $site_code['1'], 'GET', null, $header2, true, $this->cookie['cookie_id']);
                $result = str_replace(['handleResponse(', ');'], '', $curl->send_request()->get_response_body());
                $result = json_decode($result, true);
                if (!empty($result['data']['url'])) {
                    $download['下载源文件'] = $result['data']['url'];
                }
                break;
            case 'ycaudio':
                //源文件
                $curl = new HttpCurl('http://dl.588ku.com/down/rar?callback=handleResponse&type=8&picid=' . $site_code['1'], 'GET', null, $header2, true, $this->cookie['cookie_id']);
                $result = str_replace(['handleResponse(', ');'], '', $curl->send_request()->get_response_body());
                $result = json_decode($result, true);

                $http_curl = new HttpCurl($result['data']['url'], 'GET', null, $header2, true);
                $response_header = $http_curl->request_curlopts([
                    CURLOPT_FOLLOWLOCATION => false,
                    CURLOPT_MAXREDIRS => 0,
                ])->send_request()->get_response_header();
                if (!empty($response_header['location'])) {
                    $download['下载源文件'] = $response_header['location'];
                } else if (!empty($response_header['info']['redirect_url'])) {
                    $download['下载源文件'] = $response_header['info']['redirect_url'];
                }

                break;
            case 'font':
                //源文件
                /*   $curl   = new HttpCurl('http://dl.588ku.com/down/rar?callback=handleResponse&type=5&picid=' . $site_code['1'], 'GET', null, $header, true, $this->cookie['cookie_id']);
                $result = str_replace(['handleResponse(', ');'], '', $curl->send_request()->get_response_body());
                $result = json_decode($result, true);
                if (!empty($result['data']['url'])) {
                $download['下载源文件'] = $result['data']['url'];
                }*/
                break;
        }

        if (!empty($download)) {
            return ['code' => 1, 'site_code_type' => $url_type, 'site_code' => $site_code['1'], 'msg' => $download];
        }
        return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
    }

    public function get_ibaotu_com($verify = '')
    {
        preg_match('/\/([0-9]+).html/', $this->link, $site_code);
        if (!$site_code) {
            echo 1;
            preg_match('/\/([0-9]+)/', $this->link, $site_code);
        }
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }
        if (!empty($verify)) {
            $header = [
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Host' => 'ibaotu.com',
                'Referer' => $this->link,
                'X-Requested-With' => 'XMLHttpRequest',
            ];
            $http_curl = new HttpCurl('https://ibaotu.com/index.php?m=downVarify&a=verifyCaptcha&answer_key=' . $verify . '&callback=' . $site_code['1'], 'GET', null, $header, true, $this->cookie['cookie_id']);
            $v_result = $http_curl->send_request()->get_response_body();
            $v_result = json_decode($v_result, true);
            if (!empty($v_result) && $v_result['status'] == 1) {
                return $this->get_ibaotu_com();
            }
        }
        $header = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Cache-Control' => 'max-age=0',
            'Host' => 'ibaotu.com',
            'Referer' => $this->link,
        ];
        $curl = new HttpCurl('https://ibaotu.com/?m=download&id=' . $site_code['1'], 'GET', null, $header, true, $this->cookie['cookie_id']);
        $html = $curl->send_request()->get_response_body();
        preg_match('/<div class="dl-btn-wrap clearfix"><a data-href="(.*?)" id="downvip".*?>.*?VIP免费下载.*?<\/a>/', $html, $url);

        if (empty($url['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }

        $curl = new HttpCurl('https:' . $url['1'], 'GET', null, $header, true, $this->cookie['cookie_id']);
        $response_header = $curl->request_curlopts([
            CURLOPT_FOLLOWLOCATION => false,
        ])->send_request()->get_response_header();
        if (!empty($response_header['location'])) {
            if (stripos($response_header['location'], 'index.php?m=downVarify') !== false) {
                $curl = new HttpCurl('https:' . $response_header['location'], 'GET', null, $header, true, $this->cookie['cookie_id']);
                $body = $curl->send_request()->get_response_body();
                preg_match('/<div class="yanzheng-wrap"><h3>(.*?)<\/div><\/div><\/div><\/div><!--sem新人注册活动弹窗-->/', $body, $yanzheng);
                if (!empty($yanzheng['1'])) {
                    preg_match('/<p class="tips">请点击图片中的\'<span>(.*?)<\/span>\'字<\/p>/', $yanzheng['1'], $font);
                    preg_match_all('/<img src=".*?" data-key="(.*?)">/', $yanzheng['1'], $codes);
                    $verify = '';
                    foreach ($codes['1'] as $key) {
                        $verify .= '<img class="d-block float-left" src="https://ibaotu.com/index.php?m=downVarify&a=renderCode&k=' . $key . '" data-key="' . $key . '" style="width:160px;height:80px;">';
                    }
                    return ['code' => 'verify', 'area' => '512px', 'title' => '请点击下方的<strong class="text-danger px-2">' . $font['1'] . '</strong>字，只用点击文字即可', 'content' => '<div class="p-3 verify-img-box" style="background-color: #393D49;width:512px;height:272px;">' . $verify . '<input type="hidden" id="verify_code"></div>', 'no_button' => '1'];
                }
            }
            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $response_header['location']]];
        }
        return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
    }

    public function get_699pic_com()
    {
        preg_match('/\/video-([0-9]+).html/', $this->link, $site_code); //视频
        if (!empty($site_code['1'])) {
            $cache = $this->get_cache($site_code['1'], 'video');
            if ($cache !== false) {
                return $cache;
            }
            $header = [
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'Host' => '699pic.com',
                'Referer' => $this->link,
                'X-Requested-With' => 'XMLHttpRequest',
            ];

            $curl = new HttpCurl('http://699pic.com/download/video?id=' . $site_code['1'], 'GET', null, $header, true, $this->cookie['cookie_id']);
            $result = $curl->send_request()->get_response_body();
            $result = json_decode($result, true);
            if (!empty($result['src'])) {
                return ['code' => 1, 'site_code_type' => 'video', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $result['src']]];
            }
        }
        preg_match('/\/font-([0-9]+).html/', $this->link, $site_code); //字体
        if (!empty($site_code['1'])) {
            $cache = $this->get_cache($site_code['1'], 'font');
            if ($cache !== false) {
                return $cache;
            }
            $post_data = 'fid=' . $site_code['1'] . '&download_from=0&sid=0&page_num=0';
            $header = [
                'Accept' => '*/*',
                'Content-Length' => strlen($post_data),
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Host' => '699pic.com',
                'Origin' => 'http://699pic.com',
                'Referer' => $this->link,
                'X-Requested-With' => 'XMLHttpRequest',
            ];

            $curl = new HttpCurl('http://699pic.com/newdownload/font', 'POST', $post_data, $header, true, $this->cookie['cookie_id']);
            $result = $curl->send_request()->get_response_body();
            $result = json_decode($result, true);
            if (!empty($result['url'])) {
                return ['code' => 1, 'site_code_type' => 'font', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $result['url']]];
            }
        }
        preg_match('/\/tupian-([0-9]+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $header = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'Cache-Control' => 'max-age=0',
            'Host' => '699pic.com',
            'Referer' => $this->link,
        ];
        $curl = new HttpCurl($this->link, 'GET', null, $header, true, $this->cookie['cookie_id']);
        $html = $curl->send_request()->get_response_body();
        preg_match('/CONFIG\[\'search_mode\'\] = \'(.*?)\';/', $html, $match);

        preg_match('/<input type="hidden" value="(.*?)" id="byso".*?/', $html, $byso);
        preg_match('/<input type="hidden" value="(.*?)" id="bycat".*?/', $html, $bycat);
        $download = [];
        if (!empty($match['1'])) {
            $cache = $this->get_cache($site_code['1'], $match['1']);
            if ($cache !== false) {
                return $cache;
            }
            $header = [
                'Accept' => '*/*',
                'Content-Length' => strlen('pid=' . $site_code['1'] . '&byso=' . (isset($byso['1']) ? $byso['1'] : 0) . '&bycat=' . (isset($bycat['1']) ? $bycat['1'] : 0)),
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Host' => '699pic.com',
                'Origin' => 'http://699pic.com',
                'Referer' => $this->link,
                'X-Requested-With' => 'XMLHttpRequest',
            ];

            switch ($match['1']) {
                case 'photo':
                    $ajax_url = 'http://699pic.com/download/getDownloadUrl';
                    break;
                case 'vector':
                    $ajax_url = 'http://699pic.com/newdownload/design';
                    break;
                case 'originality':
                    $ajax_url = 'http://699pic.com/download/getDownloadUrl';
                    break;
                case 'chahua':
                    $ajax_url = 'http://699pic.com/download/getDownloadUrl';
                    break;
                case 'yuansu':
                    $ajax_url = 'http://699pic.com/newdownload/yuansu';
                    break;
                case 'peitu':
                    $ajax_url = 'http://699pic.com/newdownload/phoneMap';
                    break;
                case 'gif':
                    $ajax_url = 'http://699pic.com/download/getDownloadUrl';
                    break;
                case 'ppt':
                    $ajax_url = 'http://699pic.com/download/getDownloadUrl';

                    $curl = new HttpCurl($ajax_url, 'POST', 'pid=' . $site_code['1'] . '&byso=' . (isset($byso['1']) ? $byso['1'] : 0) . '&bycat=' . (isset($bycat['1']) ? $bycat['1'] : 0), $header, true, $this->cookie['cookie_id']);
                    $result = $curl->send_request()->get_response_body();
                    $result = json_decode($result, true);
                    if (!empty($result['url'])) {
                        return ['code' => 1, 'site_code_type' => 'ppt', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $result['url']]];
                    }
                    break;
            }
            preg_match_all('/<i class="i-set.*?" data-id=[\'|"](.*?)[\'|"]><var><\/var><\/i>(.*?)<\/span>/', $html, $ids);
            if (empty($ids['1']) || count($ids['1']) <= 0 || empty($ids['1'][0])) {
                return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
            }
            $header['Content-Length'] = strlen('pid=' . $site_code['1'] . '&byso=' . (isset($byso['1']) ? $byso['1'] : 0) . '&bycat=' . (isset($bycat['1']) ? $bycat['1'] : 0) . '&filetype=2');
            foreach ($ids['1'] as $key => $filetype) {
                $curl = new HttpCurl($ajax_url, 'POST', 'pid=' . $site_code['1'] . '&byso=' . (isset($byso['1']) ? $byso['1'] : 0) . '&bycat=' . (isset($bycat['1']) ? $bycat['1'] : 0) . '&filetype=' . $filetype, $header, true, $this->cookie['cookie_id']);
                $response = $curl->send_request()->get_response_body();
                $response = json_decode($response, true);
                if (!empty($response['url'])) {
                    $download[$ids['2'][$key]] = $response['url'];
                }
            }
        }

        if (!empty($download)) {
            return ['code' => 1, 'site_code_type' => isset($match['1']) ? $match['1'] : '', 'site_code' => $site_code['1'], 'msg' => $download];
        }
        return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
    }

    public function get_download_csdn_net()
    {
        preg_match('/download\/.*?\/([0-9]+)/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
    }

    public function get_docer_com()
    {
        preg_match('/\/([0-9]+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }

        $header = [
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
            'Host' => 'detail.docer.com',
            'Referer' => $this->link,
            'X-Requested-With' => 'XMLHttpRequest',
        ];
        $http_curl = new HttpCurl('http://detail.docer.com/detail/dl?id=' . $site_code['1'], 'GET', null, $header, true, $this->cookie['cookie_id']);
        $result = $http_curl->send_request()->get_response_body();
        $result = json_decode($result, 1);
        if (!empty($result['data'])) {
            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $result['data']]];
        }
        return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
    }

    public function get_wenku_baidu_com()
    {
        preg_match('/view\/(\w+)/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }

        $header = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'Cache-Control' => 'max-age=0',
            'Host' => 'wenku.baidu.com',
            'Referer' => 'https://wenku.baidu.com/user/mydownload',
        ];
        $http_curl = new HttpCurl($this->link, 'GET', null, $header, true, $this->cookie['cookie_id']);
        $html = $http_curl->send_request()->get_response_body();
        $html = iconv('GBK', 'UTF-8', $html);
        preg_match('/<form name="downloadForm".*?>(.*?)<\/form>/s', $html, $form);
        if (count($form) < 2) {
            return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
        }
        preg_match('/name=\"ct\".*?value=\"(.*?)\"/', $form['1'], $ct);
        if (count($ct) < 2) {
            return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
        }
        preg_match('/name=\"retType\".*?value=\"(.*?)\"/', $form['1'], $retType);
        if (count($retType) < 2) {
            return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
        }
        preg_match('/name=\"storage\".*?value=\"(.*?)\"/', $form['1'], $storage);
        if (count($storage) < 2) {
            return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
        }
        preg_match('/name=\"useTicket\".*?value=\"(.*?)\"/', $form['1'], $useTicket);
        if (count($useTicket) < 2) {
            return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
        }
        preg_match('/name=\"target_uticket_num\".*?value=\"(.*?)\"/', $form['1'], $target_uticket_num);
        if (count($target_uticket_num) < 2) {
            return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
        }
        preg_match('/name=\"downloadToken\".*?value=\"(.*?)\"/', $form['1'], $downloadToken);
        if (count($downloadToken) < 2) {
            return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
        }
        preg_match('/name=\"sz\".*?value=\"(.*?)\"/', $form['1'], $sz);
        if (count($sz) < 2) {
            return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
        }
        preg_match('/name=\"v_code\".*?value=\"(.*?)\"/', $form['1'], $v_code);
        if (count($v_code) < 2) {
            return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
        }
        preg_match('/name=\"v_input\".*?value=\"(.*?)\"/', $form['1'], $v_input);
        if (count($v_input) < 2) {
            return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
        }
        preg_match('/name=\"req_vip_free_doc\".*?value=\"(.*?)\"/', $form['1'], $req_vip_free_doc);
        if (count($req_vip_free_doc) < 2) {
            return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
        }
        $postfields = http_build_query([
            'ct' => $ct['1'],
            'doc_id' => $site_code['1'],
            'retType' => $retType['1'],
            'sns_type' => '',
            'storage' => $storage['1'],
            'useTicket' => $useTicket['1'],
            'target_uticket_num' => $target_uticket_num['1'],
            'downloadToken' => $downloadToken['1'],
            'sz' => $sz['1'],
            'v_code' => $v_code['1'],
            'v_input' => $v_input['1'],
            'req_vip_free_doc' => $req_vip_free_doc['1'],
        ]);
        $http_curl = new HttpCurl('https://wenku.baidu.com/user/submit/download', 'POST', $postfields, [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Cache-Control' => 'max-age=0',
            'Content-Length' => strlen($postfields),
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Host' => 'wenku.baidu.com',
            'Origin' => 'https://wenku.baidu.com',
            'Referer' => $this->link,
        ], true, $this->cookie['cookie_id']);
        $response_header = $http_curl->request_curlopts([
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0,
            CURLOPT_ENCODING => 'gzip',
        ])->send_request()->get_response_header();
        if (empty($response_header['location'])) {
            return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
        }
        preg_match('/filename=\"(.*?)\"/', urldecode(urldecode($response_header['location'])), $filename);
        if (!isset($filename['1'])) {
            return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
        }

        $attach = Attach::create([
            'site_id' => $this->site['site_id'],
            'request_url' => $this->link,
            'site_code' => $site_code['1'],
            'filename' => $filename['1'],
            'response_url' => $response_header['location'],
            'button_name' => '立即下载',
            'queue_error' => '',
            'cookie_id' => $this->cookie['cookie_id'],
            'status' => 0,
        ]);
        Debug::remark('begin');
        $request = new \Http\RequestCore($attach->response_url);

        $request->set_write_file($attach->local_file);
        try {
            $request->send_request();
        } catch (\Exception $e) {
            $attach->queue_error = $e->getMessage();
            $attach->save();
            return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
        }

        Debug::remark('end');
        $attach->queue_error = '';
        $attach->download_time = Debug::getRangeTime('begin', 'end');
        if (!is_file($attach->local_file)) {
            return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
        }
        $attach->savename = md5(request()->time() . random(10)) . '.' . pathinfo($filename['1'], PATHINFO_EXTENSION);
        $attach->filesize = filesize($attach->local_file);
        $attach->status = 2;
        $attach->save();
        return ['code' => 1, 'site_code_type' => '', 'has_attach' => 1, 'site_code' => $site_code['1'], 'msg' => ['立即下载' => url('index/download/index', ['attach_id' => $attach['attach_id']])]];

    }

    public function get_tukuppt_com()
    {
        preg_match('/\/(\w+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }
        $header = [
            'Accept' => 'text/javascript, application/javascript, application/ecmascript, application/x-ecmascript, */*; q=0.01',
            'Host' => 'www.tukuppt.com',
            'Referer' => 'https://www.tukuppt.com/muban/' . $site_code['1'] . '.html',
            'X-Requested-With' => 'XMLHttpRequest',
        ];

        $curl = new HttpCurl('https://www.tukuppt.com/index/down?callback=jQuery&pid=' . $site_code['1'] . '&code=&ispng=0', 'GET', null, $header, true, $this->cookie['cookie_id']);
        $result = str_replace(['jQuery(', ');'], '', $curl->send_request()->get_response_body());
        $result = json_decode($result, true);
        if (!empty($result['downurl'])) {
//            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $result['downurl']], 'callback' => 'copyDownload'];
            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $result['downurl']]];
        }
        return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
    }

    public function get_92sucai_com()
    {
        preg_match('/\/([0-9]+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }
        $header = [
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
            'Host' => 'www.92sucai.com',
            'Referer' => 'http://www.92sucai.com/yinleyinxiao/' . $site_code['1'] . '.html',
            'X-Requested-With' => 'XMLHttpRequest',
        ];
        $curl = new HttpCurl('http://www.92sucai.com/down.html?aid=' . $site_code['1'] . '&key=0', 'GET', null, $header, true, $this->cookie['cookie_id']);
        $result = $curl->send_request()->get_response_body();
        $result = json_decode($result, true);
        if (!empty($result['url'])) {
            $result['url'] = urldecode($result['url']);
            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $result['url']]];
        }
        return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
    }

    public function get_yanj_cn()
    {
        preg_match('/\/([0-9]+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }
    }

    public function get_pic_netbian_com()
    {
        preg_match('/\/([0-9]+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }
        $header = [
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
            'Host' => 'pic.netbian.com',
            'Referer' => 'http://pic.netbian.com/tupian/' . $site_code[1] . '.html',
            'X-Requested-With' => 'XMLHttpRequest',
        ];
        $curl = new HttpCurl('http://pic.netbian.com/e/extend/downpic.php?id=' . $site_code['1'], 'GET', null, $header, true, $this->cookie['cookie_id']);
        $result = $curl->send_request()->get_response_body();
        $result = json_decode($result, true);
        if (!empty($result['pic'])) {
            $href = 'http://pic.netbian.com/' . ltrim($result['pic'], '/');
            $attach = Attach::create([
                'site_id' => $this->site['site_id'],
                'request_url' => $this->link,
                'site_code' => $site_code['1'],
                'filename' => $site_code['1'] . '.zip',
                'response_url' => $href,
                'button_name' => '立即下载',
                'queue_error' => '',
                'cookie_id' => $this->cookie['cookie_id'],
                'status' => 1,
            ]);
            Debug::remark('begin');

            $http_curl = new HttpCurl($href, 'GET', null, $header, true, $this->cookie['cookie_id']);
            $response = $http_curl->timeout(3600)->send_request();

            $fp = fopen($attach->local_file, 'w');
            fwrite($fp, $response->get_response_body());
            fclose($fp);
            $response_header = $response->get_response_header();
            if (!empty($response_header['content-disposition'])) {
                $response_header['content-disposition'] = iconv('gbk', 'utf-8', $response_header['content-disposition']);
                preg_match('/filename=\"(.*?)\"/', $response_header['content-disposition'], $disposition);
                if (!empty($disposition['1'])) {
                    $attach->filename = $disposition['1'];
                    $attach->savename = md5(request()->time() . random(10)) . '.' . pathinfo($disposition['1'], PATHINFO_EXTENSION);
                }
            }
            Debug::remark('end');

            $attach->download_time = Debug::getRangeTime('begin', 'end');
            if (!is_file($attach->local_file)) {
                return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
            }

            $ext = $this->get_ext($attach->local_file);
            if ($ext !== 'unknow') {
                $attach->savename = md5(request()->time() . random(10)) . '.' . $ext;
            }
            $attach->filesize = filesize($attach->local_file);
            $attach->status = 2;
            $attach->save();
            return ['code' => 1, 'site_code_type' => '', 'has_attach' => 1, 'site_code' => $site_code['1'], 'msg' => ['立即下载' => url('index/download/index', ['attach_id' => $attach['attach_id']])]];
        }
        return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
    }

    public function get_huiyi8_com()
    {
        preg_match('/\/([0-9]+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }
        $header = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Cache-Control' => 'max-age=0',
            'Host' => 'www.huiyi8.com',
            'Referer' => 'https://www.huiyi8.com/',
        ];
        $http_curl = new HttpCurl('https://www.huiyi8.com/member/hauth/down/' . $site_code['1'], 'GET', null, $header, true, $this->cookie['cookie_id']);
        $response_header = $http_curl->request_curlopts([
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0,
        ])->send_request()->get_response_header();
        if (!empty($response_header['location'])) {
            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $response_header['location']]];
        }
        return ['code' => 0, 'msg' => '资源解析失败，请联系管理员'];
    }

    public function get_88tph_com()
    {
        preg_match('/\/([0-9]+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }
        $postfields = http_build_query(['doc' => $site_code[1], 'vip' => true]);
        $header = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Referer' => $this->link,
            'X-Requested-With' => 'XMLHttpRequest',
        ];

        $http_curl = new HttpCurl('https://www.88tph.com/geturl', 'POST', $postfields, $header, true, $this->cookie['cookie_id']);
        $result = $http_curl->send_request()->get_response_body();
        $result = json_decode($result, 1);
        if (!empty($result['body']['url'])) {
//            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $result['body']['url']], 'callback' => 'copyDownload'];
            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $result['body']['url']]];
        }
        return ['code' => 0, 'msg' => '解析失败，请联系管理员。'];
    }

    public function get_51miz_com()
    {
        preg_match('/\/([0-9]+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }

        $download = [];
        $header = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'Host' => 'www.51miz.com',
            'Referer' => $this->link,
        ];
        $http_curl = new HttpCurl('http://www.51miz.com/?m=download&a=download&id=' . $site_code['1'] . '&plate_id=17&format=source', 'GET', null, $header, true, $this->cookie['cookie_id']);
        $response_header = $http_curl->request_curlopts([
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0,
        ])->send_request()->get_response_header();

        if (!empty($response_header['location'])) {
            $download['下载PSD'] = $response_header['location'];
        }
        $http_curl = new HttpCurl('http://www.51miz.com/?m=download&a=download&id=' . $site_code['1'] . '&plate_id=17&format=image', 'GET', null, $header, true, $this->cookie['cookie_id']);
        $response_header = $http_curl->request_curlopts([
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0,
        ])->send_request()->get_response_header();

        if (!empty($response_header['location'])) {
            $download['下载PNG'] = $response_header['location'];
        }
        if (!empty($download)) {
            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => $download];
        }
        return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
    }

    public function get_16pic_com()
    {
        preg_match('/\/pic_([0-9]+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }

        $header = [
            ':authority' => 'www.16pic.com',
            ':method' => 'GET',
            ':path' => '/down/down?id=' . $site_code['1'] . '&from=1',
            ':scheme' => 'https',
            'accept' => 'application/json, text/javascript, */*; q=0.01',
            'accept-encoding' => 'gzip, deflate, br',
            'referer' => $this->link,
            'x-requested-with' => 'XMLHttpRequest',
        ];
        $http_curl = new HttpCurl('https://www.16pic.com/down/down?id=' . $site_code['1'] . '&from=1', 'GET', null, $header, true, $this->cookie['cookie_id']);
        $result = $http_curl->send_request()->get_response_body();
        $result = json_decode($result, 1);
        if (!empty($result['res_data'])) {
            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $result['res_data']]];
        }
        return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
    }

    public function get_125pic_com()
    {
        preg_match('/\/([0-9]+)/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }
        $postfields = '{"id":' . $site_code['1'] . ',"t":' . Request::time() . random(3, 1) . '}';
        $header = [
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
            'Content-Length' => strlen($postfields),
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'Host' => 'www.125pic.com',
            'Origin' => 'http://www.125pic.com',
            'Referer' => $this->link,
            'X-Requested-With' => 'XMLHttpRequest',
        ];
        $http_curl = new HttpCurl('http://www.125pic.com/api/sucai/download', 'POST', $postfields, $header, true, $this->cookie['cookie_id']);
        $result = $http_curl->send_request()->get_response_body();
        $result = json_decode($result, 1);
        if (!empty($result['data']['url'])) {
            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $result['data']['url']]];
        }
        return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
    }

    public function get_ppt_dwuva_com()
    {
        preg_match('/\/([A-Za-z0-9]+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }
        $header = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'Host' => 'ppt.dwuva.com',
        ];

        $http_curl = new HttpCurl('http://ppt.dwuva.com/download?id=' . $site_code['1'], 'GET', null, $header, true, $this->cookie['cookie_id']);

        $response_header = $http_curl->request_curlopts([
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0,
        ])->send_request()->get_response_header();

        if (!empty($response_header['location'])) {
            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $response_header['location']]];
        }

        return ['code' => 0, 'msg' => '解析失败，请点击解析再次尝试!'];
    }

    public function get_ppt118_com()
    {
        preg_match('/\/([A-Za-z0-9]+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }
        $header = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'Host' => 'www.ppt118.com',
        ];
        $http_curl = new HttpCurl('http://www.ppt118.com/download?id=' . $site_code['1'], 'GET', null, $header, true, $this->cookie['cookie_id']);
        $response_header = $http_curl->request_curlopts([
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0,
        ])->send_request()->get_response_header();
        if (!isset($response_header['location'])) {
            $response_header = $http_curl->request_curlopts([
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_MAXREDIRS => 0,
            ])->send_request()->get_response_header();
        }
        if (!empty($response_header['location'])) {
            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $response_header['location']]];
        }
        return ['code' => 0, 'msg' => '解析失败，请点击解析再次尝试!'];
    }

    public function get_yipic_cn()
    {
        preg_match('/\/([0-9]+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }
        $header = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'Cache-Control' => 'max-age=0',
            'Host' => 'api.yipic.cn',
        ];

        $http_curl = new HttpCurl('http://api.yipic.cn/vip_download.html', 'POST', 'm_id=' . $site_code['1'], $header, true, $this->cookie['cookie_id']);
        $result = $http_curl->send_request()->get_response_body();
        $result = json_decode($result, 1);
        if (!empty($result['data'])) {
            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $result['data']]];
        }
        return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
    }

    public function get_photophoto_cn()
    {
        preg_match('/\/([0-9]+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }
        $header = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'Host' => 'app.photophoto.cn',
        ];

        $http_curl = new HttpCurl('http://app.photophoto.cn/?id=' . $site_code['1'], 'GET', null, $header, true, $this->cookie['cookie_id']);
        $html = $http_curl->send_request()->get_response_body();
        preg_match_all('/<a href="(.*?)".*?alt="(.*?)".*?<\/a>/s', $html, $matchs);
        $header2 = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
        ];
        $download = [];
        foreach ($matchs[1] as $key => $url) {
            if (empty(trim($matchs[2][$key]))) {
                continue;
            }
            $host = str_replace(['http://', 'https://'], '', $url);

            $header2['Host'] = substr($host, 0, stripos($host, '/'));

            $http_curl = new HttpCurl($url, 'GET', null, $header2, true, $this->cookie['cookie_id']);
            $response_header = $http_curl->request_curlopts([
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_MAXREDIRS => 0,
            ])->send_request()->get_response_header();
            if (!empty($response_header['location'])) {
                $download[$matchs[2][$key]] = 'http://' . $header2['Host'] . '/' . $response_header['location'];
            } else if (!empty($response_header['redirect_url'])) {
                $download[$matchs[2][$key]] = 'http://' . $header2['Host'] . '/' . $response_header['redirect_url'];
            }

        }
        if (!empty($download)) {
            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => $download];
        }
        return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
    }

    public function get_669pic_com()
    {
        preg_match('/\/([0-9]+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }
        $postfields = 'id=' . $site_code['1'] . '&pictype=1&s_id=';
        $header = [
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
            'Content-Length' => strlen($postfields),
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'Host' => '669pic.com',
            'Origin' => 'http://669pic.com',
            'Referer' => 'http://669pic.com/sc/' . $site_code['1'] . '.html',
            'X-Requested-With' => 'XMLHttpRequest',
        ];

        $http_curl = new HttpCurl('http://669pic.com/?c=Ajax&a=ajaxDownload', 'POST', $postfields, $header, true, $this->cookie['cookie_id']);
        $result = $http_curl->send_request()->get_response_body();
        $result = json_decode($result, 1);
        if (!empty($result['link'])) {
            return ['code' => 1, 'site_code_type' => '', 'site_code' => $site_code['1'], 'msg' => ['立即下载' => $result['link']]];
        }
        return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
    }

    public function get_kuaipng_com()
    {
        preg_match('/\/([0-9]+).html/', $this->link, $site_code);
        if (empty($site_code['1'])) {
            return ['code' => 0, 'msg' => '解析失败，网址输入错误或不支持该站点解析'];
        }
        $cache = $this->get_cache($site_code['1']);
        if ($cache !== false) {
            return $cache;
        }
        $header = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'Host' => 'www.kuaipng.com',
            'Referer' => 'http://www.kuaipng.com/sucai/' . $site_code[1] . '.html',
        ];
        $response_url = 'http://www.kuaipng.com/Download/download/photo_id/' . $site_code['1'] . '/speed/2/type/yuantu';
        Debug::remark('begin');
        $http_curl = new HttpCurl($response_url, 'GET', null, $header, true, $this->cookie['cookie_id']);
        $response = $http_curl->timeout(3600)->send_request();
        Debug::remark('end');
        $response_header = $response->get_response_header();
        if (!empty($response_header['content-disposition'])) {
            $filename = str_replace(['attachment; filename=', '"'], '', $response_header['content-disposition']);
            if (!empty($filename)) {
                $attach = Attach::create([
                    'site_id' => $this->site['site_id'],
                    'request_url' => $this->link,
                    'site_code' => $site_code['1'],
                    'filename' => $filename,
                    'savename' => md5(request()->time() . random(10)) . '.' . pathinfo($filename, PATHINFO_EXTENSION),
                    'response_url' => $response_url,
                    'button_name' => '立即下载',
                    'download_time' => Debug::getRangeTime('begin', 'end'),
                    'queue_error' => '',
                    'cookie_id' => $this->cookie['cookie_id'],
                    'status' => 1,
                ]);
                $fp = fopen($attach->local_file, 'w');
                fwrite($fp, $response->get_response_body());
                fclose($fp);
            }
        }

        if (empty($attach) || !is_file($attach->local_file)) {
            return ['code' => 0, 'msg' => '解析失败，请联系管理员'];
        }

        $ext = $this->get_ext($attach->local_file);
        if ($ext !== 'unknow') {
            $attach->savename = md5(request()->time() . random(10)) . '.' . $ext;
        }
        $attach->filesize = filesize($attach->local_file);
        $attach->status = 2;
        $attach->save();
        return ['code' => 1, 'site_code_type' => '', 'has_attach' => 1, 'site_code' => $site_code['1'], 'msg' => ['立即下载' => url('index/download/index', ['attach_id' => $attach['attach_id']])]];
    }

    private function get_ext($local_file = '', $filename = '')
    {
        $file = fopen($local_file, 'rb');
        $bin = fread($file, 2);
        fclose($file);
        $strInfo = @unpack('C2chars', $bin);
        $typeCode = intval($strInfo['chars1'] . $strInfo['chars2']);
        switch ($typeCode) {
            case 7790:
                $fileType = 'exe';
                break;
            case 7784:
                $fileType = 'midi';
                break;
            case 8297:
                $fileType = 'rar';
                break;
            case 255216:
                $fileType = 'jpg';
                break;
            case 7173:
                $fileType = 'gif';
                break;
            case 6677:
                $fileType = 'bmp';
                break;
            case 13780:
                $fileType = 'png';
                break;
            case 8075:
                $fileType = 'zip';
                break;
            case 4949:
                $fileType = 'tar';
                break;
            case 55122:
                $fileType = '7z';
                break;
            case 5666:
                $fileType = 'psd';
                break;
            default:
                if (empty($filename)) {
                    $fileType = 'unknow';
                } else {
                    $fileType = pathinfo($filename, PATHINFO_EXTENSION);
                }
        }
        return $fileType;
    }
}
