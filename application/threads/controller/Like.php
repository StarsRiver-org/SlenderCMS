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
namespace qzxy\threads\Controller;

use qzxy\consoleboard\controller\Admincheck;
use qzxy\Thread;
use think\Controller;

class Like extends Controller{

    public function main(){
        return Thread::like($_GET['id']);
    }
}