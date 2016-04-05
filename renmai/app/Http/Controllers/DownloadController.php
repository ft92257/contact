<?php

namespace App\Http\Controllers;

class DownloadController extends Controller
{
    protected $_authlevel = 0;

    public function __construct()
    {
        parent::__construct();

        $this->model = \Friend::m();
    }

    public function app()
    {
        $uid = request('uid');
        $is_self = request('is_self');

        if ($is_self) {
            //发送加好友申请
            if (!$this->uid) {
                $this->noLoginError();
            }

            $this->_apply($uid, '对方请求添加你为好友');

            $this->success(lang(20016));
        } else {
            //TODO 跳转到应用宝
        }
    }

    /**
     * 申请好友处理
     * @param $friend_uid
     * @param $msg
     */
    protected function _apply($friend_uid, $msg)
    {
        $where = [
            'friend_uid' => $this->uid,
            'uid'        => $friend_uid,
            'type'       => \Friend::TYPE_FRIEND,
        ];
        $friend = $this->model->getOne(['where' => $where]);
        if (empty($friend)) {
            //对方添加记录
            $name = \User::m()->getField(['uid' => $friend_uid], 'realname');
            $data = [
                'friend_uid'   => $this->uid,
                'uid'          => $friend_uid,
                'type'         => \Friend::TYPE_FRIEND,
                'first_letter' => \Func::getFirstLetter($name),
                'msg'          => $msg,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ];

            $this->model->addData($data);
        } else {
            if ($friend['status'] == 2) {
                //$this->error(lang(50053));
            } else {
                $data = [
                    'msg' => $msg,
                    'status'     => 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                $this->model->updateData($data, ['id' => $friend['id']]);
            }
        }
    }
}
