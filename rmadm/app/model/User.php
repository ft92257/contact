<?php

use \Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $_tableName = 'tb_user';

    protected $aOptions = [
        'card_verify' => [
            '' => '',
            '0' => '未认证',
            '1' => '未审核',
            '2' => '认证失败',
            '3' => '已认证',
        ],
        'sex' => ['1' => '男', '2' => '女'],
        'supply_type' => [
            '' => '',
            '1' => '原料',
            '2' => '工厂',
            '3' => '贸易',
            '4' => '定制',
            '5' => '试剂',
            '6' => '设备',
            '7' => '服务',
            '8' => '其他',
        ],
        'status' => ['0' => '正常', '2' => '禁止发布动态和聊天'],
        'industry' => [
            '1' => '行业1',
            '2' => '行业2',
            '3' => '行业3',
            '4' => '行业4',
            '5' => '行业5',
        ],
    ];

    public function __construct() {
        parent::__construct();

        $this->aOptions['province'] = Region::m()->getProvince();
    }

    protected $listConfig = [
        'uid' => 'UID',
        'realname' => '真实姓名',
        'mobile' => '手机',
        'company' => '在职公司',
        'card_verify' => '名片认证',
        'created_at' => '注册时间',
        ['处理'],
    ];

    protected function _operate($config, $data) {
        $ret = '<a href="'.Func::U('edit', 'user', ['uid' => $data['uid']]).'">编辑</a>';

        return $ret;
    }

    protected $searchConfig = [
        'uid' => ['会员ID', 'text', 'params' => ['exact' => true]],
        'card_verify' => ['认证', 'select'],
        'created_at' => ['注册时间', 'datetime', 'params' => ['type' => 'date']],
        ['', 'submit'],
    ];

    protected $formConfig = [
        'uid' => ['会员ID', 'span'],
        'password' => ['密码', 'text', '留空表示不修改密码'],
        'avatar' => ['头像', 'image', 'params' => ['thumb' => '160x160', 'thumbs' => '160x160,640x640']],
        'mobile' => ['注册手机', 'span'],
        'realname' => ['真实姓名', 'text'],
        'company' => ['在职公司', 'text'],
        'position' => ['现任职位', 'text'],
        'card_pic' => ['名片', 'image', 'params' => ['thumb' => '160x160', 'thumbs' => '160x160,640x640']],
        'card_verify' => ['加V认证', 'radio'],
        'card_reason' => ['认证失败原因', 'textarea'],
        'sex' => ['性别', 'radio'],
        'province' => ['地区', 'select',
            'attrs' => ' onchange="getChildrenOptions(\'/user/getCity\', this, \'\')"',
            'children' => ['city' => ['', 'select']],
        ],
        'industry' => ['从事行业', 'checkbox'],
        'supply_type' => ['供应链身份', 'select'],
        'info' => ['个人简介', 'textarea'],
        'buy_info' => ['采购需求', 'textarea'],
        'sell_info' => ['销售需求', 'textarea'],
        'experience' => ['工作经历', 'custom'],
        'education' => ['教育背景', 'custom'],
        'status' => ['权限', 'radio'],
    ];

    /**
     * 处理用户编辑
     * @param $uid
     * @return bool
     */
    public function doSubmit($uid) {
        $data = $this->getFormData(true);
        if ($this->checkData($data, false)) {
            //print_r($data);exit;
            $this->begin();

            //处理
            if ($data['password']) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            } else {
                unset($data['password']);
            }
            if (!$data['avatar']) {
                unset($data['avatar']);
            }
            if (!$data['card_pic']) {
                unset($data['card_pic']);
            }

            //更新行业信息
            UserIndustry::m()->updateIndustry($data['industry'], UserIndustry::TYPE_BELONG, $uid);
            unset($data['industry']);

            //更新工作经验
            if (!empty($data['experience'])) {
                foreach ($data['experience'] as $key => $value) {
                    UserExperience::m()->updateData($value, ['id' => $key]);
                }
            }
            unset($data['experience']);
            //更新教育经历
            if (!empty($data['education'])) {
                foreach ($data['education'] as $key => $value) {
                    UserEducation::m()->updateData($value, ['id' => $key]);
                }
            }
            unset($data['education']);

            $this->updateData($data, ['uid' => $uid]);
            $this->commit();

            return true;
        } else {
            return false;
        }
    }

}