<?php
namespace app\admin\controller;

use app\admin\controller\Base;
use app\common\model\MemberLog;
use think\Db;

class Log extends Base
{
    public function index()
    {
        $where                = [];
        $this->view->log_list = MemberLog::where($where)->paginate(10);
        $this->view->page     = $this->view->log_list->render();
        return $this->fetch();
    }

    public function delete($log_id = 0)
    {
        if (!$this->request->isAjax()) {
            return $this->error('请求类型错误');
        }
        $attach = MemberLog::where('log_id', '=', $log_id)->find();
        if (empty($attach)) {
            return $this->error('指定数据不存在');
        }
        $attach->delete();
        return $this->success('记录删除成功');
    }

    public function delete_all()
    {
        if (!$this->request->isAjax()) {
            return $this->error('请求类型错误');
        }
        Db::query("DELETE FROM member_log;");
        return $this->success('数据已清空');
    }
}
