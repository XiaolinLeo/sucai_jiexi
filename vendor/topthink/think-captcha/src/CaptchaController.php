<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

namespace think\captcha;
ob_clean();//宝塔6.8以上放弃gd库 改成Imagick高性能图形库
use think\facade\Config;

class CaptchaController
{
    public function index($id = "")
    {
        $captcha = new Captcha((array) Config::pull('captcha'));
        return $captcha->entry($id);
    }
}
