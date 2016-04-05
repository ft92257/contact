<?php

namespace App\Http\Controllers;

class DynamicController extends Controller
{
    protected $_authlevel = 0;

    public function __construct() {
        parent::__construct();

        $this->model = \Dynamic::m();
    }

    public function indexAction() {
        $this->_getPageList([
            'where' => ['status !=' => 1],
        ]);
    }

    public function deleteAction() {
        $id = request('id');

        $this->_delete(['id' => $id]);
    }
}
