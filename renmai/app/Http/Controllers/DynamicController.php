<?php

namespace App\Http\Controllers;

class DynamicController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->model = \Dynamic::m();
    }

    /**
     * 我从事和关注的行业
     */
    public function myIndustry() {
        $options = \UserIndustry::m()->getOptions('industry');

        //我从事的行业
        $belongs = \UserIndustry::m()->getAll([
            'where' => [
                'uid' => $this->uid,
                'type' => \UserIndustry::TYPE_BELONG,
                'status' => 0,
            ],
        ]);
        $blgs = [];
        foreach ($belongs as $value) {
            if (isset($options[$value['industry_id']])) {
                $blgs[] = [
                    'key' => $value['industry_id'],
                    'value' => $options[$value['industry_id']],
                ];
            }
        }

        //我关注的行业
        $attentions = \UserIndustry::m()->getAll([
            'where' => [
                'uid' => $this->uid,
                'type' => \UserIndustry::TYPE_ATTENTION,
                'status' => 0,
            ],
        ]);
        $atnIds = \Func::pickArrayField($attentions, 'industry_id');
        $atns = [];
        foreach ($attentions as $value) {
            if (isset($options[$value['industry_id']])) {
                $atns[] = [
                    'key'   => $value['industry_id'],
                    'value' => $options[$value['industry_id']],
                ];
            }
        }

        //更多
        $others = [];
        foreach ($options as $key => $option) {
            if (!in_array($key, $atnIds)) {
                $others[] = [
                    'key' => $key,
                    'value' => $option,
                ];
            }
        }

        $ret = [
            'belong' => $blgs,
            'attention' => $atns,
            'more' => $others,
        ];

        $this->success(lang(20000), $ret);
    }


    /**
     * 行业动态
     */
    public function industry()
    {
        $page = (int)request('page');
        $page = max($page, 1);
        if ($page == 1) {
            //获取1000条动态id列表
            $arr = \UserIndustry::m()->getAll([
                'where' => ['uid' => $this->uid, 'status' => 0],
            ]);
            $ids = \Func::pickArrayField($arr, 'industry_id');
            $ids = array_unique($ids);

            $dyns = \DynamicIndustry::m()->getAll([
                'fields' => 'DISTINCT did',
                'where' => ['industry_id in' => $ids, 'status' => 0],
                'limit' => 1000,
                'order' => 'created_at DESC',
            ]);
            $dids = \Func::pickArrayField($dyns, 'did');

            //保存到缓存表
            \TmpList::m()->saveListIds(\TmpList::TYPE_INDUSTRY, $dids);
        } else {
            //从缓存表读取
            $dids = \TmpList::m()->getListIds(\TmpList::TYPE_INDUSTRY);
        }

        if (empty($dids)) {
            $data = [];
        } else {
            //数据处理
            $offset = ($page - 1) * 20;
            $pids = array_slice($dids, $offset, 20);
            if (empty($pids)) {
                $data = [];
            } else {
                $data = $this->model->getAll([
                    'where' => ['id in' => $pids],
                    'order' => 'created_at DESC',
                ], true);
            }
        }

        //记录访问时间
        \UserLastview::m()->setData(['industry_dynamic' => date('Y-m-d H:i:s')]);

        $this->success(lang(20000), $data);
    }

    /**
     * 好友动态
     */
    public function friend()
    {
        $page = request('page');
        $friends = \Friend::m()->getAll([
            'where' => ['uid' => $this->uid, 'status' => 2],
            'order' => 'created_at DESC',
        ]);
        if ($page == 1) {
            //获取1000条动态id列表
            $fuids = \Func::pickArrayField($friends, 'friend_uid');

            if (empty($fuids)) {
                $dids = [];
            } else {
                $dyns = $this->model->getAll([
                    'fields' => 'id',
                    'where'  => ['uid in' => $fuids, 'status' => 0],
                    'order'  => 'created_at DESC',
                    'limit'  => 1000,
                ]);

                $dids = \Func::pickArrayField($dyns, 'id');

                //保存到缓存表
                \TmpList::m()->saveListIds(\TmpList::TYPE_FRIEND, $dids);
            }
        } else {
            //从缓存表读取
            $dids = \TmpList::m()->getListIds(\TmpList::TYPE_FRIEND);
        }

        if (empty($dids)) {
            $data = [];
        } else {
            //数据处理
            $offset = ($page - 1) * 20;
            $pids = array_slice($dids, $offset, 20);

            $data = $this->model->getAll([
                'where' => ['id in' => $pids],
                'order' => 'created_at DESC',
            ], true);

            $friends = \Func::setArrayKey($friends, 'friend_uid');
            foreach ($data as &$value) {
                $value['user']['remark'] = isset($friends[$value['uid']]) ? $friends[$value['uid']]['remark'] : '';
            }
        }

        //记录访问时间
        \UserLastview::m()->setData(['friend_dynamic' => date('Y-m-d H:i:s')]);

        $this->success(lang(20000), $data);
    }

    /**
     * 点赞
     */
    public function praise() {
        $did = request('did');
        $dynamic = $this->model->getById($did);
        if (empty($dynamic)) {
            $this->error(lang(50016));
        }

        if ($dynamic['praise']) {
            $praises = explode(',', $dynamic['praise']);
        } else {
            $praises = [];
        }

        $realname = \User::m()->getField(['uid' => $this->uid], 'realname');
        if (!in_array($realname, $praises)) {
            $praises[] = $realname;
            $set = [
                'praise' => join(',', $praises),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $this->model->updateData($set, ['id' => $did]);

            $this->sendPush([
                'title' => $realname . '赞了您的动态！',
                'message' => '',
                'type' => 1,//点赞
                'data' => [
                    'source_id' => $did,
                    'realname' => $realname,
                ],
            ], [$dynamic['uid']]);
        }

        $this->success(lang(20004));
    }

    /**
     * 个人动态
     */
    public function personal()
    {
        $uid = request('uid');
        $page = request('page');

        if ($page == 1) {
            $fields = 'realname,avatar,company,position,info,card_verify,supply_type,sex,background';
            $user = \User::m()->getInfo($uid, $fields);
            if ($uid == $this->uid) {
                $user['realname'] = '我';
            }
        } else {
            $user = [];
        }

        $data = $this->model->getAll([
            'where' => ['uid' => $uid, 'status' => 0],
            'order' => 'created_at DESC',
            'limit' => [$page, 20],
        ], '_format_personal');

        $ret = [
            'user' => $user,
            'list' => $data,
        ];

        $this->success(lang(20000), $ret);
    }

    /**
     * 发布动态
     */
    public function publish()
    {
        $this->checkForbidden();

        $data = [
            'uid'        => $this->uid,
            'content'    => request('content'),
            'images'     => request('images'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $did = $this->model->addData($data);

        //TODO 是否从群发起
        /*
        $groups = \GroupMember::m()->getAll([
            'where' => [
                'uid'    => $this->uid,
                'type >' => 0,
                'status' => 0,
            ],
        ]);
        foreach ($groups as $group) {
            $dg = [
                'did'        => $did,
                'gid'        => $group['gid'],
                'created_at' => date('Y-m-d H:i:s'),
            ];
            \DynamicGroup::m()->addData($dg);
        }*/

        //添加行业动态关联记录
        $arr = \UserIndustry::m()->getAll([
            'where' => [
                'uid' => $this->uid,
                'type' => \UserIndustry::TYPE_BELONG,
                'status' => 0
            ],
        ]);

        foreach ($arr as $value) {
            \DynamicIndustry::m()->addData([
                'did' => $did,
                'industry_id' => $value['industry_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->success(lang(20007));
    }

    /**
     * 选择关注的行业
     */
    public function selectIndustry()
    {
        $industry = request('industry');

        \UserIndustry::m()->updateIndustry($industry, \UserIndustry::TYPE_ATTENTION);

        $this->success(lang(20014));
    }

    /**
     * 动态详情
     */
    public function detail()
    {
        $did = request('id');
        $dynamic = $this->model->getOne(['where' => ['id' => $did]]);
        $dynamic['is_self'] = $this->uid == $dynamic['uid'];
        $dynamic['images'] = empty($dynamic['images']) ? [] : explode(',', $dynamic['images']);

        $fields = 'realname,avatar,company,position,info,card_verify,supply_type';
        $user = \User::m()->getInfo($dynamic['uid'], $fields);

        $reply = \DynamicReply::m()->getAll([
            'where' => ['did' => $did, 'status' => 0],
            'order' => 'created_at',
        ]);

        $ret = [
            'dynamic' => $dynamic,
            'user'    => $user,
            'reply'   => $reply,
        ];

        $this->success(lang(20000), $ret);
    }

    /**
     * 上传图片
     */
    public function upload() {
        $info = \File::m()->upload('image');
        if ($info['status'] != 0) {
            $this->error(lang(50051));
        } else {
            $this->success(lang(20012), ['url' => $info['data']['url']]);
        }
    }

    /**
     * 回复
     */
    public function reply() {
        $this->checkForbidden();

        $did = request('did');
        $touid = request('uid');
        $toname = request('name');
        $content = request('content');
        $realname = \User::m()->getField(['uid' => $this->uid], 'realname');
        $data = [
            'did' => $did,
            'uid' => $this->uid,
            'name' => $realname,
            'touid' => $touid,
            'toname' => $toname,
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        \DynamicReply::m()->addData($data);

        $this->sendPush([
            'title' => $realname . '回复了您的动态！',
            'message' => $content,
            'type' => 2,//回复
            'data' => [
                'source_id' => $did,
                'realname' => $realname,
            ],
        ], [$touid]);

        $this->success(lang(20017));
    }

    /**
     * 删除回复
     */
    public function deleteReply()
    {
        $rid = request('id');

        \DynamicReply::m()->deleteData([
            'id' => $rid,
            'uid' => $this->uid,
        ]);

        $this->success(lang(20008));
    }

    /**
     * 删除自己的动态
     */
    public function delete() {
        $did = request('did');
        $data = $this->model->getById($did);
        if (empty($data) || $data['uid'] != $this->uid) {
            $this->error(lang(50016));
        }

        $this->model->deleteData(['id' => $did]);
        \DynamicIndustry::m()->deleteData(['did' => $did]);
        \DynamicGroup::m()->deleteData(['did' => $did]);

        $this->success(lang(20008));
    }

}
