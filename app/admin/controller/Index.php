<?php
namespace app\admin\controller;

use app\admin\controller\Base;
use app\common\model\HttpCurl;
use think\Db;
use think\facade\App;

class Index extends Base
{

    public function index()
    {
        global $_G;

        $version                   = Db::query('SELECT version() as ver');
        $this->view->mysql_version = $version[0]['ver'];

        $sql = "SHOW TABLE STATUS FROM " . config('database.database');
        if ($prefix = config('database.prefix')) {
            $sql .= " LIKE '{$prefix}%'";
        }
        $row  = Db::query($sql);
        $size = 0;
        foreach ($row as $value) {
            $size += $value['Data_length'] + $value['Index_length'];
        }
        $this->view->db_size = format_bytes($size);
        return $this->fetch();
    }

    public function check()
    {
        global $_G;
        $http_curl = new HttpCurl($this->upgradeApi(), 'POST', 'version=' . $_G['setting']['version'] . '&unique=' . request()->site_unique(), ['Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3'], true, 0);

        $check     = json_decode($http_curl->send_request()->get_response_body(), true);

        if ($check['version'] > $_G['setting']['version']) {
            $check['has_new'] = 1;
        } else {
            $check['has_new'] = 0;
        }

        return json($check);
    }

    private function upgradeApi(){
        return false;
    }
}
