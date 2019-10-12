<?php
namespace app\common\behavior;

use app\common\model\Member;
use app\common\model\Setting;
use app\common\model\WebSite;
use think\facade\Cache;
use think\facade\Cookie;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;

class Common
{
    public function appInit()
    {
        global $_G;
        $login = Session::get('uid');
        if (empty($login) && $auth = Cookie::get('uid') && is_string(Cookie::get('uid'))) {
            $login = json_decode(base64_decode(authcode($auth)), true);
        }
        $_G = [
            'site_url' => Request::domain() . Request::rootUrl() . '/',
            'uid'      => empty($login['uid']) ? 0 : $login['uid'],
            'member'   => false,
            'setting'  => Cache::get('setting'),
            'web_site' => Cache::get('web_site'),
        ];
        if ($_G['uid']) {
            $_G['member'] = Member::where('uid', '=', $_G['uid'])->find();
            if (empty($_G['member']) || md5($_G['member']['password']) !== $login['password']) {
                (new Member)->logout();
            }
            if (!empty($auth) && !empty($_G['member'])) {
                $_G['member']->login();
            }
        }
        if (empty($_G['web_site'])) {
            $_G['web_site'] = [];
            foreach (WebSite::select() as $site) {
                $_G['web_site'][$site['site_id']] = $site;
            }

            Cache::set('web_site', $_G['web_site']);
        }
        if (empty($_G['setting'])) {
            foreach (Setting::select() as $set) {
                $_G['setting'][$set['key']] = $set['value'];
            }
            Cache::set('setting', $_G['setting']);
        }
        View::share('_G', $_G);
    }
}
