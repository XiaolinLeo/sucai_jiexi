<?php
namespace app\admin\controller;

use app\admin\controller\Base;
use app\common\model\HttpCurl;
use app\common\model\Setting;
use app\common\model\WebSite;
use think\Db;
use think\facade\App;
use think\facade\Cache;
use think\facade\Env;

class Tools extends Base
{

    public function index()
    {

        Cache::rm('setting');
        $setting = [];
        foreach (Setting::select() as $set) {
            $setting[$set['key']] = $set['value'];
        }
        Cache::set('setting', $setting);

        Cache::rm('web_site');
        $web_site = [];
        foreach (WebSite::select() as $site) {
            $web_site[$site['site_id']] = $site;
        }
        Cache::set('web_site', $web_site);
        return $this->success('缓存更新成功！', url('admin/index/index'));
    }

    public function check_version()
    {
        global $_G;
        $http_curl = new HttpCurl($this->upgradeApi(), 'POST', 'version=' . $_G['setting']['version'] . '&unique=' . request()->site_unique(), ['Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3'], true, 0);
        $check     = json_decode($http_curl->send_request()->get_response_body(), true);

        if ($check['version'] > $_G['setting']['version']) {
            return $this->success('发现新版本：' . $check['version'] . '，正在为你升级新版本，请勿关闭浏览器', url('admin/tools/upgrade', ['upgrade_url' => $check['upgrade_url']]));
        }
        return $this->success('当前版本已是最新版', url('admin/index/index'));
    }

    public function upgrade($upgrade_url = '')
    {
        global $_G;
        if (empty($upgrade_url)) {
            return $this->error('缺少升级参数，请从首页进入升级页面!', url('admin/index/index'));
        }
        if (!class_exists('ZipArchive')) {
            return $this->error('你的服务器尚未安装ZipArchive扩展，无法使用在线更新服务！');
        }
        $http_curl = new HttpCurl($upgrade_url, 'GET', 'version=' . $_G['setting']['version'] . '&unique=' . request()->site_unique(), ['Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3'], true, 0);
        $response  = $http_curl->send_request();

        $upgrade_file = Env::get('runtime_path') . 'upgrade.zip';
        $upgrade_dir  = Env::get('runtime_path') . 'upgrade';

        $fp = fopen($upgrade_file, 'w');
        fwrite($fp, $response->get_response_body());
        fclose($fp);
        $zip = new \ZipArchive;
        if ($zip->open($upgrade_file) !== true) {
            return $this->error('升级失败，请联系开发者！');
        }
        $zip->extractTo($upgrade_dir);
        $zip->close();
        @unlink($upgrade_file);
        $this->upgrade_file($upgrade_dir, strlen($upgrade_dir));
        delete_dir($upgrade_dir);
        return $this->success('文件更新成功，正在更新数据库...', url('admin/tools/upgrade_db'));
    }

    private function upgrade_file($dir = '', $length = 0)
    {
        if (!is_dir($dir)) {
            return true;
        }

        foreach (scandir($dir) as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            $file = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_file($file)) {
                $new = Env::get('root_path') . substr($file, $length + 1);
                if (is_file($new)) {
                    unlink($new);
                }
                rename($file, $new);
            } else if (is_dir($file)) {
                $this->upgrade_file($file, $length);
            }
        }
    }

    public function upgrade_db()
    {
        if (empty(config('app.authkey'))) {
            $file   = Env::get('config_path') . 'app.php';
            $config = file_get_contents($file);

            $config = preg_replace_callback('/(.*?)[\'|\"]exception_handle[\'|\"](.*?)=>(.*?)\'\',/i', function ($matches) {
                return "{$matches[1]}'exception_handle'{$matches[2]}=>{$matches[3]}''," . PHP_EOL .
                "{$matches[1]}'authkey'{$matches[2]}=>{$matches[3]}'" . random(32) . "',";
            }, $config);
            file_put_contents($file, $config);
        }
        $sql = '';
        if (!empty($sql)) {
            $result = Db::execute($sql);
        }
        return $this->success('程序升级成功，正在清理缓存', url('admin/tools/index'));
    }
}
