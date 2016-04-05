<?php

namespace App\Http\Controllers;

class LoginController extends Controller
{
    protected $_authlevel = 0;

    public function loginAction()
    {
        if ($this->isPost()) {
            if (request('account') != 'admin' || request('password') != 'molbase2016') {
                $this->error('账号密码错误！');
            }

            $_SESSION['user'] = ['username' => '管理员'];

            $this->redirect(\Func::U('index', 'index'));
        } else {
            $this->display();
        }
    }
}
