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

class Paxy extends Controller{
    public function main(){
        Log::visit("portal","sztd","");
        $chunk = [
            'id'         =>   16,
            'name'       =>   '清泽心雨 - 平安校园',
            'template'   =>   'portal/paxy/paxy',
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
            'safe' => 51,
            'rule' => 52,
            'life' => 53,
            'web' => 54,
            'translation' => 55,
            'food' => 56,
            'selfsave' => 57,
            'fire' => 58,
        ]);
        $this->assign([
            'threadlist' => $threadlist,
            'banners' => Chunk::loadbanner($chunkid),
        ]);
    }
}