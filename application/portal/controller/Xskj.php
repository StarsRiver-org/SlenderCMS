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

namespace qzxy\portal\Controller;

use qzxy\Base;
use qzxy\Log;
use qzxy\Chunk;
use qzxy\Thread;
use think\Controller;
use qzxy\common\controller\Admincheck;


class Xskj extends Controller{
    public function main(){
        Log::visit("portal","xskj","");
        $chunk = [
            'id'         =>   10,
            'name'       =>   '清泽心雨 - 学术科技',
            'template'   =>   'portal/xskj/xskj',
        ];
        $this->loader($chunk['id']);
        $this->assign([
            'title' => $chunk['name'],
            'base' => Base::baseinfo(),
        ]);
        return Admincheck::view($chunk['template']);
    }

    public function loader($chunkid){
        $threadlist = Thread::loadlist([
            'new' => 43,
            'notice' => 44,
            'gzzd' => 45,
            'kjjs' => 46,
            'kjjs1' => 64,
            'kjjs2' => 65,
            'kjjs3' => 66,
            'jdjs' => 47,
            'sztd' => 48,
            'kjcg' => 49,
            'fyrw' => 50,
            'qzxjt' => 70,
        ]);
        $this->assign([
            'threadlist' => $threadlist,
            'banners' => Chunk::loadbanner($chunkid),
        ]);
    }
}