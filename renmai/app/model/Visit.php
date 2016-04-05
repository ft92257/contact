<?php

use \Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $_tableName = 'tb_visit';

    protected function _format(&$row)
    {
        $fields = 'realname,avatar,company,position,info,card_verify,supply_type,sex';
        $row = array_merge($row, \User::m()->getInfo($row['vuid'], $fields));

        $row['is_friend'] = \Friend::m()->isFriend($row['vuid']);
    }

    /**
     * 添加访问记录
     * @param $uid
     */
    public function addRecord($uid) {
        $where = [
            'uid' => $this->uid,
            'vuid' => $uid,
        ];
        if ($this->isExists($where)) {
            $set = [
                'updated_at' => date('Y-m-d H:i:s'),
                'status' => 0,
            ];
            $this->updateData($set, $where);
        } else {
            $data = [
                'uid' => $this->uid,
                'vuid' => $uid,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->addData($data);
        }
    }
}