<?php
namespace app\admin\controller;

use app\admin\controller\Base;
use app\common\model\Jobs;
use think\Db;

class Queue extends Base
{
    public function index()
    {
        $where                  = [];
        $this->view->queue_list = Jobs::where($where)->paginate(10);
        $this->view->page       = $this->view->queue_list->render();
        return $this->fetch();
    }

    public function create()
    {
        if ($this->request->isPost()) {
            $result = $this->validate($this->request->post(), [
                'title'   => 'require',
                'queue'   => 'require',
                'payload' => 'require',
            ], [
                'title'   => '用户名必填',
                'queue'   => '队列名必填，默认default',
                'payload' => '执行文件名必填',
            ]);
            if ($result !== true) {
                return $this->error($result);
            }
            Jobs::create($this->request->post());
            return $this->success('任务添加成功');
        }
        return $this->fetch();
    }

    public function edit($id = 0)
    {
        $job = Jobs::where('id', '=', $id)->find();
        if (empty($job)) {
            return $this->error('指定数据不存在');
        }
        if ($this->request->isPost()) {
            $result = $this->validate($this->request->post(), [
                'title'   => 'require',
                'queue'   => 'require',
                'payload' => 'require',
            ], [
                'title'   => '用户名必填',
                'queue'   => '队列名必填，默认default',
                'payload' => '执行文件名必填',
            ]);
            if ($result !== true) {
                return $this->error($result);
            }
            $job->allowField(true)->save($this->request->post(), ['id' => $job['id']]);
            return $this->success('任务数据编辑成功！');
        }
        $this->view->job = $job;
        return $this->fetch();
    }

    public function reset()
    {
        $sql = <<<EOT
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`  (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `title` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
    `queue` varchar(255) NOT NULL DEFAULT '' COMMENT '类型',
    `payload` longtext NOT NULL COMMENT '执行任务文件',
    `attempts` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '已执行次数',
    `reserved` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '重发次数',
    `reserved_at` int(11) unsigned NULL DEFAULT NULL COMMENT '上次执行时间',
    `available_at` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '下次执行时间',
    `created_at` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
    `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='队列';

INSERT INTO `jobs` VALUES
(1, '下载文件', 'download', '{\"job\":\"common\\/Download\",\"data\":\"\"}', 0, 1, 1549970379, 1549940197, 1549940196, 1),
(2, '上传文件', 'upload', '{\"job\":\"common\\/Upload\",\"data\":\"\"}', 0, 1, 1549970379, 1549940197, 1549940196, 1),
(3, '刷新Cookie', 'cookie', '{\"job\":\"common\\/RefreshCookie\",\"data\":\"\"}', 0, 1, 1549970379, 1549940197, 1549940196, 1),
(4, '刷新下载次数', 'default', '{\"job\":\"common\\/ResetTimes\",\"data\":\"\"}', 0, 1, 1549970379, 1549940197, 1549940196, 1),
(5, '系统任务', 'default', '{\"job\":\"common\\/Common\",\"data\":\"\"}', 0, 1, 1549970379, 1549940197, 1549940196, 1);
EOT;
        $this->run_sql($sql);
        return $this->success('任务重置成功！');
    }

    public function delete($id = 0)
    {
        if (!$this->request->isAjax()) {
            return $this->error('请求类型错误');
        }
        $job = Jobs::where('id', '=', $id)->find();
        if (empty($job)) {
            return $this->error('指定数据不存在');
        }
        $job->delete();
        return $this->success('任务删除成功');
    }

    private function run_sql($sql_code = '')
    {
        if (empty($sql_code)) {
            return false;
        }
        $sql_code = $this->_split_sql(str_replace(
            [' {prefix}', ' prefix_', ' `prefix_'],
            [' ' . config('database.prefix'), ' ' . config('database.prefix'), ' `' . config('database.prefix')],
            $sql_code));
        foreach ($sql_code as $sql) {
            $result = Db::execute($sql);
        }
    }

    private function _split_sql($sql)
    {
        $sql          = str_replace([PHP_EOL, "\r"], "\n", $sql);
        $ret          = [];
        $num          = 0;
        $queriesarray = explode(";\n", trim($sql));
        unset($sql);
        foreach ($queriesarray as $query) {
            $queries   = explode("\n", trim($query));
            $ret[$num] = '';
            foreach ($queries as $query) {
                $ret[$num] .= substr($query, 0, 1) == "#" ? null : $query;
            }
            $num++;
        }
        return ($ret);
    }
}
