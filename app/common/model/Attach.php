<?php
namespace app\common\model;

use think\facade\Request;
use think\Model;
use think\model\concern\SoftDelete;

class Attach extends Model
{
    use SoftDelete;
    protected $pk                 = 'attach_id';
    protected $autoWriteTimestamp = true;
    protected $createTime         = 'create_time';
    protected $insert             = ['create_ip', 'update_time', 'update_ip', 'local_file'];
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

    protected function setUpdateTimeAttr($value)
    {
        return Request::time();
    }

    protected function setUpdateIpAttr($value)
    {
        return Request::ip();
    }

    protected function setLocalFileAttr($value)
    {
        return md5(request()->time() . random(10)) . '.temp';
    }

    public function getDownloadTimeAttr($value)
    {
        return round($value, 2) . 's';
    }

    public function getUploadTimeAttr($value)
    {
        return round($value, 2) . 's';
    }

    public function getStatusTextAttr($value, $data)
    {
        $status = [0 => '新建', 1 => '下载中', 2 => '已下载', 3 => '上传中', 4 => '完成'];
        return $status[$data['status']];
    }

    public function member()
    {
        return $this->hasOne('Member', 'uid');
    }
}
