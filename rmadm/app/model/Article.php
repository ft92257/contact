<?php

use \Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $_tableName = 'tb_article';

    protected $formConfig = [
        'content' => ['内容', 'richtext'],
    ];
}