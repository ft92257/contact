<?php
/**
 * 用户登录模型
 *
 */
use \Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    protected $_tableName = 'tb_user';

    public function mobileLogin($username, $password)
    {
        if (!Func::checkMobile($username)) {
            return $this->errorCode(50003);
        }

        $user = $this->getById($username, 'mobile');
        if (empty($user)) {
            return $this->errorCode(50001);
        }

        if (!password_verify($password, $user['password'])) {
            return $this->errorCode(50002);
        }

        $key = Session::m()->setKey($user['uid']);

        $ret = [
            'uid' => $user['uid'],
            'mobile'   => $user['mobile'],
            'SN_API'   => $key,
            'realname' => $user['realname'],//为空则需完善个人资料
        ];

        return $ret;
    }

    public function codeLogin($username, $code)
    {
        if (!Func::checkMobile($username)) {
            return $this->errorCode(50003);
        }

        $user = $this->getById($username, 'mobile');
        if (empty($user)) {
            return $this->errorCode(50001);
        }

        //验证码判断
        if (!MobileVerify::m()->checkCode($username, $code)) {
            return $this->errorCode(50006);
        }

        $key = Session::m()->setKey($user['uid']);

        $ret = [
            'uid' => $user['uid'],
            'mobile'   => $user['mobile'],
            'SN_API'   => $key,
            'realname' => $user['realname'],//为空则需完善个人资料
        ];

        return $ret;
    }


    public function password($mobile, $code, $password)
    {
        if (!Func::checkMobile($mobile)) {
            return $this->errorCode(50003);
        }
        $user = $this->getById($mobile, 'mobile');
        if (empty($user)) {
            return $this->errorCode(50001);
        }
        //验证码判断
        if (!MobileVerify::m()->checkCode($mobile, $code)) {
            return $this->errorCode(50006);
        }

        $set = ['password' => password_hash($password, PASSWORD_DEFAULT)];
        $this->updateData($set, ['uid' => $user['uid']]);

        return true;
    }

    public function register($username, $code, $password)
    {
        if (!Func::checkMobile($username)) {
            return $this->errorCode(50003);
        }

        $user = $this->getById($username, 'mobile');
        if (!empty($user)) {
            return $this->errorCode(50004);
        }

        //验证码判断
        if (!MobileVerify::m()->checkCode($username, $code)) {
            return $this->errorCode(50006);
        }

        $user = [
            'mobile'     => $username,
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'realname' => '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $uid = $this->addData($user);
        //创建环信用户
        $this->createHxUser($uid);

        $key = Session::m()->setKey($uid);

        $ret = [
            'uid' => $uid,
            'mobile'   => $username,
            'SN_API'   => $key,
            'realname' => '',//为空则需完善个人资料
        ];

        return $ret;
    }

    public function molbaseLogin($username, $password)
    {
        if (Func::checkMobile($username)) {
            //手机
            $store = MolbaseStore::m()->getById($username, 'mobile_phone');
            if (empty($store)) {
                return $this->errorCode(50001);
            }
            $user = MolbaseMember::m()->getById($store['store_id'], 'user_id');
        } elseif (strpos($username, '@') !== false) {
            //邮箱
            $user = MolbaseMember::m()->getById($username, 'email');
        }
        if (empty($user)) {
            //账号
            $user = MolbaseMember::m()->getById($username, 'user_name');
        }
        if (empty($user)) {
            return $this->errorCode(50001);
        }
        if ($user['password'] != md5($password)) {
            return $this->errorCode(50002);
        }
        if (!isset($store)) {
            $store = MolbaseStore::m()->getById($user['user_id'], 'store_id');
        }

        //手机未认证
        if (!$store['mobile_phone'] || !$user['mail_verify']) {
            $this->errorMessage($store['mobile_phone']);
            return $this->errorCode(50050);
        }

        $curUser = $this->getById(md5($user['user_id']), 'openid');
        $curUser['set_password'] = false;
        if (empty($curUser)) {
            $data = [
                'mobile'     => $store['mobile_phone'],
                //'password'   => password_hash($password, PASSWORD_DEFAULT),
                'openid' => md5($user['user_id']),
                'type' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $uid = $this->addData($data);
            //创建环信用户
            $this->createHxUser($uid);

            $curUser = [
                'uid'      => $uid,
                'mobile'   => $store['mobile_phone'],
                'realname' => '',
                'set_password' => true,
            ];
        }

        //设置session
        $key = Session::m()->setKey($curUser['uid']);
        $ret = [
            'uid' => $curUser['uid'],
            'mobile'   => $curUser['mobile'],
            'SN_API'   => $key,
            'realname' => $curUser['realname'],//为空则需完善个人资料
            'set_password' => $curUser['set_password'],
        ];

        return $ret;
    }

    /**
     * 创建环信用户
     */
    public function createHxUser($uid) {
        $curl = new Curl();
        $curl->init();
        $curl->setOption(CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $url = 'https://a1.easemob.com'.Cf::C('HX_PATH').'/token';
        $data = [
            'grant_type' => 'client_credentials',
            'client_id' => Cf::C('HX_CLIENT_ID'),
            'client_secret' => Cf::C('HX_CLIENT_SECRET'),
        ];

        $result = $curl->post($url, json_encode($data));
        $arr = json_decode($result, true);
        if (isset($arr['access_token'])) {
            $token = $arr['access_token'];
        } else {
            $curl->close();
            return false;
        }

        $url = 'https://a1.easemob.com'.Cf::C('HX_PATH').'/users';
        $data = [
            'username' => 'renmai_' . $uid,
            'password' => 'renmai2016',
            'nickname' => $uid,
        ];
        $curl->init();
        $curl->setOption(CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
        ]);
        $result = $curl->post($url, json_encode($data));
        $arr = json_decode($result, true);

        $curl->close();
        if (isset($arr['entities'])) {
            return true;
        } else {
            return false;
        }
    }
}

?>