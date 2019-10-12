<?php
namespace app\common\model;

use think\facade\Request;
use think\Model;

class Jobs extends Model
{
    protected $pk                 = 'id';
    protected $json               = ['payload'];
    protected $autoWriteTimestamp = true;
    protected $createTime         = 'created_at';
    protected $insert             = ['available_at'];

    protected function setCreatedAtAttr($value)
    {
        return Request::time();
    }
    protected function setAvailableAtAttr($value = 0)
    {
        return Request::time() + 10;
    }

    public function getStatusTextAttr($value, $data)
    {
        $status = [-1 => '删除', 0 => '禁用', 1 => '正常', 2 => '待审核'];
        return $status[$data['status']];
    }
}
