<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    //当前模型
    public $model;
    //返回函数
    protected $_ret_func = '_ret_api';
    //权限验证级别 2:需验证权限, 1:只需验证登录, 0不需验证
    protected $_authlevel = 1;
    protected $_success_status = 200;
    protected $uid = 0;

    public function __construct()
    {
        //加密判断，上线时恢复
        /*
        if (!($_GET['c'] == 'other' && $_GET['a'] == 'begin')) {
            $token = strtolower(\Func::R('TOKEN'));
            $captcha = substr($token, 0, 32);
            $time = substr($token, 32);
            if (md5('hdkCMA!qxH8Qv&IVZHEn2ar#oqCW!%mM' . $time) != $captcha) {
                $this->error(\Cf::lang(50016));
            }
            if ($time < (time() - 60) || $time > (time() + 60)) {
                $this->error(\Cf::lang(50033));
            }
        }*/

        //加载用户数据
        $uid = \Session::m()->check();
        //$uid = 1;//test
        if ($this->_authlevel >= 1 && !$uid) {
            $this->noLoginError();
        }
        if ($uid) {
            $this->uid = $uid;
            \Func::setGlobal('uid', $this->uid);
        }
    }

    public function noLoginError()
    {
        $this->error('请先登录!', 1021);
    }

    public function isLogin()
    {
        return $this->uid;
    }

    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    public function success($msg = 'Success!', $data = array())
    {
        $this->result(true, $msg, 20000, $data);
    }

    public function error($msg = 'Fail!', $code = 50000, $data = array())
    {
        if ($code == 50000 && $GLOBALS['RESULT_CODE']) {
            $code = $GLOBALS['RESULT_CODE'];
        }
        $this->result(false, $msg, $code, $data);
    }

    protected function result($done = false, $msg = '', $code = 50000, $retval = array())
    {
        $ret = [
            'done'   => $done,
            'msg'    => $msg,
            'code'   => $code,
            'retval' => empty($retval) ? null : $retval,
        ];
        if (is_array($ret['retval'])) {
            $ret['retval'] = \Func::formatReturnData($ret['retval']);
        }

        header('Content-type: application/json');
        die(json_encode($ret));
    }

    public function listPush($params)
    {
        return \Func::listPush('push_list', $params);
    }

    /**
     * 发送消息
     * @param $data
     * @param array $uids
     */
    public function sendPush($data, $uids = [])
    {
        return \JgPush::send($data['title'], $data, $uids);
    }

    /**
     * 是否被禁止发布内容
     * @return bool
     */
    protected function checkForbidden() {
        $status = \User::m()->getField(['uid' => $this->uid], 'status');
        if ($status == 2) {
            $this->error(lang(50054));
        }
    }

}
