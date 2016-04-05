<?php

namespace App\Http\Controllers;

class LoginController extends Controller
{
    protected $_authlevel = 0;

    public function __construct()
    {
        parent::__construct();

        $this->model = \Login::m();
    }

    /**
     * 手机登录
     */
    public function login()
    {
        $username = request('username');
        $password = request('password');

        $ret = $this->model->mobileLogin($username, $password);
        if ($ret) {
            $this->success(lang(20001), $ret);
        } else {
            $this->error(lang($this->model->errorCode()), $this->model->errorCode());
        }
    }

    /**
     * 摩贝账号登录
     */
    public function molbaseLogin()
    {
        $username = request('username');
        $password = request('password');

        $ret = $this->model->molbaseLogin($username, $password);
        if ($ret) {
            $this->success(lang(20001), $ret);
        } else {
            $this->error(lang($this->model->errorCode()), $this->model->errorCode(), ['mobile' => $this->model->errorMessage()]);
        }
    }

    /**
     * 验证码登录
     */
    public function codeLogin()
    {
        $username = request('mobile');
        $code = request('code');

        $ret = $this->model->codeLogin($username, $code);
        if ($ret) {
            $this->success(lang(20001), $ret);
        } else {
            $this->error(lang($this->model->errorCode()), $this->model->errorCode());
        }
    }

    /**
     * 获取验证码
     */
    public function getCode()
    {
        $mobile = request('mobile');
        $mobileVerify = \MobileVerify::m();
        $userModel = \User::m();
        $type = request('type');//1 注册 2验证码登录 3找回密码
        if ($type == 1) {
            if ($userModel->isExists(['mobile' => $mobile])) {
                $this->error(lang(50004));
            }
        } elseif ($type == 2 || $type == 3) {
            if (!$userModel->isExists(['mobile' => $mobile])) {
                $this->error(lang(50001));
            }
        } else {
            $this->error(lang(50021));
        }

        $ret = $mobileVerify->sendCode($mobile);
        if ($ret) {
            $this->success(lang(20000));
        } else {
            $this->error(lang($mobileVerify->errorCode()), $mobileVerify->errorCode());
        }
    }

    /**
     * 注册
     */
    public function register()
    {
        $ret = $this->model->register(request('mobile'), request('code'), request('password'));
        if ($ret) {
            $this->success(lang(20002), $ret);
        } else {
            $this->error(lang($this->model->errorCode()), $this->model->errorCode());
        }
    }

    /**
     * 找回密码
     */
    public function password()
    {
        $ret = $this->model->password(request('mobile'), request('code'), request('password'));
        if ($ret) {
            $this->success(lang(20003));
        } else {
            $this->error(lang($this->model->errorCode()), $this->model->errorCode());
        }
    }

}
