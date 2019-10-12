<?php
namespace app\common\job;

use app\common\model\Setting;
use app\common\model\WebSiteCookie;
use Jobs\queue\Job;

class Common
{

    public function fire(Job $job, $data)
    {
        $reset_cookie_times = Setting::where('key', '=', 'reset_cookie_times')->value('value');
        $day                = date('Ymd');
        if ($reset_cookie_times != $day) {
            Setting::where('key', '=', 'reset_cookie_times')->update(['value' => $day]);
            WebSiteCookie::where('used_times', '<>', 0)->update(['used_times' => 0]);
        }
    }

    public function failed($data)
    {
    }

}
