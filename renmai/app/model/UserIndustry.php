<?php

use \Illuminate\Database\Eloquent\Model;

class UserIndustry extends Model
{
    protected $_tableName = 'tb_user_industry';

    const TYPE_BELONG = 1;//从属的行业
    const TYPE_ATTENTION = 2;//关注的行业

    protected $aOptions = [
        'industry' => ['1' => '行业1', '2' => '行业2', '3' => '行业3', '4' => '行业4', '5' => '行业5'],
    ];

    /**
     * 更新行业信息
     */
    public function updateIndustry($industry, $type)
    {
        $arr = $this->getAll([
            'where' => ['uid' => $this->uid, 'type' => $type],
        ]);
        $existIds = Func::pickArrayField($arr, 'industry_id');
        $ids = explode(',', $industry);
        $needCreate = [];
        $needUpdate = [];
        foreach ($ids as $id) {
            if (isset($this->aOptions['industry'][$id])) {
                if (in_array($id, $existIds)) {
                    $needUpdate[] = $id;
                } else {
                    $needCreate[] = $id;
                }
            }
        }

        $needDelete = array_diff($existIds, $needUpdate);

        if (!empty($needCreate)) {
            foreach ($needCreate as $id) {
                $this->addData([
                    'uid'         => $this->uid,
                    'industry_id' => $id,
                    'type'        => $type,
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ]);
            }
        }
        if (!empty($needUpdate)) {
            $this->updateData(['status' => 0, 'updated_at' => date('Y-m-d H:i:s')], [
                'uid'            => $this->uid,
                'type'           => $type,
                'industry_id in' => $needUpdate,
            ]);
        }
        if (!empty($needDelete)) {
            $this->deleteData([
                'uid'            => $this->uid,
                'type'           => $type,
                'industry_id in' => $needDelete,
            ]);
        }
    }

    protected function _format(&$row)
    {
        $uid = $row['uid'];
        $fields = 'uid,realname,avatar,company,position,info,card_verify,supply_type,sell_info,buy_info,sex';
        $row = \User::m()->getInfo($row['uid'], $fields);
        $row['is_friend'] = \Friend::m()->isFriend($uid);
    }

    /**
     * 获取用户行业ids
     * @param $uid
     */
    public function getIndustryIds($uid)
    {
        $data = $this->getAll([
            'fields' => 'industry_id',
            'where'  => [
                'uid'    => $uid,
                'type'   => self::TYPE_BELONG,
                'status' => 0,
            ],
        ]);

        return Func::pickArrayField($data, 'industry_id');
    }

    /**
     * 获取结果数据
     * @param $data
     * @return array
     */
    public function getFormatData($data)
    {
        $ret = [];
        foreach ($data as $value) {
            $fields = 'uid,realname,avatar,company,position,info,card_verify,supply_type,sell_info,buy_info,sex';
            $ret[] = \User::m()->getInfo($value['uid'], $fields);
        }

        return $ret;
    }
}