<?php

namespace App\Http\Controllers;
use App\Core\File;
class IndexController extends Controller
{
    protected $_authlevel = 0;

    public function __construct() {
        parent::__construct();

        $this->model = \Group::m();
    }

    public function indexAction() {
        echo 'hello!';
    }

}
