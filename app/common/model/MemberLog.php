<?php
namespace app\common\model;

use think\facade\Request;
use think\Model;
use think\model\concern\SoftDelete;

class MemberLog extends Model
{
    use SoftDelete;
    protected $pk                 = 'log_id';
    protected $autoWriteTimestamp = true;
    protected $createTime         = 'create_time';
    protected $updateTime         = false;
    protected $insert             = ['create_ip'];
    protected $deleteTime         = 'delete_time';
    protected $defaultSoftDelete  = 0;

    public static function init()
    {
    }

    protected function setCreateTimeAttr($value)
    {
        return Request::time();
    }

    protected function setCreateIpAttr($value)
    {
        return Request::ip();
    }

    public function getStatusTextAttr($value, $data)
    {
        $status = [0 => '失败', 1 => '成功'];
        return $status[$data['status']];
    }

    public function member()
    {
        return $this->hasOne('Member', 'uid', 'uid');
    }

    public function website()
    {
        return $this->hasOne('WebSite', 'site_id', 'site_id');
    }
}
