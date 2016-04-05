<?php

use \Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $_tableName = 'tb_user';

    /**
     * 完善个人资料
     */
    public function setInfo($data)
    {
        $industry = $data['industry'];
        unset($data['industry']);

        $this->updateData($data, ['uid' => $this->uid]);

        //更新行业信息
        UserIndustry::m()->updateIndustry($industry, UserIndustry::TYPE_BELONG);
    }

    public function getInfo($uid, $fields = '*')
    {
        $user = $this->getById($uid, 'uid', $fields);
        if ($fields === '*') {
            unset($user['password']);
        }
        if (isset($user['card_verify'])) {
            $user['card_verify'] = $user['card_verify'] == 3 ? 1 : 0;
        }
        if (isset($user['avatar']) && !$user['avatar']) {
            $user['avatar'] = 'http://renmai.haowand.com/images/default.jpg';
        }

        return $user;
    }

    /**
     * 获取周围的用户
     * @param $lat 纬度
     * @param $lng 经度
     * @param $raidus 半径
     */
    public function getAroundUsers($lat, $lng, $raidus) {
        $around = Func::getAround($lat, $lng, $raidus);

        $where = [
            'lng >=' => $around['minLng'],
            'lng <=' => $around['maxLng'],
            'lat >=' => $around['minLat'],
            'lat <=' => $around['maxLat'],
            'uid !=' => $this->uid,
            'status' => 0,
        ];

        $data = \User::m()->getAll([
            'fields' => 'uid,realname,avatar,company,position,info,card_verify,lng,lat,supply_type,sell_info,buy_info,sex',
            'where' => $where,
            'limit' => 1000,
        ]);

        return $data;
    }

    /**
     * 附近的人数据处理
     * @param $data
     */
    public function formatNearbyUsers($data) {
        $fields = 'lng,lat';
        $user = $this->getInfo($this->uid, $fields);

        foreach ($data as &$value) {
            $value['distance'] = Func::getDistance($user['lng'], $user['lat'], $value['lng'], $value['lat']);
            unset($value['password']);

            $value['is_friend'] = \Friend::m()->isFriend($value['uid']);
        }

        usort($data, function($a, $b) {
            $al = strlen($a['distance']);
            $bl = strlen($b['distance']);
            if ($al == $bl)
                return 0;
            return ($al > $bl) ? -1 : 1;
        });

        return array_slice($data, 0, 200);
    }

}