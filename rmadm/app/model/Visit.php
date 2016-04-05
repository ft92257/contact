<?php

use \Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $_tableName = 'tb_visit';

    protected function _format(&$row)
    {
        $row['user'] = \User::m()->getInfo($row['vuid']);
    }
}