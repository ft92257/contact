<?php

namespace App\Http\Controllers;

class IndexController extends Controller
{
    protected $_authlevel = 0;

    public function index()
    {
        $data = [
            'title' => '恭喜您中了500万，请赶紧前往领奖！',
            'message' => '过期不领就没啦！',
            'type' => 1,
            'data' => [
                'id' => 2,
            ],
        ];
        $this->sendPush($data);
    }

    /*
    public function initHxUser()
    {
        $users = \User::m()->getAll([]);
        foreach ($users as $user) {
            $password = md5(mt_rand());
            $password = '123456';
            $ret = \Login::m()->createHxUser($user['uid'], $password);
            if ($ret) {
                \User::m()->updateData(['hx_pwd' => $password], ['uid' => $user['uid']]);
                echo 'SUCCESS:' . $user['uid'] . '\n<br>';
            } else {
                echo 'ERROR:' . $user['uid'] . '\n<br>';
            }
        }
    }*/
}
