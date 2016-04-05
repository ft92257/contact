<?php

use \Illuminate\Database\Eloquent\Model;

class UserLastview extends Model
{
    protected $_tableName = 'tb_user_lastview';

    /**
     * 获取数据
     * @return array
     */
    public function getData()
    {
        $data = $this->getById($this->uid, 'uid');
        if (empty($data)) {
            $data = [
                'uid' => $this->uid,
                'industry_dynamic' => '0000-00-00 00:00:00',
                'friend_dynamic' => '0000-00-00 00:00:00',
                'new_friend' => '0000-00-00 00:00:00',
                'visit' => '0000-00-00 00:00:00',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $this->addData($data);
        }

        return $data;
    }

    /**
     * 设置字段值
     * @param $set
     */
    public function setData($set)
    {
        $set['updated_at'] = date('Y-m-d H:i:s');
        $this->updateData($set, ['uid' => $this->uid]);
    }

}