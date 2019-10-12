<?php
namespace app\admin\controller;

use app\admin\controller\Base;
use app\common\model\Member;
use app\common\model\WebSite;
use think\Db;
use think\facade\Validate;

class User extends Base
{
    public function index()
    {
        $where                 = [];
        $this->view->user_list = Member::where($where)->order('uid desc')->paginate(30);
        $this->view->page      = $this->view->user_list->render();
        return $this->fetch();
    }

    public function create()
    {
        if ($this->request->isPost()) {
            $post   = $this->request->post();
            $result = $this->validate($post, [
                'username' => 'require|unique:member',
                'password' => 'require',
                'email'    => 'unique:member',
                'mobile'   => 'unique:member',
                'out_time' => 'require|integer|>=:0|<=:87600',
            ], [
                'username.require' => '用户名必填',
                'username.unique'  => '用户名已存在',
                'password.require' => '密码必填',
                'email'            => '邮箱已存在',
                'mobile'           => '手机号已存在',
                'out_time'         => '账户有效期填写错误',
            ]);
            if ($result !== true) {
                return $this->error($result);
            }
            $post['out_time'] = $post['out_time'] * 3600;
            Member::create($post);
            return $this->success('会员添加成功');
        }

        $this->view->site_list = WebSite::select();
        return $this->fetch();
    }

    public function edit($uid = 0)
    {
        $user = Member::where('uid', '=', $uid)->find();
        if (empty($user)) {
            return $this->error('指定数据不存在');
        }
        if ($this->request->isPost()) {
            $post = $this->request->post();
            if (empty($post['password'])) {
                unset($post['password']);
            }
            $result = $this->validate($post, [
                'username' => 'require|unique:member',
                'email'    => 'unique:member',
                'mobile'   => 'unique:member',
            ], [
                'username.require' => '用户名必填',
                'username.unique'  => '用户名已存在',
                'email'            => '邮箱已存在',
                'mobile'           => '手机号已存在',
            ]);
            if ($result !== true) {
                return $this->error($result);
            }
            if (!Validate::isNumber($post['out_time'])) {
                $post['out_time'] = strtotime($post['out_time']);
            } else {
                $post['out_time'] = $post['out_time'] * 3600;
            }
            $user->allowField(true)->save($post, ['uid' => $user['uid']]);
            return $this->success('会员数据编辑成功！');
        }

        $this->view->site_list = WebSite::select();
        $this->view->user      = $user;
        return $this->fetch();
    }

    public function batch_add()
    {
        if ($this->request->isPost()) {
            $post   = $this->request->post();
            $result = $this->validate($post, [
                'username' => 'require',
                'password' => 'require',
                'numbers'  => 'require|integer|>:0|<=:200',
                'out_time' => 'require|integer|>=:0|<=:87600',
            ], [
                'username.require' => '用户名必填',
                'username.unique'  => '用户名已存在',
                'password.require' => '密码必填',
                'numbers'          => '生成数量只能是1-200之间的整数',
                'out_time'         => '账户有效期填写错误',
            ]);
            if ($result !== true) {
                return $this->error($result);
            }
            $username = $password = [];
            for ($i = 0; $i < $post['numbers']; $i++) {
                $username[$i] = replace_random_str($post['username']);
                $password[$i] = replace_random_str($post['password']);
            }
            foreach (Member::where('username', 'in', $username)->field('uid,username')->select() as $old) {
                $k = array_search($old['username'], $username);
                unset($username[$k]);
            }
            foreach ($username as $k => $name) {
                Member::create([
                    'username'        => $name,
                    'password'        => strtolower($password[$k]),
                    'password_see'    => strtolower($password[$k]),
                    'out_time'        => $post['out_time'] * 3600,
                    'site_access'     => $post['site_access'],
                    'from'            => 'batch_add',
                    'parse_max_times' => (int) $post['parse_max_times'],
                ]);
            }

            return $this->success('本次成功生成' . count($username) . '个账号！', 'admin/user/index');
        }

        $this->view->site_list = WebSite::select();
        return $this->fetch();
    }

    public function export($export = null)
    {
        $field_list = Db::query("SHOW FULL COLUMNS FROM `member`");
        if ($export === 'yes') {
            $expCellName = [];
            foreach ($field_list as $field) {
                if (in_array($field['Field'], $this->request->param('fields/a', []))) {
                    $expCellName[] = [$field['Field'], $field['Comment']];
                }
            }
            if (empty($expCellName)) {
                return $this->error('未选中任何字段');
            }
            $where = [];
            if (!empty($this->request->param('start_uid/d'))) {
                $where[] = ['uid', '>=', $this->request->param('start_uid/d')];
            }
            if (!empty($this->request->param('end_uid/d'))) {
                $where[] = ['uid', '<=', $this->request->param('end_uid/d')];
            }
            if (!empty($this->request->param('start_time', ''))) {
                $where[] = ['register_time', '>=', $this->request->param('start_time', '')];
            }
            if (!empty($this->request->param('end_time', ''))) {
                $where[] = ['register_time', '<=', $this->request->param('end_time', '')];
            }
            if ($this->request->param('user_type', 'all') != 'all') {
                $where[] = ['type', '=', $this->request->param('user_type/s')];
            }
            $expTableData = Member::where($where)->select();
            $cellNum      = count($expCellName);
            $dataNum      = count($expTableData);
            $objPHPExcel  = new \PHPExcel();
            $cellName     = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'];
            for ($i = 0; $i < count($expCellName); $i++) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($cellName[$i])->setWidth(15);
            }
            $objPHPExcel->getActiveSheet(0)->mergeCells('A1:' . $cellName[$cellNum - 1] . '1');
            $objPHPExcel->getActiveSheet()->setCellValue('A1', '会员数据')->getStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            for ($i = 0; $i < $cellNum; $i++) {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '2', $expCellName[$i][1]);
            }
            for ($i = 0; $i < $dataNum; $i++) {
                for ($j = 0; $j < $cellNum; $j++) {
                    $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 3), $expTableData[$i][$expCellName[$j][0]]);
                }
            }
            $filename = $this->request->param('filename/s', '会员数据', 'strip_tags,trim');
            $filename = $filename ? $filename : '会员数据';
            ob_end_clean();
            header('pragma:public');
            header('Content-type:application/octet-stream;charset=utf-8;name="' . $filename . '.xls"');
            header("Content-Disposition:attachment;filename=" . $filename . ".xls");
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        }
        if ($this->request->isPost()) {
            return $this->success('正在导出数据', url('admin/user/export', [
                'export'     => 'yes',
                'fields'     => $this->request->post('fields/a'),
                'start_time' => strtotime($this->request->post('start_time/s')),
                'end_time'   => strtotime($this->request->post('end_time/s')),
                'start_uid'  => $this->request->post('start_uid/d', 0),
                'end_uid'    => $this->request->post('end_uid/d', 0),
                'user_type'  => $this->request->post('user_type/s', 'all'),
                'filename'   => $this->request->post('filename/s'),
            ]));
        }
        $this->view->field_list = $field_list;
        return $this->fetch();
    }

    public function delete($uid = 0)
    {
        if (!$this->request->isAjax()) {
            return $this->error('请求类型错误');
        }
        if (in_array($uid, config('app.founder'))) {
            return $this->error('超级管理员无法删除，请先从配置文件中移除该管理员');
        }
        $user = Member::where('uid', '=', $uid)->find();
        if (empty($user)) {
            return $this->error('指定数据不存在');
        }
        $user->delete();
        return $this->success('会员删除成功');
    }

    public function delete_all()
    {
        if (!$this->request->isAjax()) {
            return $this->error('请求类型错误');
        }
        Member::where('uid', 'not in', config('app.founder'))->delete(true);
        return $this->success('除管理员外的所有用户已清空');
    }

    public function delete_out_user()
    {
        if (!$this->request->isAjax()) {
            return $this->error('请求类型错误');
        }
        Member::where('out_time', '>', 315360000)->where('out_time', '<', $this->request->time())->where('uid', 'not in', config('app.founder'))->delete(true);
        return $this->success('过期会员清除成功！');
    }
}
