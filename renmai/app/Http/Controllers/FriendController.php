<?php

namespace App\Http\Controllers;

class FriendController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->model = \Friend::m();
    }

    /**
     * 最近聊天的人 TODO 改为前端处理
     */
    public function recentChat()
    {
        $data = \Message::m()->getAll([
            'where' => [
                'touid' => $this->uid,
                'is_read' => 0,
                'status' => 0,
            ],
            'order' => 'created_at DESC',
        ]);

        $chats = [];
        foreach ($data as $value) {
            if (!isset($chats[$value['uid']])) {
                $value['count'] = 1;
                $chats[$value['uid']] = $value;
            } else {
                $chats[$value['uid']]['count']++;
            }
        }

        $ret = \Message::m()->formatLastChat($chats);

        $this->success(lang(20000), $ret);
    }

    /**
     * 新的人脉
     */
    public function newFriend()
    {
        $data = $this->model->getAll([
            'fields' => 'id,msg,friend_uid,created_at',
            'where' => [
                'uid'    => $this->uid,
                'type'   => \Friend::TYPE_FRIEND,
                'status' => 0,
            ],
            'order' => 'created_at DESC',
        ], true);

        //记录访问时间
        \UserLastview::m()->setData(['new_friend' => date('Y-m-d H:i:s')]);

        $this->success(lang(20000), $data);
    }

    /**
     * 我关注的人脉
     */
    public function myAttention()
    {
        $data = $this->model->getAll([
            'fields' => 'id,friend_uid,created_at',
            'where' => [
                'uid'    => $this->uid,
                'type'   => \Friend::TYPE_ATTENTION,
                'status' => 2,//关注的人状态直接设为2
            ],
            'order' => 'created_at DESC',
        ], true);

        $this->success(lang(20000), $data);
    }

    /**
     * 最近访问的人
     */
    public function lastVisit()
    {
        $data = \Visit::m()->getAll([
            'fields' => 'id,vuid,created_at',
            'where' => [
                'uid'          => $this->uid,
                'created_at >' => date('Y-m-d H:i:s', time() - 2592000),
                'status' => 0,
            ],
            'limit' => 50,
            'order' => 'updated_at DESC',
        ], true);

        //记录访问时间
        \UserLastview::m()->setData(['visit' => date('Y-m-d H:i:s')]);

        $this->success(lang(20000), $data);
    }

    /**
     * 我的好友
     */
    public function myFriend()
    {
        $data = $this->model->getAll([
            'fields' => 'id,friend_uid,first_letter,created_at,remark,shield',
            'where' => [
                'uid'    => $this->uid,
                'type'   => \Friend::TYPE_FRIEND,
                'status' => 2,
            ],
            'order' => 'first_letter,created_at DESC',
        ], true);

        $this->success(lang(20000), $data);
    }


    /**
     * 行业人脉
     */
    public function industry()
    {
        $industry_id = request('industry_id');
        $city = request('city');
        $province = request('province');
        $page = request('page');

        $where = [
            'type'   => \UserIndustry::TYPE_BELONG,
            'status' => 0,
            'uid !=' => $this->uid,
        ];
        if ($industry_id) {
            $where['industry_id'] = $industry_id;
        }
        if ($province) {
            $where['province'] = $province;
        }
        if ($city) {
            $where['city'] = $city;
        }

        $data = \UserIndustry::m()->getAll([
            'fields' => 'DISTINCT uid',
            'where'  => $where,
            'limit'  => [$page, 20],
        ], true);

        $this->success(lang(20000), $data);
    }

    /**
     * 推荐好友
     */
    public function recommend()
    {
        //排除的好友
        $fuids = \Friend::m()->getAll([
            'fields' => 'friend_uid',
            'where'  => [
                'type'      => \Friend::TYPE_FRIEND,
                'status !=' => 1,
                'uid'       => $this->uid,
            ],
        ]);
        $fuids = \Func::pickArrayField($fuids, 'friend_uid');

        $applys = \Friend::m()->getAll([
            'fields' => 'uid',
            'where'  => [
                'type'       => \Friend::TYPE_FRIEND,
                'status'     => 0,
                'friend_uid' => $this->uid,
            ],
        ]);
        $applys = \Func::pickArrayField($applys, 'uid');

        $fuids = array_merge($fuids, $applys, [$this->uid]);

        //我的行业ids
        $industry_ids = \UserIndustry::m()->getIndustryIds($this->uid);
        if (empty($industry_ids)) {
            $this->error('我的行业信息不能为空！');
        }

        $userInfo = \User::m()->getInfo($this->uid);

        $data = [];
        if ($userInfo['city']) {
            //先查询同一城市的
            $data = \UserIndustry::m()->getAll([
                'fields' => 'uid',
                'where'  => [
                    'uid not in'     => $fuids,
                    'industry_id in' => $industry_ids,
                    'city'           => $userInfo['city'],
                ],
                'limit'  => 10,
            ]);
            $uids = \Func::pickArrayField($data, 'uid');
            $fuids = array_merge($fuids, $uids);
        }
        $count = count($data);
        if ($count < 10 && $userInfo['province']) {
            //再查询同一省的
            $pvdata = \UserIndustry::m()->getAll([
                'fields' => 'uid',
                'where'  => [
                    'uid not in'     => $fuids,
                    'industry_id in' => $industry_ids,
                    'province'       => $userInfo['province'],
                ],
                'limit'  => 10 - $count,
            ]);
            $data = array_merge($data, $pvdata);
            $uids = \Func::pickArrayField($data, 'uid');
            $fuids = array_merge($fuids, $uids);
        }
        $count = count($data);
        if ($count < 10) {
            $odata = \UserIndustry::m()->getAll([
                'fields' => 'uid',
                'where'  => [
                    'uid not in'     => $fuids,
                    'industry_id in' => $industry_ids,
                ],
                'limit'  => 10 - $count,
            ]);
            $data = array_merge($data, $odata);
        }

        $ret = \UserIndustry::m()->getFormatData($data);

        $this->success(lang(20000), $ret);
    }

    /**
     * 附近的人脉
     */
    public function nearby()
    {
        $lng = (float) request('lng');//经度
        $lat = (float) request('lat');//纬度

        //保存我的经纬度信息
        \User::m()->updateData(['lng' => $lng, 'lat' => $lat], ['uid' => $this->uid]);

        $data = \User::m()->getAroundUsers($lat, $lng, 5000);
        if (count($data) < 200) {
            $data = \User::m()->getAroundUsers($lat, $lng, 20000);
        }

        $ret = \User::m()->formatNearbyUsers($data);

        $this->success(lang(20000), $ret);
    }

    /**
     * 批量申请好友
     */
    public function multiApply() {
        $fuids = request('fuids');
        $friend_uids = explode(',', $fuids);
        if (!$fuids || empty($friend_uids)) {
            $this->error(lang(50021));
        }
        $msg = '对方请求添加你为好友';
        foreach ($friend_uids as $friend_uid) {
            $this->_apply($friend_uid, $msg);
        }

        $this->success(lang(20016));
    }

    /**
     * 申请好友
     */
    public function apply() {
        $friend_uid = request('friend_uid');
        if (!$friend_uid) {
            $this->error(lang(50021));
        }
        $msg = request('msg');

        $this->_apply($friend_uid, $msg);

        $this->success(lang(20016));
    }

    /**
     * 申请好友处理 申请通过后才添加自己的记录
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

    /**
     * 通过好友申请
     */
    public function approve()
    {
        $id = request('id');//申请id
        $data = $this->model->getById($id);
        if (empty($data) || $data['uid'] != $this->uid) {
            $this->error(lang(50016));
        }

        //更改状态
        $this->model->updateData(['status' => 2], ['id' => $id]);

        $where = [
            'uid' => $data['friend_uid'],
            'friend_uid'   => $this->uid,
            'type'         => \Friend::TYPE_FRIEND,
        ];
        if ($this->model->isExists($where)) {
            $set = [
                'msg'          => '已经通过好友申请',
                'updated_at'   => date('Y-m-d H:i:s'),
                'status' => 2,
            ];
            $this->model->updateData($set, $where);
        } else {
            //对方添加好友记录
            $name = \User::m()->getField(['uid' => $this->uid], 'realname');
            $friend = [
                'uid'          => $data['friend_uid'],
                'friend_uid'   => $this->uid,
                'type'         => \Friend::TYPE_FRIEND,
                'first_letter' => \Func::getFirstLetter($name),
                'msg'          => '已经通过好友申请',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
                'status' => 2,
            ];

            $this->model->addData($friend);
        }

        $this->success(lang(20004));
    }

    /**
     * 删除好友申请
     */
    public function deleteApply() {
        $id = request('id');
        $data = $this->model->getById($id);
        if (empty($data)) {
            $this->error(lang(50016));
        }
        $this->model->deleteData([
            'id' => $id,
            'uid' => $this->uid,
        ]);

        $this->model->deleteData([
            'uid' => $data['friend_uid'],
            'friend_uid' => $this->uid,
        ]);

        $this->success(lang(20008));
    }

    /**
     * 删除好友
     */
    public function delete() {
        $uid = request('uid');
        $this->model->deleteData([
            'friend_uid' => $uid,
            'uid' => $this->uid,
        ]);

        $this->model->deleteData([
            'uid' => $uid,
            'friend_uid' => $this->uid,
        ]);

        $this->success(lang(20008));
    }

    /**
     * 好友备注
     */
    public function remark() {
        $friend_uid = request('friend_uid');
        $remark = request('remark');

        $set = [
            'remark' => $remark,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->model->updateData($set, ['uid' => $this->uid, 'friend_uid' => $friend_uid]);

        $this->success(lang(20010));
    }

    /**
     * 屏蔽对方
     */
    public function shield() {
        $friend_uid = request('friend_uid');
        $type = request('type');
        $set = [
            'shield' => $type == 1 ? 0 : 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->model->updateData($set, ['uid' => $this->uid, 'friend_uid' => $friend_uid]);

        //TODO push

        $this->success(lang(20004));
    }

    /**
     * 获取好友信息
     */
    public function getInfo()
    {
        $uid = request('uid');
        $friend = \Friend::m()->getOne([
            'where' => [
                'type' => \Friend::TYPE_FRIEND,
                'uid' => $this->uid,
                'friend_uid' => $uid,
            ],
        ]);

        $this->success(lang(20000), $friend);
    }

}
