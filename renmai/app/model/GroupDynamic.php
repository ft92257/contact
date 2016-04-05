<?php

use \Illuminate\Database\Eloquent\Model;

class GroupDynamic extends Model
{
    protected $_tableName = 'tb_dynamic_group';

    protected function _format(&$row)
    {
        $row = \Dynamic::m()->getById($row['did']);

        $fields = 'realname,avatar,company,position,info,card_verify,supply_type,sex,background';
        $row['user'] = \User::m()->getInfo($row['uid'], $fields);
        $row['images'] = empty($row['images']) ? [] : explode(',', $row['images']);

        $row['reply'] = \DynamicReply::m()->getAll([
            'where' => ['did' => $row['id'], 'status' => 0],
            'order' => 'created_at',
        ]);
    }

}