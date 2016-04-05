<?php
/**
 * 用户信息
 */
namespace App\Http\Controllers;

class InfoController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->model = \User::m();
    }

    /**
     * 教育经历
     */
    public function educationList()
    {
        $data = \UserEducation::m()->getAll([
            'where' => ['uid' => $this->uid, 'status' => 0],
        ]);

        $this->success(lang(20000), $data);
    }

    public function educationAdd()
    {
        $data = [
            'uid'        => $this->uid,
            'enter_time' => request('enter_time'),
            'leave_time' => request('leave_time'),
            'school'     => request('school'),
            'specialty'  => request('specialty'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        \UserEducation::m()->addData($data);

        $this->success(lang(20006));
    }

    public function educationEdit()
    {
        $id = request('id');
        $data = [
            'enter_time' => request('enter_time'),
            'leave_time' => request('leave_time'),
            'school'     => request('school'),
            'specialty'  => request('specialty'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        \UserEducation::m()->updateData($data, ['uid' => $this->uid, 'id' => $id]);

        $this->success(lang(20003));
    }

    /**
     * 删除教育经历
     */
    public function educationDelete()
    {
        $id = request('id');
        if (!$id) {
            $this->error(lang(50026));
        }
        \UserEducation::m()->deleteData(['id' => $id, 'uid' => $this->uid]);

        $this->success(lang(20008));
    }

    /**
     * 工作经历
     */
    public function experienceList()
    {
        $data = \UserExperience::m()->getAll([
            'where' => ['uid' => $this->uid, 'status' => 0],
        ]);

        $this->success(lang(20000), $data);
    }

    public function experienceAdd()
    {
        $data = [
            'uid'        => $this->uid,
            'enter_time' => request('enter_time'),
            'leave_time' => request('leave_time'),
            'company'    => request('company'),
            'position'   => request('position'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        \UserExperience::m()->addData($data);

        $this->success(lang(20006));
    }

    /**
     * 删除工作经历
     */
    public function experienceDelete()
    {
        $id = request('id');
        if (!$id) {
            $this->error(lang(50026));
        }

        \UserExperience::m()->deleteData(['id' => $id, 'uid' => $this->uid]);

        $this->success(lang(20008));
    }

    public function experienceEdit()
    {
        $id = request('id');
        $data = [
            'enter_time' => request('enter_time'),
            'leave_time' => request('leave_time'),
            'company'    => request('company'),
            'position'   => request('position'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        \UserExperience::m()->updateData($data, ['uid' => $this->uid, 'id' => $id]);

        $this->success(lang(20003));
    }

    /**
     * 个人名片
     */
    public function card() {
        $uid = request('uid');
        $fields = 'uid,realname,avatar,company,position,info,card_verify,city,province,buy_info,sell_info,supply_type,sex,background';
        $info = $this->model->getInfo($uid, $fields);
        if (empty($info)) {
            $this->error(lang(50016));
        }

        $info['is_self'] = $this->uid == $uid;
        $info['is_friend'] = \Friend::m()->getField(['uid' => $this->uid, 'friend_uid' => $uid], 'status');
        $where = [
            'uid' => $uid,
            'friend_uid' => $this->uid,
            'status' => 0,
        ];
        if (!$info['is_friend'] && \Friend::m()->isExists($where)) {
            $info['is_friend'] = 0;
        }

        $info['region'] = \Region::m()->getRegionName($info['province'], $info['city']);

        $info['dynamic_count'] = \Dynamic::m()->getCount([
            'uid' => $uid,
            'status' => 0,
        ]);

        $info['remark'] = \Friend::m()->getField([
            'uid' => $this->uid,
            'friend_uid' => $uid,
        ], 'remark');

        $info['experience'] = \UserExperience::m()->getAll([
            'where' => ['uid' => $uid, 'status' => 0],
            'order' => 'created_at DESC',
        ]);

        $info['education'] = \UserEducation::m()->getAll([
            'where' => ['uid' => $uid, 'status' => 0],
            'order' => 'created_at DESC',
        ]);

        if ($this->uid != $uid) {
            //添加访问记录
            \Visit::m()->addRecord($uid);
        }

        $this->success(lang(20000), $info);
    }

    /**
     * 进入APP时调用
     */
    public function onLoad()
    {
        $lastView = \UserLastview::m()->getData();

        $arr = \UserIndustry::m()->getAll([
            'where' => ['uid' => $this->uid, 'status' => 0],
        ]);
        $ids = \Func::pickArrayField($arr, 'industry_id');
        $ids = array_unique($ids);

        $dyns = \DynamicIndustry::m()->getAll([
            'fields' => 'count(DISTINCT did) as c',
            'where' => ['industry_id in' => $ids, 'created_at >' => $lastView['industry_dynamic']],
        ]);
        $countIndustry = $dyns[0]['c'];

        $friends = \Friend::m()->getAll([
            'where' => ['uid' => $this->uid, 'status' => 2],
            'order' => 'created_at DESC',
        ]);
        $fuids = \Func::pickArrayField($friends, 'friend_uid');

        if (empty($fuids)) {
            $countFriend = 0;
        } else {
            $countFriend = \Dynamic::m()->getCount([
                 'uid in' => $fuids,
                 'status' => 0,
                 'created_at >' => $lastView['friend_dynamic'],
            ]);
        }

        //新的人脉，最近访问
        $countNewFriend = \Friend::m()->getCount([
            'uid'    => $this->uid,
            'type'   => \Friend::TYPE_FRIEND,
            'status' => 0,
            'created_at >' => $lastView['new_friend'],
        ]);

        $visitTime = strtotime($lastView['visit']);
        if ((time() - 2592000) > $visitTime) {
            $visitTime = time() - 2592000;
        }
        $countNewVisit = \Visit::m()->getCount([
            'uid'          => $this->uid,
            'created_at >' => date('Y-m-d H:i:s', $visitTime),
            'status' => 0,
        ]);


        $user = \User::m()->getInfo($this->uid);

        $ret = [
            'lastViewIndustry' => $countIndustry,
            'lastViewFriend' => $countFriend,
            'countNewFriend' => $countNewFriend,
            'countNewVisit' => $countNewVisit,
            'user' => $user,
        ];

        $this->success(lang(20000), $ret);
    }

}
