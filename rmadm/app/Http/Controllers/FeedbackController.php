<?php

namespace App\Http\Controllers;

class FeedbackController extends Controller
{
    protected $_authlevel = 0;

    public function __construct() {
        parent::__construct();

        $this->model = \Feedback::m();
    }

    public function indexAction() {
        $this->_getPageList();
    }

}
