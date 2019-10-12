<?php
namespace app\common\model;

use think\facade\Cache;
use think\facade\Cookie;
use think\facade\Request;
use think\facade\Session;
use think\Model;
use think\model\concern\SoftDelete;

class Member extends Model
{
    use SoftDelete;
    protected $pk                 = 'uid';
    protected $autoWriteTimestamp = true;
    protected $createTime         = 'register_time';
    protected $insert             = ['register_ip', 'last_time', 'last_ip', 'site_access'];
    protected $updateTime         = '';
    protected $deleteTime         = 'delete_time';
    protected $defaultSoftDelete  = 0;
    protected $json               = ['site_access'];
    protected $jsonAssoc          = true;

    public static function init()
    {
    }

    protected function setPasswordAttr($value)
    {
        return password_hash(md5($value), PASSWORD_DEFAULT);
    }

    protected function setRegisterTimeAttr($value)
    {
        return Request::time();
    }

    protected function setRegisterIpAttr($value)
    {
        return Request::ip();
    }

    protected function setLastTimeAttr($value)
    {
        return Request::time();
    }

    protected function setLastIpAttr($value)
    {
        return Request::ip();
    }

    protected function setSiteAccess($value)
    {
        if (empty($value) || !is_array($value)) {
            $result = [];
            foreach (Cache::get('web_site') as $site) {
                $result[$site['site_id']] = ['day_used' => 0, 'max_used' => 0, 'day' => -1, 'all' => -1];
            }
            return $result;
        }
        return $value;
    }

    public function getStatusTextAttr($value, $data)
    {
        $status = [-1 => '删除', 0 => '禁用', 1 => '正常', 2 => '待审核'];
        return $status[$data['status']];
    }

    public function getTypeTextAttr($value, $data)
    {
        $type = ['system' => '管理员', 'proxy' => '代理', 'member' => '会员'];
        return $type[$data['type']];
    }

    public function getSiteAccessTextAttr($value, $data)
    {
        global $_G;
        $summery = '日已用/日总数/已用/总次数<br>';
        foreach ($data['site_access'] as $site_id => $access) {
            if (empty($access) || empty($_G['web_site'][$site_id]) || $access['day'] < 0 || $access['all'] < 0) {
                continue;
            }
            $summery .= $_G['web_site'][$site_id]['title'] . '：';
            $summery .= '<strong class="text-danger">' . $access['day_used'] . '</strong> / <strong class="text-success">' . $access['day'] . '</strong>';
            $summery .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $summery .= '<strong class="text-danger">' . $access['max_used'] . '</strong> / <strong class="text-success">' . $access['all'] . '</strong>';
            $summery .= '<br>';
        }
        return $summery;
    }

    public function login($auto = true)
    {
        global $_G;
        if (empty($this->uid)) {
            return false;
        }
        $this->last_time = Request::time();
        $this->last_ip   = Request::ip();
        $this->save();
        if (isset($this->reset_times) && $this->reset_times < date('Ymd')) {
            $this->reset_times();
        }
        $info = [
            'uid'       => $this->uid,
            'password'  => md5($this->password),
            'last_time' => $this->last_time,
        ];
        Session::set('uid', $info);
        if ($auto) {
            Cookie::set('uid', authcode(base64_encode(json_encode($info)), 'ECODE'), 2592000);
        }
        $_G['member'] = $this;
        return true;
    }

    public function reset_times()
    {
        $site_access = [];
        foreach ($this->site_access as $site_id => $access) {
            $site_access[$site_id] = ['day_used' => '0', 'max_used' => $access['max_used'], 'day' => $access['day'], 'all' => $access['all']];
        }
        $this->reset_times = date('Ymd');
        $this->site_access = $site_access;
        $this->save();
    }

    public function logout()
    {
        global $_G;
        Session::delete('uid');
        Cookie::delete('uid');
        $_G['uid']    = 0;
        $_G['member'] = false;
        return true;

    }
}
