<?php

namespace App\Http\Controllers;

class SearchController extends Controller
{

    /**
     * 群搜索
     */
    public function group()
    {
        $keyword = request('keyword');
        $page = request('page');
        $data = \Group::m()->getAll([
            'where' => [
                'status' => 0,
                "concat(name,info) LIKE '%" . $keyword . "%'",
            ],
            'order' => 'member_count DESC',
            'limit' => [$page, 100],
        ]);

        $this->success(lang(20000), $data);
    }

    /**
     * 动态搜索
     */
    public function dynamic()
    {
        $keyword = request('keyword');
        $page = request('page');
        $data = \Dynamic::m()->getAll([
            'where' => [
                'status' => 0,
                "content LIKE '%" . $keyword . "%'",
                //时间一个月内
                'created_at >' => time() - 2592000,
            ],
            'order' => 'created_at DESC',
            'limit' => [$page, 100],
        ], true);

        $this->success(lang(20000), $data);
    }

}
