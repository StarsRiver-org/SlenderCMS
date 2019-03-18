<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-10-02
 *
 */
namespace app\threads\Controller;

use app\consoleboard\controller\Admincheck;
use app\Thread;
use think\Controller;

class Like extends Controller{
	
	function _initialize() {
		new \app\Start();
	}
	
    public function main(){
        return Thread::like($_GET['id']);
    }
}