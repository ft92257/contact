<?php
namespace App\Http\Controllers;
use App\Core\File;

class RichtextController extends Controller
{
	protected $_authlevel = 0;
    /**
     * 上传
     */
	public function uploadAction() {
	    $info = File::m()->upload('imgFile');
	    $arr = array(
	            'error' => $info['status'],
	    );
	    if ($info['status'] != 0) {
	        $arr['message'] = $info['msg'];
	    } else {
	        $arr['url'] = $info['data']['url'];
	    }

	    die(json_encode($arr));
	}
	
}
