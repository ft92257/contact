<?php

use \Illuminate\Database\Eloquent\Model;

class TmpList extends Model
{
    protected $_tableName = 'tb_tpm_list';

    const TYPE_INDUSTRY = 1;//行业动态
    const TYPE_FRIEND = 2;//好友动态

    /**
     * 获取数据
     * @param $type
     * @return array
     */
    public function getListIds($type)
    {
        $ids = \TmpList::m()->getField(['uid' => $this->uid, 'type' => \TmpList::TYPE_INDUSTRY], 'data');
        if (empty($ids)) {
            return [];
        } else {
            return explode(',', $ids);
        }
    }

    /**
     * 保存数据
     * @param $type
     * @param $ids
     * @return bool|number
     */
    public function saveListIds($type, $ids)
    {
        $data = [
            'uid'        => $this->uid,
            'type'       => $type,
            'data'       => join(',', $ids),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        return $this->addData($data);
    }
}