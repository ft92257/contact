<?php

use \Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $_tableName = 'tb_group';

    /**
     * 获取当前用户已创建群数量
     */
    public function getCreatedCount()
    {
        return $this->getCount([
            'uid'    => $this->uid,
            'status' => 0,
        ]);
    }

}