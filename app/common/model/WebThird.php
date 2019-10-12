<?php
namespace app\common\model;

use think\facade\Cache;
use think\Model;

class WebThird extends Model
{
    protected $pk = 'third_id';

    public static function init()
    {
        self::event('after_write', function () {
            $web_third = [];
            foreach (self::select()->toArray() as $third) {
                $web_third[$third['third_id']] = $third;
            }
            Cache::set('web_third', $web_third);
        });
    }

    public function cookies()
    {
        return $this->hasMany('WebSiteCookie', 'third_id', 'third_id');
    }

    public function getStatusTextAttr($value, $data)
    {
        $status = [-1 => '删除', 0 => '禁用', 1 => '正常', 2 => '待审核'];
        return $status[$data['status']];
    }
}
