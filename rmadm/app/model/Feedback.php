<?php

use \Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $_tableName = 'tb_feedback';

    protected $listConfig = [
        'uid' => '用户ID',
        'mobile' => '手机号',
        'content' => '反馈内容',
        'created_at' => '反馈时间',
    ];

    protected $searchConfig = [
        'created_at' => ['反馈时间', 'datetime', 'params' => ['type' => 'date']],
        ['', 'submit'],
    ];
}