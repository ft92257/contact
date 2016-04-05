<?php

use \Illuminate\Database\Eloquent\Model;

class Dynamic extends Model
{
    protected $_tableName = 'tb_dynamic';

    protected $aOptions = [
        'type' => ['1' => '动态ID', '2' => '会员ID'],
    ];

    public function __construct() {
        parent::__construct();

        $this->aOptions['industry'] = UserIndustry::m()->getOptions('industry');
    }

    protected $listConfig = [
        'id' => '动态ID',
        'uid' => '会员ID',
        'industry' => '行业',
        'content' => '内容',
        'images' => '图片',
        'created_at' => '产出时间',
        ['处理', 'func' => 'delete'],
    ];

    protected function _format(&$row) {
        //获取行业
        $result = DynamicIndustry::m()->getAll([
            'fields' => 'industry_id',
            'where' => ['did' => $row['id'], 'status' => 0],
        ]);
        $industry = [];
        foreach ($result as $value) {
            $industry[] = UserIndustry::m()->getOptions('industry', $value['industry_id']);
        }
        $row['industry'] = join('<br>', $industry);

        //处理图片信息
        if (!empty($row['images'])) {
            $images = explode(',', $row['images']);
            $imgHtml = '';
            foreach ($images as $img) {
                $imgHtml .= '<img src="'.$img.'" width=80 style="margin-right:10px;" />';
            }
            $row['images'] = $imgHtml;
        }
    }

    protected $searchConfig = [
        'type' => ['', 'select'],
        'type_cont' => ['', 'text', 'params' => ['exact' => true]],
        'industry' => ['', 'select'],
        'created_at' => ['产出时间', 'datetime', 'params' => ['type' => 'date']],
        ['', 'submit'],
    ];

    protected function _after_search($search) {
        if (isset($search['type_cont']) && $search['type_cont']) {
            if ($search['type'] == 1) {
                $search['id'] = $search['type_cont'];
            } else {
                $search['uid'] = $search['type_cont'];
            }
        }
        unset($search['type']);
        unset($search['type_cont']);

        if (Func::KV($search, 'industry')) {
            $dyns = \DynamicIndustry::m()->getAll([
                'fields' => 'DISTINCT did',
                'where' => ['industry_id' => $search['industry']],
                'limit' => 1000,
                'order' => 'created_at DESC',
            ]);
            $dids = \Func::pickArrayField($dyns, 'did');
            if (!empty($dids)) {
                $search['id in'] = $dids;
            } else {
                $search['id'] = 0;
            }
        }
        unset($search['industry']);

        return $search;
    }
}