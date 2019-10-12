<?php
namespace app\admin\controller;

use app\common\model\Member;
use think\Controller;
use think\facade\Validate;
use think\captcha\Captcha;

class Account extends Controller
{
    public function index()
    {
        return $this->redirect('admin/account/login');
    }

    public function login()
    {
        global $_G;
        if (!empty($_G['member'])) {
            return $this->request->isPost() ? $this->success('登录成功！', 'admin/index/index', '', -1) : $this->redirect('admin/index/index');
        }
        if ($this->request->isPost()) {
            if (!$account = $this->request->post('account')) {
                return $this->error('请输入帐户名');
            }
            if (!$password = $this->request->post('password')) {
                return $this->error('请输入密码');
            }
                         if(request()->isPost()){
            $data = input('post.');
            if(!captcha_check($data['verifyCode'])) {
                // 校验失败
                $this->error('验证码不正确');
            }
            }
            if (Validate::isEmail($account)) {
                $type = 'email';
            } else if (Validate::isMobile($account)) {
                $type = 'mobile';
            } else {
                $type = 'username';
            }
            if (!$member = Member::where($type, '=', $account)->find()) {
                return $this->error('账号不存在！');
            }
            if ($member['status'] == -2) {
                return $this->error('用户已注销');
            }
            if ($member['status'] == -1) {
                return $this->error('用户已被禁用');
            }
            if (!password_verify(md5($password), $member['password'])) {
                return $this->error('密码错误！');
            }

            if ($member['type'] !== 'system') {
                return $this->error('权限不足！');
            }
            $member->login();
            return $this->success('登录成功！', 'admin/index/index', '', -1);
        }
        $this->view->engine->layout(false);
        return $this->fetch();
    }

    public function logout()
    {
        global $_G;
        $_G['member']->logout();
        return $this->redirect('admin/account/login');
    }
}
