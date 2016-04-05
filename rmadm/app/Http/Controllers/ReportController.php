<?php

namespace App\Http\Controllers;

class ReportController extends Controller
{
    protected $_authlevel = 0;

    public function __construct() {
        parent::__construct();

        $this->model = \Report::m();
    }

    /**
     * 列表
     */
    public function indexAction()
    {
        $ord = request('order');
        if ($ord == 1) {
            $order = 'count DESC,created_at DESC';
        } elseif ($ord == 2) {
            $order = 'count,created_at DESC';
        } else {
            $order = 'created_at DESC';
        }

        $this->_getPageList([
            'order' => $order,
        ]);
    }

    /**
     * 编辑
     */
    public function editAction() {
        $id = request('id');
        $data = $this->model->getById($id);
        if (empty($data)) {
            $this->error('没有该数据');
        }

        if ($this->isPost()) {
            $where = [
                'type' => $data['type'],
                'source_id' => $data['source_id'],
            ];
            $this->_edit($where);
        } else {
            if ($data['type'] == 1) {
                $data['source'] = \Dynamic::m()->getById($data['source_id']);
                if (!empty($data['source']['images'])) {
                    $data['source']['images'] = explode(',', $data['source']['images']);
                } else {
                    $data['source']['images'] = [];
                }
            } else {
                //TODO
            }

            $this->_display_form($data);
        }
    }
}
