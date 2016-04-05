<?php

use \Illuminate\Database\Eloquent\Model;

class Dynamic extends Model
{
    protected $_tableName = 'tb_dynamic';

    private static $last_personal_date = '';

    protected function _format(&$row)
    {
        $fields = 'realname,avatar,company,position,info,card_verify,supply_type,sex,background';
        $row['user'] = \User::m()->getInfo($row['uid'], $fields);
        $row['images'] = empty($row['images']) ? [] : explode(',', $row['images']);

        $row['reply'] = \DynamicReply::m()->getAll([
            'where' => ['did' => $row['id'], 'status' => 0],
            'order' => 'created_at',
        ]);
    }

    protected function _format_personal(&$row) {
        $row['images'] = empty($row['images']) ? [] : explode(',', $row['images']);
        $date = substr($row['created_at'], 0, 10);
        if (self::$last_personal_date != $date) {
            self::$last_personal_date = $date;
            $row['date'] = $date;
        } else {
            $row['date'] = '';
        }
    }



}