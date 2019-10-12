<?php
namespace app\index\controller;

use app\common\model\Member;
use app\common\model\VerifyCode;
use app\common\model\WebSite;
use PHPMailer\PHPMailer\PHPMailer;
use think\Controller;
use think\facade\Validate;
use think\captcha\Captcha;


class Account extends Controller
{

    protected function initialize()
    {
        $this->view->engine->layout(false);
    }

    public function index()
    {
        return $this->redirect('index/account/login');
    }

    public function register()
    {
        global $_G;
        if (!empty($_G['member'])) {
            return $this->request->isPost() ? $this->success('您已登录，请退出后再操作', 'index/index/index') : $this->redirect('index/index/index');
        }
        if (!$_G['setting']['allow_register']) {
            $this->view->engine->layout(true);
            return $this->success('目前禁止自助注册新用户，请联系管理员');
        }
        if ($this->request->isPost()) {
            $site_access = [];
            foreach (WebSite::where('status', '=', 1)->select() as $site) {
                $site_access[$site['site_id']] = [
                    'day_used' => 0,
                    'max_used' => 0,
                    'day'      => 1,
                    'all'      => 1,
                ];
            }
            $post = [
                'username'         => $this->request->post('username/s', null),
                'password'         => $this->request->post('password/s', null),
                'password_confirm' => $this->request->post('password_confirm/s', null),
                'from'             => 'register',
                'type'             => 'member',
                'parse_max_times'  => count($site_access),
                'out_time'         => $this->request->time() + 3600,
                'site_access'      => $site_access,
            ];
            $result = $this->validate($post, [
                'username' => 'require|chsDash|unique:member',
                'password' => 'require|confirm',
            ], [
                'username.require' => '用户名必填',
                'username.chsDash' => '用户名只能是汉字、字母、数字和下划线_及破折号-',
                'username.unique'  => '用户名已存在',
                'password.require' => '密码必填',
                'password.confirm' => '两次输入密码不相同',
            ]);
            if ($result !== true) {
                return $this->error($result);
            }

            Member::create($post)->login();

            return $this->success('账户注册成功！', 'index/index/index');
        }
        return $this->fetch();
    }




    public function login()
    {
        global $_G;
        if (!empty($_G['member'])) {
            return $this->request->isPost() ? $this->success('您已登录，请退出后再操作', 'index/index/index') : $this->redirect('index/index/index');
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
            if (!$member = Member::where('username', '=', $account)->find()){
                if (!$member = Member::where($type, '=', $account)->find()) {
                    return $this->error('账号不存在！');
                }
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
            $update_data = [];
            if ($member['out_time'] > 0 && $member['out_time'] <= 315360000) {
                $update_data['out_time'] = $this->request->time() + $member['out_time'];

            }
            if ($member['password_see']) {
                $update_data['password_see'] = '';
            }
            if ($update_data) {
                foreach ($update_data as $key => $value) {
                    $member->$key = $value;
                }
                $member->save();
            }
            $member->login();
            return $this->success('登录成功！', 'index/index/index', '', -1);
        }
        return $this->fetch();
    }

    public function get_password($email = '', $verify_code = '')
    {
        global $_G;
        if ($this->request->isPost()) {
            $verify = VerifyCode::where('email', '=', $email)->find();
            if (!$verify) {
                return $this->error('未找到相关验证记录');
            }
            if ($verify['code'] != $verify_code || $verify['out_time'] < $this->request->time()) {
                return $this->error('验证码错误或已过期');
            }
            cookie('reset_password', authcode(json_encode(['uid' => $verify->uid]), 'ECODE', '', 3600));
            $verify->delete();
            return $this->success('验证成功，请重置密码', url('index/account/reset_password'), '', -1);
        }
        if (empty($_G['setting']['email_host']) || empty($_G['setting']['email_port']) || empty($_G['setting']['email_username']) || empty($_G['setting']['email_password'])) {
            $this->view->engine->layout(true);
            return $this->error('抱歉，我们暂未开启找回密码功能', url('index/account/login'));
        }
        return $this->fetch();
    }

    public function reset_password($password = '', $password2 = '')
    {
        $token = json_decode(authcode(cookie('reset_password')), true);
        if (empty($token['uid'])) {
            return $this->redirect('index/account/get_password');
        }
        if ($this->request->isPost()) {
            if ($password !== $password2) {
                return $this->error('两次输入密码不相同');
            }
            cookie('reset_password', null);
            $member = Member::where('uid', '=', $token['uid'])->find();
            if (!$member) {
                return $this->error('未找到相关用户');
            }
            $member->password = $password;
            $member->save();
            return $this->success('密码重置成功', url('index/account/login'));
        }
        return $this->fetch();
    }

    public function get_verify_code($email = '')
    {
        global $_G;
        if (!$this->request->isAjax() || !$this->request->isPost()) {
            return $this->error('请求类型错误');
        }
        if (empty($_G['setting']['email_host']) || empty($_G['setting']['email_port']) || empty($_G['setting']['email_username']) || empty($_G['setting']['email_password'])) {
            return $this->error('抱歉，我们暂未开启找回密码功能');
        }
        if (!Validate::isEmail($email)) {
            return $this->error('请输入正确的邮箱地址');
        }
        $member = Member::where('email', '=', $email)->find();
        if (!$member) {
            return $this->error('未找到相关账号，请确认邮箱是否输入正确');
        }
        VerifyCode::where('email', '=', $email)->delete();
        $verify = VerifyCode::create([
            'uid'      => $member->uid,
            'email'    => $member->email,
            'code'     => random(6, 1),
            'out_time' => $this->request->time() + 3600,
        ]);
        $mail            = new PHPMailer;
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->isHTML(true);
        $mail->SMTPSecure = 'ssl';
        $mail->Host       = $_G['setting']['email_host'];
        $mail->Port       = $_G['setting']['email_port'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_G['setting']['email_username'];
        $mail->Password   = $_G['setting']['email_password'];
        $mail->From       = $_G['setting']['email_username'];
        $mail->FromName   = $_G['setting']['email_fromname'] ?? $_G['setting']['site_name'];
        $mail->addAddress($member->email);
        $mail->Subject = '您正在找回网站密码';
        $mail->Body    = '您的验证码为：' . $verify->code;
        $mail->AltBody = '您的验证码为：' . $verify->code;
        $mail->send();
        return $this->success('已向该邮箱发送验证码，请输入验证码后进入下一步');
    }

    public function logout()
    {
        global $_G;
        $_G['member']->logout();
        return $this->redirect('index/account/login');
    }
}
