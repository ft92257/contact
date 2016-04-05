<?php

use \Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $_tableName = 'tb_report';

    protected $aOptions = [
        'type' => [
            '' => '', '1' => '动态', '2' => '名片', '3' => '公司成员',
        ],
        'status' => [
            '' => '', '0' => '待处理', '1' => '核实中', '2' => '已处理' , '3' => '不通过',
        ],
        'order' => ['' => '', '1' => '由高到低', '2' => '由低到高'],
    ];

    protected $listConfig = [
        'number' => '编号',
        'informer' => '举报人ID',
        'defendant' => '被举报人ID',
        'type' => '举报类型',
        'content' => '举报内容',
        'created_at' => '举报时间',
        'count' => '被举报次数',
        'status' => '进度',
        ['处理', 'func' => 'edit'],
    ];

    protected $searchConfig = [
        'type' => ['举报类型', 'select'],
        'order' => ['被举报次数', 'select'],
        'status' => ['进度', 'select'],
        'number' => ['编号', 'text'],
        [' ', 'submit'],
    ];

    protected function _after_search($search) {
        unset($search['order']);

        return $search;
    }

    protected $formConfig = [
        'status' => ['状态', 'select'],
        'reason' => ['原因', 'textarea'],
    ];

}