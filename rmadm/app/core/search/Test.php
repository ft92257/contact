<?php
/**
 * Created by PhpStorm.
 * User: xiang.chen
 * Date: 2015/11/26
 * Time: 15:32
 * php Test.php --q="q" --page=1 --pagesize=100
 */
$argv		= arguments($argv);
$q	        = isset($argv['q']) ? $argv['q'] : '';
$page	    = isset($argv['page']) ? $argv['page'] : 1;
$pagesize	= isset($argv['pagesize']) ? $argv['pagesize'] : 10;

if(!$q) {
    dlog("no q!");
    exit();
}

require_once __DIR__."/Search.php";
$search = Search::getInstance();
$data   = $search->searchMol($q, $page, $pagesize);
dlog("search result:");
dlog("data:"  . (is_array($data['data']) ? implode(",",$data['data']) : ""));
dlog("total:" . (!empty($data['total'])  ? $data['total'] : ""));
dlog("start:" . (!empty($data['start'])  ? $data['start'] : 0));
dlog("limit:" . (!empty($data['limit'])  ? $data['limit'] : ""));
dlog("time:"  . (!empty($data['time'])   ? $data['time']  : 0));





function dlog($str,$addtime = true) {
    if($addtime) {
        echo date("Y-m-d H:i:s") . "=>" . $str . "\r\n";
    } else {
        echo $str . "\r\n";
    }
}

function arguments($argv) {
    $ret = array();
    foreach($argv as $arg) {
        if(preg_match("/--([^=]+)=(.*)/",$arg,$matches)) {
            $ret[$matches[1]] = $matches[2];
        } else if(preg_match("/^-([a-zA-Z0-9])/is",$arg,$matches)) {
            $ret[$matches[1]] = "true";
        }
    }
    return $ret;
}