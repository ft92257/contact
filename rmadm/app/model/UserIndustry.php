<?php

use \Illuminate\Database\Eloquent\Model;

class UserIndustry extends Model
{
    protected $_tableName = 'tb_user_industry';

    const TYPE_BELONG = 1;//从属的行业
    const TYPE_ATTENTION = 2;//关注的行业

    protected $aOptions = [
        'industry' => ['' => '所有行业', '1' => '行业1', '2' => '行业2', '3' => '行业3', '4' => '行业4', '5' => '行业5'],
    ];

    /**
     * 更新行业信息
     */
    public function updateIndustry($industry, $type, $uid)
    {

        $arr = $this->getAll([
            'where' => ['uid' => $uid, 'type' => $type],
        ]);
        $existIds = Func::pickArrayField($arr, 'industry_id');
        $ids = explode(',', $industry);
        $needCreate = [];
        $needUpdate = [];
        foreach ($ids as $id) {
            if (in_array($id, $existIds)) {
                $needUpdate[] = $id;
            } else {
                $needCreate[] = $id;
            }
        }

        $needDelete = array_diff($existIds, $needUpdate);

        if (!empty($needCreate)) {
            foreach ($needCreate as $id) {
                $this->addData([
                    'uid'         => $uid,
                    'industry_id' => $id,
                    'type'        => $type,
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ]);
            }
        }
        if (!empty($needUpdate)) {
            $this->updateData(['status' => 0, 'updated_at' => date('Y-m-d H:i:s')], [
                'uid'            => $uid,
                'type'           => $type,
                'industry_id in' => $needUpdate,
            ]);
        }
        if (!empty($needDelete)) {
            $this->deleteData([
                'uid'            => $uid,
                'type'           => $type,
                'industry_id in' => $needDelete,
            ]);
        }
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

}