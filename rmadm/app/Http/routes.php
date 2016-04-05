<?php

$app = 'index';
$action = 'index';
if (isset($_SERVER['PATH_INFO'])) {
    $path = $_SERVER['PATH_INFO'];
} else {
    if (!isset($_SERVER['REQUEST_URI'])) {
        exit('NO REQUEST_URI');
    }
    $arr = explode('?', $_SERVER['REQUEST_URI']);
    $path = $arr[0];
}
$aPath = explode('/', $path);
if (isset($aPath[1]) && $aPath[1]) {
    $app = $aPath[1];
}
if (isset($aPath[2]) && $aPath[2]) {
    $action = $aPath[2];
}

define('CONTROLLER', $app);
define('ACTION', $action);

Route::any('/', 'IndexController@indexAction');
Route::any('/'.$app.'/'.$action, ucfirst($app) .'Controller@' . $action . 'Action');


