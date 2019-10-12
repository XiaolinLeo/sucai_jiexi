<?php
namespace app\common\model;

use think\facade\Cache;
use think\facade\Request;
use think\Model;

class Card extends Model
{
    protected $pk                 = 'card_id';
    protected $autoWriteTimestamp = true;
    protected $createTime         = 'create_time';
    protected $updateTime         = 'update_time';
    protected $insert             = ['create_ip'];
    protected $json               = ['access_times'];
    protected $jsonAssoc          = true;

    protected function setCreatedTimeAttr($value)
    {
        return Request::time();
    }
    protected function setCreateIpAttr($value = 0)
    {
        return Request::ip();
    }

    public function getStatusTextAttr($value, $data)
    {
        $status = [-1 => '过期', 0 => '已使用', 1 => '可用'];
        return $status[$data['status']];
    }

    public function getInfoAttr($value, $data)
    {
        $site   = Cache::get('web_site');
        $return = [];
        if (!empty($data['valid_time'])) {
            $return[] = '有效期+' . ($data['valid_time'] / 3600) . '小时';
        }
        if (!empty($data['account_times'])) {
            $return[] = '总次数+' . $data['account_times'] . '次';
        }
        $access_message = [];
        foreach ($data['access_times'] as $site_id => $access) {
            if ((!empty($access['day']) || !empty($access['all'])) && !empty($site[$site_id])) {
                $access_message[$site_id] = ['title' => $site[$site_id]['title'], 'day' => $access['day'], 'all' => $access['all']];
            }
        }
        if (!empty($access_message)) {
            $return[] = '<a class="show-access-times" href="javascript:;" data-access-times="' . htmlentities(json_encode($access_message)) . '">站点权限</a>';
        }
        return implode(' | ', $return);
    }

    public function useUser()
    {
        return $this->hasOne('Member', 'uid', 'use_uid');
    }

    public function createUser()
    {
        return $this->hasOne('Member', 'uid', 'create_uid');
    }
}
