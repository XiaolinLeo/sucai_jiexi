<?php
namespace app\common\model;

use think\Model;

class WebSiteCookie extends Model
{
    protected $pk = 'cookie_id';

    public static function init()
    {
    }

    public function getStatusTextAttr($value, $data)
    {
        $status = [-1 => '删除', 0 => '禁用', 1 => '正常'];
        return $status[$data['status']];
    }
}
