<?php

use \Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $_tableName = 'tb_group';

    protected $aOptions = [
        'verify_type' => [
            '0' => '允许任何人加入',
            '1' => '需身份认证',
            '2' => '不允许任何人加入',
        ],
    ];

    protected $formConfig = [
        'name' => ['名称', 'text', '请填写群的名称'],
        'info' => ['简介', 'richtext'],
        'pic' => ['图片', 'image'],
        'verify_type' => ['验证类型', 'checkbox'],
        'created_at' => ['时间', 'datetime', 'params' => ['type' => 'date']],
    ];

}