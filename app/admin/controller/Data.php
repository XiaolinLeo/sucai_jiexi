<?php
namespace app\admin\controller;

use app\admin\controller\Base;
use app\common\model\Attach;

class Data extends Base
{
    public function index()
    {
        $where                   = [];
        $this->view->attach_list = Attach::where($where)->paginate(10);
        $this->view->page        = $this->view->attach_list->render();
        return $this->fetch();
    }

    public function delete($attach_id = 0)
    {
        if (!$this->request->isAjax()) {
            return $this->error('请求类型错误');
        }
        $attach = Attach::where('attach_id', '=', $attach_id)->find();
        if (empty($attach)) {
            return $this->error('指定数据不存在');
        }
        if (is_file($attach->local_file)) {
            @unlink($attach->local_file);
        }
        $attach->delete();
        return $this->success('附件删除成功');
    }

    public function delete_all()
    {
        if (!$this->request->isAjax()) {
            return $this->error('请求类型错误');
        }
        Attach::chunk(100, function ($attach_list) {
            foreach ($attach_list as $attach) {
                if (is_file($attach->local_file)) {
                    @unlink($attach->local_file);
                }
                $attach->delete();
            }
        });
        return $this->success('附件及其记录批量删除成功！');
    }
}
