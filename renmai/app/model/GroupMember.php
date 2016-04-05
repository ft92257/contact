<?php

use \Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    protected $_tableName = 'tb_group_member';

    protected function _format(&$row)
    {
        $fields = 'realname,avatar,company,position,info,card_verify,supply_type,sex,background';
        $user = \User::m()->getInfo($row['uid'], $fields);
        $row = array_merge($row, $user);
    }

    protected function _get_group(&$row)
    {
        $row = \Group::m()->getById($row['gid']);
    }
}