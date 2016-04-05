<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
/*
$app->get('/', function () use ($app) {
    return $app->version();
});*/

$info = Func::getPathInfo();
$c = $info['app'];
$a = $info['action'];

$app->get('/' . $c . '/' . $a, ucfirst($c) . 'Controller@' . $a);
$app->post('/' . $c . '/' . $a, ucfirst($c) . 'Controller@' . $a);
