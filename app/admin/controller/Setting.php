<?php

namespace app\admin\controller;

use app\common\model\Setting as CommonSetting;
use think\Exception;
use think\facade\Env;

class Setting extends Base
{
    public function index()
    {
        if ($this->request->isPost()) {
            foreach ($this->request->post('setting/a') as $key => $value) {
                $data = CommonSetting::where('key', '=', $key)->find();
                if($data){
                    CommonSetting::where('key', '=', $key)->update(['value' => $value]);
                }else{
                    CommonSetting::insert(['value' => $value,'key'=>$key]);
                }
            }
            $app_debug = $this->request->post('app_debug') == 1;
            if ($app_debug !== config('app.app_debug')) {
                $file = Env::get('config_path') . 'app.php';
                $config = file_get_contents($file);

                $config = preg_replace_callback('/[\'|\"]app_debug[\'|\"](.*?)=>(.*?)(false|true),/i', function ($matches) use ($app_debug) {
                    return "'app_debug'" . $matches['1'] . "=>" . $matches['2'] . ($app_debug == true ? 'true' : 'false') . ",";
                }, $config);
                $config = preg_replace_callback('/[\'|\"]app_trace[\'|\"](.*?)=>(.*?)(false|true),/i', function ($matches) use ($app_debug) {
                    return "'app_trace'" . $matches['1'] . "=>" . $matches['2'] . ($app_debug == true ? 'true' : 'false') . ",";
                }, $config);
                file_put_contents($file, $config);
            }

            (new CommonSetting)->update_cache();
            return $this->success('设置保存成功！');
        }
        return $this->fetch();
    }

    public function email()
    {
        if ($this->request->isPost()) {
            foreach ($this->request->post('setting/a') as $key => $value) {
                CommonSetting::where('key', '=', $key)->update(['value' => $value]);
            }
            (new CommonSetting)->update_cache();
            return $this->success('设置保存成功！');
        }
        return $this->fetch();
    }

    public function proxy()
    {
        if ($this->request->isPost()) {
            foreach ($this->request->post('setting/a') as $key => $value) {
                CommonSetting::where('key', '=', $key)->update(['value' => $value]);
            }
            (new CommonSetting)->update_cache();
            return $this->success('设置保存成功！');
        }
        return $this->fetch();
    }

    public function uploadFile()
    {
        try {
            if ((($_FILES["photo"]["type"] == "image/gif")
                    || ($_FILES["photo"]["type"] == "image/jpeg")
                    || ($_FILES["photo"]["type"] == "image/pjpeg")
                    || ($_FILES["photo"]["type"] == "image/png")
                )
                && ($_FILES["photo"]["size"] < 2582323)) {
                $lastUri = "/static/images/";
                $picName = $_FILES['photo']['name'];
                $newlocal = Env::get('root_path') . $lastUri;
                if (!is_dir($newlocal)) {
                    $newlocal = Env::get('root_path')."/public".$lastUri;
                }
                $newlocal = $newlocal . $picName ;
                move_uploaded_file($_FILES['photo']['tmp_name'], $newlocal);

                $data["url"] = $lastUri.$picName;
                return $data;
            } else {
                throw new Exception("格式错误,或图片不能超过2MB。");
            }
        } catch (Exception $e) {
            $data['error'] = $e->getMessage();
            return $data;
        }
    }
}
