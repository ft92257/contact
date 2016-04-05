<?php

namespace App\Http\Controllers;

class VerifyController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->model = \UserVerify::m();
    }


    /**
    public function _info()
    {
        $type = request('type');
        $data = $this->model->getAll([
            'where' => ['uid' => $this->uid, 'type' => $type],
        ]);

        $this->success(lang(20000), $data);
    }*/
    /**
     * 认证信息
     */
    public function info()
    {
        $fields = 'card_verify as status,card_reason as reason,card_pic as pic';
        $data = \User::m()->getById($this->uid, 'uid', $fields);

        $this->success(lang(20000), $data);
    }

    /**
     * 认证提交
     */
    public function submit()
    {
        $info = \File::m()->upload('pic');
        if ($info['status'] != 0) {
            $this->error(lang(50051));
        }
        $pic = $info['data']['url'];
        $data = [
            'card_pic' => $pic,
            'card_verify' => 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        \User::m()->updateData($data, ['uid' => $this->uid]);

        $this->success(lang(20013));
    }

    /*
    public function _submit()
    {
        $info = \File::m()->upload('pic');
        if ($info['status'] != 0) {
            $this->error(lang(50051));
        }
        $pic = $info['data']['url'];
        $type = request('type');//1 公司，2名片
        $where = [
            'uid'  => $this->uid,
            'type' => $type,
        ];
        if ($this->model->isExists($where)) {
            $data = [
                'updated_at' => date('Y-m-d H:i:s'),
                'pic'        => $pic,
                'status'     => 0,
            ];

            $this->model->updateData($data, $where);
        } else {
            $data = [
                'uid'        => $this->uid,
                'type'       => $type,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'pic'        => $pic,
            ];

            $this->model->addData($data);
        }

        $this->success(lang(20013));
    }*/


}
