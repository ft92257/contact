<?php

namespace App\Http\Controllers;

class ArticleController extends Controller
{
    protected $_authlevel = 0;

    public function __construct() {
        parent::__construct();

        $this->model = \Article::m();
    }

    public function aboutAction() {
        $data = $this->model->getById(1);
        if (empty($data)) {
            $this->error('没有初始记录！');
        }
        if ($this->isPost()) {
            $this->_edit(['id' => 1]);
        } else {
            $this->_display_form($data);
        }
    }

    public function serviceAction() {
        $data = $this->model->getById(2);
        if (empty($data)) {
            $this->error('没有初始记录！');
        }
        if ($this->isPost()) {
            $this->_edit(['id' => 2]);
        } else {
            $this->_display_form($data);
        }
    }

}
