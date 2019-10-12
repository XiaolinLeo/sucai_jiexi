<?php
namespace app\admin\controller;

use think\Controller;

class Base extends Controller
{
    protected function initialize()
    {
        global $_G;
        parent::initialize();
        if (empty($_G['uid'])) {
            return $this->redirect('admin/account/login');
        }
        if ($_G['member']['type'] !== 'system') {
            $_G['member']->logout();
            return $this->redirect('admin/account/login');
        }
    }
}
