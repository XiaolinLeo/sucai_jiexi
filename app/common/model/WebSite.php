<?php
namespace app\common\model;

use think\facade\Cache;
use think\Model;

class WebSite extends Model
{
    protected $pk = 'site_id';

    public static function init()
    {
        self::event('after_write', function () {
            Cache::rm('web_site');
            $web_site = [];
            foreach (self::select()->toArray() as $site) {
                $web_site[$site['site_id']] = $site;
            }
            Cache::set('web_site', $web_site);
        });
    }

    public function cookies()
    {
        return $this->hasMany('WebSiteCookie', 'site_id', 'site_id');
    }

    public function getStatusTextAttr($value, $data)
    {
        $status = [-1 => '删除', 0 => '禁用', 1 => '正常', 2 => '待审核'];
        return $status[$data['status']];
    }
}
