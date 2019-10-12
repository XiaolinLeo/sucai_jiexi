<?php
namespace app\common\model;

use think\facade\Cache;
use think\Model;

class Setting extends Model
{
    protected $pk = 'key';

    public function update_cache()
    {
        Cache::rm('setting');
        $setting = [];
        foreach (self::select() as $set) {
            $setting[$set['key']] = $set['value'];
        }
        Cache::set('setting', $setting);
    }
}
