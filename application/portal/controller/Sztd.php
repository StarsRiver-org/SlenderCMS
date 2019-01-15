<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-10-03
 *
 */


namespace qzxy\portal\Controller;
use qzxy\Base;
use qzxy\Log;
use qzxy\Chunk;
use qzxy\Thread;
use think\Controller;
use qzxy\common\controller\Admincheck;

class Sztd extends Controller{
    public function main(){
        Log::visit("portal","sztd","");
        $chunk = [
            'id'         =>   6,
            'name'       =>   '清泽心雨 - 思政天地',
            'template'   =>   'portal/sztd/sztd',
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
            'new' => 17,
            'llts' => 20,
            'sdkm' => 23,
            'ddzs' => 24,
            'lkjt' => 59,
            'dsdj' => 60,
            'gfjy' => 63,
        ]);
        $this->assign([
            'threadlist' => $threadlist,
            'banners' => Chunk::loadbanner($chunkid),
        ]);
    }
}