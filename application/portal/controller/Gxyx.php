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
use qzxy\common\controller\Admincheck;
use think\Controller;

class Gxyx extends Controller{
    public function main(){
        Log::visit("portal","gxyx","");
        $chunk = [
            'id'         =>   7,
            'name'       =>   '清泽心雨 - 国学研习',
            'template'   =>   'portal/gxyx/gxyx',
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
            'top'=>7,
            'new'=>26,
            'start'=>27,
            'dictionary'=>28,
            'history'=>29,
            'websource'=>30,
            'civilize'=>31,
            'forum'=>32,
            'dispute'=>33,
            'cr'=>34
        ]);
        $this->assign([
            'threadlist' => $threadlist,
            'banners' => Chunk::loadbanner($chunkid),
        ]);
    }
}