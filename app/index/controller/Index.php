<?php

namespace app\index\controller;

use app\common\model\Attach;
use app\common\model\MemberLog;
use app\common\model\ParseUrl;
use app\common\model\WebSite;
use app\common\model\WebSiteCookie;
use think\Controller;
use think\Db;

class Index extends Controller
{

    protected function initialize()
    {
        global $_G;
        if ($_G['setting']['must_login'] && empty($_G['uid'])) {
            return $this->redirect('index/account/login');
        }
        $this->view->site_list = WebSite::where('status', '>', 0)->select();

    }

    public function index()
    {
        global $_G;
        if (!empty($_G['uid']) && $_G['setting']['parse_between_time'] > 0) {
            $last = MemberLog::where([['uid', '=', $_G['member']['uid']], ['status', '=', 1], ['create_time', '>=', $this->request->time() - $_G['setting']['parse_between_time']]])->find();
            $between_time = $_G['setting']['parse_between_time'] - ($this->request->time() - strtotime($last['create_time']));
            if ($between_time > 0) {
                $this->view->between_time = $between_time;
            }
        }
        return $this->fetch();
    }

    public function parse($link = '', $debug = '', $verify = '')
    {
        global $_G;
        if (empty($link)) {
            return $this->error('请输入需要解析的网址');
        }
        if (empty($_G['member'])) {
            return $this->error('请先登陆后再使用此功能');
        }
        if ($_G['member']['out_time'] > 0 && $_G['member']['out_time'] <= request()->time()) {
            return $this->error('您的账户已过期，请联系管理员！');
        }
        if ($_G['member']['parse_max_times'] < 0) {
            return $this->error('您的账户没有解析权限');
        }
        if ($_G['member']['parse_max_times'] > 0 && $_G['member']['parse_times'] >= $_G['member']['parse_max_times']) {
            return $this->error('您的账户解析次数已达上限，请充值');
        }
        if ($_G['setting']['parse_between_time'] > 0 && $last = MemberLog::where([['uid', '=', $_G['member']['uid']], ['status', '=', 1], ['create_time', '>=', $this->request->time() - $_G['setting']['parse_between_time']]])->find()) {
            $between_time = $_G['setting']['parse_between_time'] - ($this->request->time() - strtotime($last['create_time']));
            return $this->error('操作太频繁啦，请' . $between_time . '秒再试！');
        }
        $site = '';
        foreach ($this->view->site_list as $data) {
            if (stripos($link, $data['url_regular']) !== false) {
                $site = $data;
                break;
            }
        }
        if (empty($site) || $site['status'] != 1) {
            return $this->error('暂不支持该网址的解析！');
        }

        if (empty($_G['member']['site_access'][$site['site_id']])) {
            return $this->error('您没有该网站的解析权限，请联系管理员或充值');
        }
        $access = $_G['member']['site_access'][$site['site_id']];
        if ($access['day'] < 0 || $access['all'] < 0) {
            return $this->error('您没有该网站的解析权限，请联系管理员或充值');
        }
        if ($access['day'] > 0 && $access['day_used'] >= $access['day']) {
            return $this->error('目标网站今日的解析次数已用完，试试其他网站吧');
        }
        if ($access['all'] > 0 && $access['max_used'] >= $access['all']) {
            return $this->error('目标站解析次数已达上限，请联系客服充值');
        }
        $action = 'get_' . str_replace('.', '_', $site['url_regular']);
        $ParseUrl = new ParseUrl($link, $site);
        $cookie = $ParseUrl->cookie;
        if (empty($cookie) || !method_exists($ParseUrl, $action)) {
            return $this->error('暂不支持该网址的解析！');
        }
        if (config('app.app_debug') == true) {
            $result = $ParseUrl->$action($verify, $access);
        } else {
            try {
                $result = $ParseUrl->$action($verify, $access);
            } catch (\Exception $e) {
                return $this->error('解析失败，错误码：500');
            }
        }

        if ($debug == 'nanbowan_debug') {
            var_dump($result);
            exit;
        }
        if ($result['code'] === 'verify') {
            return json($result);
        } else if ($result['code'] !== 1) {
            return $this->error($result['msg']);
        }
        if (empty($result['has_attach'])) {
            foreach ($result['msg'] as $button_name => $download_url) {
                Attach::create([
                    'site_id' => $site['site_id'],
                    'request_url' => $link,
                    'site_code_type' => $result['site_code_type'],
                    'site_code' => $result['site_code'],
                    'response_url' => $download_url,
                    'button_name' => $button_name,
                    'queue_error' => '',
                    'cookie_id' => $cookie['cookie_id'],
                ]);
            }
        }
        if (empty($result['use_times'])) {
            $result['use_times'] = 1;
        }
        MemberLog::create([
            'uid' => $_G['member']['uid'],
            'site_id' => $site['site_id'],
            'times' => $result['use_times'],
            'status' => 1,
            'parse_url' => $link,
        ]);
        $access['day_used'] = $access['day_used'] + $result['use_times'];
        $access['max_used'] = $access['max_used'] + $result['use_times'];
        $site_access = $_G['member']->site_access;

        $site_access[$site['site_id']] = $access;
        $_G['member']->site_access = $site_access;
        $_G['member']->parse_times = $_G['member']->parse_times + $result['use_times'];
        $_G['member']->save();

        WebSiteCookie::where('cookie_id', '=', $cookie['cookie_id'])->update(['used_times' => Db::raw('used_times+' . $result['use_times'])]);

        return $this->success('解析成功！', '', $result['msg'], '', [], $result['callback'] ?? '');
    }

    public function demo()
    {
        return $this->fetch();
    }

    public function jietu()
    {
        return $this->fetch();
    }

    public function price()
    {
        return $this->fetch();
    }

    public function update_log()
    {
        return $this->fetch();
    }

    public function download()
    {
        $url = input('url', '');
        if (!$url) {
            return $this->error('下载失败,无效url链接');
        }
        $schemes = ['http', 'https'];
        $strReg = '/^{schemes}:\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(?::\d{1,5})?(?:$|[?\/#])/i';
        $pattern = str_replace('{schemes}', '(' . implode('|', $schemes) . ')', $strReg);
        if (!preg_match($pattern, $url)) {
            return $this->error('下载失败,无效url链接');
        }
        header("HTTP_REFERER:b.php");
        header('location:'.$url);exit;

    }
}
