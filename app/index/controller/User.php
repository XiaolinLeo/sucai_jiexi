<?php
namespace app\index\controller;

use app\common\model\Card;
use app\common\model\Member;
use app\common\model\MemberLog;
use app\common\model\WebSite;
use think\Controller;
use think\facade\Cache;

class User extends Controller
{

    protected function initialize()
    {
        global $_G;
        if (empty($_G['uid'])) {
            return $this->redirect('index/account/login');
        }
    }

    public function index()
    {
        $this->view->site_list = WebSite::where('status', '>', 0)->select();
        return $this->fetch();
    }

    public function recharge($card_id = '')
    {
        global $_G;
        if ($this->request->isPost()) {
            if (empty($card_id) || !$card = Card::where('card_id', '=', $card_id)->find()) {
                return $this->error('充值卡不存在，请检查');
            }
            if ($card['status'] !== 1 || !empty($card['use_uid'])) {
                return $this->error('充值卡已失效！');
            }
            $update = [
                'site_access' => $_G['member']['site_access'],
            ];
            $message = '充值成功';
            if ($card['valid_time'] > 0) {
                if ($_G['member']['out_time'] >= $this->request->time()) {
                    $update['out_time'] = $_G['member']['out_time'] + $card['valid_time'];
                } else if ($_G['member']['out_time'] > 0) {
                    $update['out_time'] = $this->request->time() + $card['valid_time'];
                }
            } else if ($card['valid_time'] == -1) {
                $update['out_time'] = 0;
            }
            if ($card['account_times'] > 0) {
                if ($_G['member']['parse_max_times'] == -1) {
                    $update['parse_max_times'] = $card['account_times'] + $_G['member']['parse_max_times'] + 1;
                } else if ($_G['member']['parse_max_times'] > 0) {
                    $update['parse_max_times'] = $card['account_times'] + $_G['member']['parse_max_times'];
                }
            } else if ($card['account_times'] == -1) {
                $update['parse_max_times'] = 0;
            }
            foreach ($card['access_times'] as $site_id => $access) {
                if (empty($update['site_access'][$site_id])) {
                    $update['site_access'][$site_id] = ['day_used' => 0, 'max_used' => 0, 'day' => -1, 'all' => -1];
                }

                if ($access['day'] > 0) {
                    if ($update['site_access'][$site_id]['day'] == -1) {
                        $update['site_access'][$site_id]['day'] = $access['day'] + $update['site_access'][$site_id]['day'] + 1;
                    } else if ($update['site_access'][$site_id]['day'] > 0) {
                        $update['site_access'][$site_id]['day'] = $access['day'] + $update['site_access'][$site_id]['day'];
                    }
                } else if ($access['day'] == -1) {
                    $update['site_access'][$site_id]['day'] = 0;
                }

                if ($access['all'] > 0) {
                    if ($update['site_access'][$site_id]['all'] == -1) {
                        $update['site_access'][$site_id]['all'] = $access['all'] + $update['site_access'][$site_id]['all'] + 1;
                    } else if ($update['site_access'][$site_id]['all'] > 0) {
                        $update['site_access'][$site_id]['all'] = $access['all'] + $update['site_access'][$site_id]['all'];
                    }
                } else if ($access['all'] == -1) {
                    $update['site_access'][$site_id]['all'] = 0;
                }
            }
            (new Member)->save($update, ['uid' => $_G['uid']]);
            $card->use_uid  = $_G['uid'];
            $card->use_time = $this->request->time();
            $card->use_ip   = $this->request->ip();
            $card->status   = 0;
            $card->save();
            return $this->success($message);
        }
        return $this->fetch();
    }

    public function proxy($type = '')
    {
        global $_G;
        if ($_G['member']['type'] !== 'proxy' && $_G['member']['type'] !== 'system') {
            return $this->error('你无法使用此功能');
        }
        if ($type == 'create_card') {
            return $this->create_card();
        }
        $this->view->card_list = Card::where('create_uid', '=', $_G['uid'])->order('create_time desc')->paginate(20);
        $this->view->page      = $this->view->card_list->render();
        return $this->fetch();
    }

    private function create_card()
    {
        global $_G;
        $count = Card::where('create_uid', '=', $_G['uid'])->count('card_id');
        if ($this->request->isPost()) {
            if ($count >= $_G['setting']['proxy_card_numbers']) {
                return $this->error('你可生成的卡密张数已达上限');
            }
            if (empty($_G['setting']['proxy_card_rule'])) {
                return $this->error('请联系管理员配置卡密生成规则');
            }

            $post = $this->request->post();
            if ($count + intval($post['numbers']) > $_G['setting']['proxy_card_numbers']) {
                return $this->error('您的账户最多还能生成' . ($_G['setting']['proxy_card_numbers'] - $count - intval($post['numbers'])) . '张卡密');
            }
            if (intval($post['numbers']) <= 0) {
                return $this->error('请输入正确的卡密张数');
            }

            $card_id = [];
            for ($i = 0; $i < $post['numbers']; $i++) {
                $card_id[$i] = replace_random_str($_G['setting']['proxy_card_rule']);
            }
            foreach (Card::where('card_id', 'in', $card_id)->field('card_id')->select() as $old) {
                $k = array_search($old['card_id'], $card_id);
                unset($card_id[$k]);
            }
            foreach ($card_id as $k => $name) {
                Card::create([
                    'card_id'       => $name,
                    'create_uid'    => $_G['uid'],
                    'account_times' => $post['account_times'] > $_G['setting']['proxy_card_account_times'] ? $_G['setting']['proxy_card_account_times'] : $post['account_times'],
                    'valid_time'    => $post['valid_time'] == -1 ? -1 : $post['valid_time'] * 3600,
                    'access_times'  => $post['access_times'] ?: [],
                    'from'          => 'proxy_create',
                    'status'        => 1,
                ]);
            }

            return $this->success('本次成功生成' . count($card_id) . '张卡密！', 'index/user/proxy');
        }
        $this->view->count     = $count;
        $this->view->site_list = Cache::get('web_site');
        return $this->fetch('proxy');
    }

    public function download()
    {
        global $_G;
        $where = [
            ['uid', '=', $_G['member']['uid']],
        ];
        $this->view->log_list = MemberLog::where($where)->limit(30)->select();
        return $this->fetch();
    }

    public function profile()
    {
        global $_G;
        if ($this->request->isPost()) {
            if (!empty($this->request->post('email')) && Member::where([
                ['email', '=', $this->request->post('email')],
                ['uid', '<>', $_G['uid']],
            ])->find()) {
                return $this->error('邮箱已被绑定，请更换');
            }
            if (!empty($this->request->post('mobile')) && Member::where([
                ['mobile', '=', $this->request->post('mobile')],
                ['uid', '<>', $_G['uid']],
            ])->find()) {
                return $this->error('手机号已被绑定，请更换');
            }
            $_G['member']->email  = $this->request->post('email');
            $_G['member']->mobile = $this->request->post('mobile');
            $_G['member']->save();
            return $this->success('资料修改成功！');
        }
        return $this->fetch();
    }

    public function password()
    {
        global $_G;
        if ($this->request->isPost()) {
            if (!$this->request->post('password')) {
                return $this->error('请输入新密码');
            }
            if ($this->request->post('password') != $this->request->post('password_confirm')) {
                return $this->error('两次新密码输入不相同');
            }
            if (!password_verify(md5($this->request->post('oldpassword')), $_G['member']['password'])) {
                return $this->error('当前密码输入错误');
            }
            $_G['member']->password = $this->request->post('password');
            $_G['member']->save();
            return $this->success('密码修改成功！');
        }
        return $this->fetch();
    }
}
