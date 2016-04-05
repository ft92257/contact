<?php

namespace App\Http\Controllers;

class GroupController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->model = \Group::m();
    }

    /**
     * 创建人脉群
     */
    public function create()
    {
        //判断已有数量
        if ($this->model->getCreatedCount() >= 3) {
            $this->error(lang(50052));
        }

        $data = [
            'uid'         => $this->uid,
            'max_count'   => 1000,
            'name'        => request('name'),
            'pic'         => request('pic'),
            'info'        => request('info'),
            'verify_type' => request('verify_type'),
            'member_count' => 1,
        ];

        $gid = $this->model->addData($data);

        $member = [
            'gid' => $gid,
            'uid' => $this->uid,
            'type' => 3,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        \GroupMember::m()->addData($member);

        $this->success(lang(20015));
    }

    /**
     * 群设置
     */
    public function setInfo() {
        $field = request('field');
        $value = request('value');
        $gid = request('gid');
        if (!in_array($field, ['name', 'info', 'pic', 'verify_type'])) {
            $this->error(lang(50016));
        }

        $this->model->updateData([$field => $value], ['id' => $gid, 'uid' => $this->uid]);

        $this->success(lang(20010));
    }

    /**
     * 群成员列表
     */
    public function members() {
        $gid = request('gid');
        $members = \GroupMember::m()->getAll([
            'fields' => 'type,uid',
            'where' => [
                'gid' => $gid,
                'type !=' => 0,
                'status' => 0,
            ],
        ], true);

        $this->success(lang(20000), $members);
    }

    /**
     * 申请加入
     */
    public function apply()
    {
        $gid = request('gid');
        if (! \GroupMember::m()->isExists(['uid' => $this->uid, 'gid' => $gid])) {
            $data = [
                'uid' => $this->uid,
                'gid' => $gid,
                'type' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            \GroupMember::m()->addData($data);
        } else {
            \GroupMember::m()->updateData(['type' => 0, 'status' => 0], ['uid' => $this->uid, 'gid' => $gid, 'status !=' => 0]);
        }

        $this->success(lang(20016));
    }

    /**
     * 审核
     */
    public function agree()
    {
        $gid = request('gid');
        $uids = request('uids');

        $this->checkAdmin($gid, $this->uid);

        //申请通过
        if (empty($uids)) {
            $this->error(lang(50021));
        }
        $uids = explode(',', $uids);

        \GroupMember::m()->updateData(['type' => 1], [
            'gid' => $gid,
            'uid in' => $uids,
            'type' => 0,
        ]);

        $this->success(lang(20004));
    }

    /**
     * 检测是否管理员
     * @param $gid
     * @param $uid
     */
    protected function checkAdmin($gid, $uid)
    {
        $me = \GroupMember::m()->getOne([
            'where' => [
                'gid' => $gid,
                'uid' => $uid,
            ],
        ]);
        //判断我是否管理员
        if ($me['type'] < 2) {
            $this->error(lang(50018));
        }
    }

    /**
     * 申请列表
     */
    public function applyList()
    {
        $gid = request('gid');

        $this->checkAdmin($gid, $this->uid);

        $data = \GroupMember::m()->getAll([
            'where' => ['gid' => $gid, 'type' => 0, 'status' => 0],
        ], true);

        $this->success(lang(20000), $data);
    }

    /**
     * 删除申请
     */
    public function deleteApply()
    {
        $uid = request('uid');
        $gid = request('gid');

        $this->checkAdmin($gid, $this->uid);

        \GroupMember::m()->deleteData([
            'gid' => $gid,
            'uid' => $uid,
        ]);

        $this->success(lang(20004));
    }

    /**
     * 剔除成员
     */
    public function tickMember() {
        $gid = request('gid');
        $uid = request('uid');

        $me = \GroupMember::m()->getOne([
            'where' => [
                'gid' => $gid,
                'uid' => $this->uid,
            ],
        ]);

        $user = \GroupMember::m()->getOne([
            'where' => [
                'gid' => $gid,
                'uid' => $uid,
            ],
        ]);

        if (empty($me) || empty($user)) {
            $this->error(lang(50016));
        }

        if ($me['type'] >= 2 && $me['type'] > $user['type']) {
            \GroupMember::m()->deleteData([
                'gid' => $gid,
                'uid' => $uid,
            ]);

            $this->success(lang(20004));
        } else {
            $this->error(lang(50018));
        }
    }

    /**
     * 设置群管理员
     */
    public function setAdmin() {
        $gid = request('gid');
        $uid = request('uid');
        $me = \GroupMember::m()->getOne([
            'where' => [
                'gid' => $gid,
                'uid' => $this->uid,
            ],
        ]);

        $user = \GroupMember::m()->getOne([
            'where' => [
                'gid' => $gid,
                'uid' => $uid,
            ],
        ]);

        if (empty($me) || empty($user)) {
            $this->error(lang(50016));
        }

        if ($me['type'] == 3 && $user['type'] == 1) {
            $count = \GroupMember::m()->getCount([
                'gid' => $gid,
                'type' => 2,
                'status' => 0,
            ]);
            if ($count < 3) {
                \GroupMember::m()->updateData(['type' => 2], ['gid' => $gid, 'uid' => $uid]);

                $this->success(lang(20004));
            } else {
                $this->error(lang(50031));
            }
        } else {
            $this->error(lang(50018));
        }
    }

    /**
     * 我加入的人脉群
     */
    public function myGroup()
    {
        $data = \GroupMember::m()->getAll([
            'where' => [
                'uid' => $this->uid,
                'status' => 0,
                'type >' => 0,
            ],
        ], '_get_group');

        $this->success(lang(20000), $data);
    }

    /**
     * 群动态
     */
    public function dynamic()
    {
        $gid = request('gid');
        $page = (int)request('page');

        $data = \GroupDynamic::m()->getAll([
            'where' => ['gid' => $gid, 'status' => 0],
            'order' => 'created_at DESC',
            'limit' => \Func::getPageLimit($page, 10),
        ], true);

        $this->success(lang(20000), $data);
    }
}
