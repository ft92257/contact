<?php

namespace App\Http\Controllers;

class OtherController extends Controller
{
    protected $_authlevel = 0;

    /**
     * 用户反馈
     */
    public function feedback() {
        $content = request('content');
        if ($this->uid) {
            $mobile = \User::m()->getField(['uid' => $this->uid], 'mobile');
        } else {
            $mobile = '';
        }

        $data = [
            'uid' => $this->uid,
            'mobile' => $mobile,
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        \Feedback::m()->addData($data);

        $this->success(lang(20013));
    }

    /**
     * 举报
     */
    public function report() {
        $type = request('type');
        $source_id = request('source_id');
        $content = request('content');

        $count = \Report::m()->getCount([
            'created_at >' => mktime(0, 0, 0),
            'status' => 0,
        ]);
        $number = 'JB' . date('ymd') . ($count + 1);

        if ($type == 1) {
            $defendant = \Dynamic::m()->getField(['id' => $source_id], 'uid');
        } else {
            $defendant = $source_id;
        }
        $data = [
            'type' => $type,
            'source_id' => $source_id,
            'content' => $content,
            'number' => $number,
            'informer' => $this->uid,
            'defendant' => $defendant,
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ];
        \Report::m()->addData($data);

        $where = [
            'type' => $type,
            'source_id' => $source_id,
            'status' => 0,
        ];
        $sameCount = \Report::m()->getCount($where);
        \Report::m()->updateData(['count' => $sameCount + 1], $where);

        $this->success(lang(20013));
    }

    /**
     * 关于我们
     */
    public function about() {
        $ret = [
            'html' => \Article::m()->getField(['id' => 1], 'content'),
        ];

        $this->success(lang(20000), $ret);
    }

    /**
     * 服务条款
     */
    public function service() {
        $ret = [
            'html' => \Article::m()->getField(['id' => 2], 'content'),
        ];

        $this->success(lang(20000), $ret);
    }
}
