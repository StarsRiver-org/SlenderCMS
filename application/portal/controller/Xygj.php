<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-10-05
 *
 */
namespace app\portal\Controller;
use app\Base;
use app\Log;
use app\Chunk;
use app\Thread;
use app\common\controller\Template;
use think\Controller;

class Xygj extends Controller{
	function _initialize() {
		new \app\Start();
	}
	
    public function main(){
        Log::visit("portal","xygj","");
        $chunk = [
            'id'         =>   9,
            'name'       =>   '清泽心雨 - 校园广角',
            'template'   =>   'portal/xygj/xygj',
        ];
        $this->loader($chunk['id']);
        $this->assign([
            'title' => $chunk['name'],
            'base' => Base::baseinfo(),
        ]);
        return Template::view($chunk['template']);
    }

    public function loader($chunkid){
        $threadlist = Thread::loadlist([
            'top'=>9,
            'show'=>35,
            'cultural'=>36,
            'hero'=>37,
            'story'=>38,
            'build'=>39,
            'teacher'=>40,
            'book'=>41,
            'interview'=>42
        ]);
        $this->assign([
            'threadlist' => $threadlist,
            'banners' => Chunk::loadbanner($chunkid),
        ]);
    }
}