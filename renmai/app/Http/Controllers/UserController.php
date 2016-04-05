<?php
/**
 * 用户中心-我的资料
 */
namespace App\Http\Controllers;

class UserController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->model = \User::m();
    }

    /**
     * 个人资料首页
     */
    public function index()
    {
        $user = $this->model->getById($this->uid, 'uid');
        unset($user['password']);
        $userIndustry = \UserIndustry::m();
        $arr = $userIndustry->getAll([
            'where' => ['uid' => $this->uid, 'type' => \UserIndustry::TYPE_BELONG, 'status' => 0],
        ]);
        $industry = [];
        foreach ($arr as $row) {
            $industry[] = [
                'key' => $row['industry_id'],
                'value' => $userIndustry->getOptions('industry', $row['industry_id']),
            ];
        }
        $user['industry'] = $industry;
        $user['region'] = \Region::m()->getRegionName($user['province'], $user['city']);

        $this->success(lang(20000), $user);
    }

    /**
     * 完善个人资料
     */
    public function setInfo()
    {
        $data = [
            'realname' => request('realname'),
            'company'  => request('company'),
            'position' => request('position'),
            'industry' => request('industry'),
        ];
        if (!$data['realname'] || !$data['company'] || !$data['position'] || !$data['industry']) {
            $this->error(lang(50021));
        }

        $this->model->setInfo($data);

        $this->success(lang(20011));
    }

    /**
     * 获取行业选项
     */
    public function getOptions()
    {
        $options = \UserIndustry::m()->getOptions('industry');
        $ret = [];
        foreach ($options as $key => $option) {
            $ret[] = [
                'key' => strval($key),
                'value' => $option,
            ];
        }

        $this->success(lang(20000), ['industry' => $ret]);
    }

    /**
     * 修改密码
     */
    public function changePassword()
    {
        $old_pwd = request('old_pwd');
        $new_pwd = request('new_pwd');
        $user = $this->model->getById($this->uid, 'uid');
        if (!password_verify($old_pwd, $user['password'])) {
            $this->error(lang(50028));
        }

        $set = [
            'password' => password_hash($new_pwd, PASSWORD_DEFAULT),
        ];
        $this->model->updateData($set, ['uid' => $this->uid]);

        $this->success(lang(20003));
    }

    /**
     * 设置密码
     */
    public function setPassword()
    {
        $password = request('password');

        if ($this->model->getField(['uid' => $this->uid], 'password')) {
            $this->error(lang(50018));
        }

        $set = ['password' => password_hash($password, PASSWORD_DEFAULT)];
        $this->model->updateData($set, ['uid' => $this->uid]);

        $this->success(lang(20010));
    }


    /**
     * 设置头像
     */
    public function setAvatar()
    {
        $info = \File::m()->upload('avatar', 'image', '80x80,160x160,640x640');

        if ($info['status'] != 0) {
            $this->error(lang(50051));
        }

        $avatar = $info['data']['url'];
        $this->model->updateData(['avatar' => $avatar], ['uid' => $this->uid]);
        $ret = ['avatar' => $avatar];

        $this->success(lang(20012), $ret);
    }

    /**
     * 设置背景图片
     */
    public function setBackground()
    {
        $info = \File::m()->upload('background', 'image', '640x640');

        if ($info['status'] != 0) {
            $this->error(lang(50051));
        }

        $background = $info['data']['url'];
        $this->model->updateData(['background' => $background], ['uid' => $this->uid]);
        $ret = ['background' => $background];

        $this->success(lang(20012), $ret);
    }

    /**
     * 更新字段值
     */
    public function updateField()
    {
        $field = request('field');
        $value = request('value');

        if (!in_array($field, ['info', 'supply_type', 'sex', 'realname', 'company', 'position', 'industry'])) {
            $this->error(lang(50016));
        }
        if (in_array($field, ['realname', 'company', 'position'])) {
            $this->model->updateData([$field => $value, 'card_verify' => 0], ['uid' => $this->uid]);
        } elseif ($field == 'industry') {
            //更新行业信息
            \UserIndustry::m()->updateIndustry($value, \UserIndustry::TYPE_BELONG);
        } else {
            $this->model->updateData([$field => $value], ['uid' => $this->uid]);
        }

        $this->success(lang(20011));
    }

    /**
     * 获取二维码名片
     */
    public function myQrCard()
    {
        $fields = 'realname,avatar,company,position,qr_code';
        $user = $this->model->getById($this->uid, 'uid', $fields);
        if (empty($user['qr_code'])) {
            //人脉APP扫描时添加参数self=1, 转为申请好友操作
            $url = \Func::getHostUrl() . '/download/app?uid=' . $this->uid;

            $img = \Func::getQrCode($url);
            $qrcode = base64_encode($img);
            $user['qr_code'] = $qrcode;

            $this->model->updateData(['qr_code' => $qrcode], ['uid' => $this->uid]);
        }

        $this->success(lang(20000), $user);
    }

    /**
     * 设置采购，销售需求
     */
    public function setDemand()
    {
        $data = [
            'buy_info'  => request('buy_info'),
            'sell_info' => request('sell_info'),
        ];
        $this->model->updateData($data, ['uid' => $this->uid]);

        $this->success(lang(20010));
    }

    /**
     * 获取区域
     */
    public function getRegions()
    {
        $user = $this->model->getById($this->uid, 'uid', 'province,city');
        //获取区域列表，选中项
        $regions = \Region::m()->getRegions($user['province'], $user['city']);
        $ret = array_values($regions);

        $this->success(lang(20000), $ret);
    }

    /**
     * 设置区域
     */
    public function setRegion()
    {
        $city = request('city');
        $province = request('province');
        $set = [
            'city'     => $city,
            'province' => $province,
        ];

        //更新用户表region字段
        $this->model->updateData($set, ['uid' => $this->uid]);

        //更新用户行业表
        \UserIndustry::m()->updateData($set, ['uid' => $this->uid, 'type' => 1]);

        $this->success(lang(20010));
    }
}
