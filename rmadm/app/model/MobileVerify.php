<?php
/**
 * 验证码
 */
use \Illuminate\Database\Eloquent\Model;

class MobileVerify extends Model
{
    protected $_dbConfig = 'molbase';
    protected $_tableName = 'ecm_mob_verify';

    public function checkCode($mobile, $code)
    {
        //测试
        if ($_SERVER['APP_ENV'] == 'local' && $code == '88888') {
            return true;
        }

        $params = [
            'fields' => 'code,send_time',
            'where'  => ['mobile' => $mobile],
            'order'  => 'send_time DESC',
        ];
        $data = $this->getOne($params);

        if (!empty($data) && $data['code'] == $code && ($data['send_time'] > time() - 1800)) {
            return true;
        } else {
            return false;
        }
    }

    public function sendCode($mobile)
    {
        if (!Func::checkMobile($mobile)) {
            return $this->errorCode(50003);
        }

        $send_time = $_SERVER['REQUEST_TIME'] - 60;
        $where = [
            'mobile'      => $mobile,
            'send_time >' => $send_time,
        ];
        if ($this->isExists($where)) {
            return $this->errorCode(50007);
        }

        $code = intval(mt_rand(100000, 999999));
        $ret = $this->sendSms($mobile, $code);
        if (!$ret) {
            return $this->errorCode(50008);
        }

        $data = [
            'user_id'   => 0,
            'mobile'    => $mobile,
            'send_time' => time(),
            'code'      => $code,
        ];
        $this->addData($data);

        return $code;
    }

    /**
     * 发送短信
     */
    public function sendSms($mobile, $code)
    {
        //调用接口，发送验证短信。
        $subject = '手机验证短信';
        $content = "【摩贝】验证码为" . $code . "，请在页面中输入以完成验证，有问题请致电：400-7281-666";

        return MobileQueue::m()->send($mobile, $subject, $content);
    }

}


?>