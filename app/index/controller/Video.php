<?php
namespace app\index\controller;

use app\common\model\HttpCurl;
use app\common\model\MemberLog;
use think\Controller;

class Video extends Controller
{
    public function index($course = '')
    {
        global $_G;
        $log = MemberLog::where('uid', '=', $_G['uid'])->where('parse_url', '=', 'https://huke88.com/course/' . $course . '.html')->find();
        if (!$log) {
            return $this->error('请通过首页解析资源后观看！', 'index/index/index');
        }
        $cache_time = is_file($course . '.m3u8') ? filectime($course . '.m3u8') : 0;
        $bet_time   = $this->request->time() - $cache_time;
        $header     = [
            'Accept'                    => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'Accept-Encoding'           => 'gzip, deflate, br',
            'Host'                      => 'huke88.com',
            'If-None-Match'             => 'W/"gTiFGRQ0swEawgc7w+6LPzv9/P4"',
            'Referer'                   => 'https://huke88.com/route/ps.html',
            'Upgrade-Insecure-Requests' => '1',
        ];
        $http_curl = new HttpCurl('https://huke88.com/course/' . $course . '.html', 'GET', null, $header, true);
        $html      = $http_curl->send_request()->get_response_body();
        preg_match('/<title>(.*?)<\/title>/', $html, $title);
        $this->view->title = empty($title['1']) ? '' : $title['1'];
        $this->view->m3u8  = $bet_time < 3600 ? $_G['site_url'] . $course . '.m3u8' : '';
        return $this->fetch();
    }

    public function ts($ts = '')
    {
        $content = file_get_contents('https://m3u8.huke88.com' . $ts);
        echo $content;
        exit;
    }

    public function decrypt($url = '')
    {
        $content = file_get_contents($url);
        echo $content;
        exit;
    }
}
