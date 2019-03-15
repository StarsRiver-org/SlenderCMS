<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-08-05
 *
 */
namespace app\special\Controller;
use app\Base;
use app\Thread;
use app\Chunk;
use app\Log;
use app\common\controller\Template;
use think\Controller;

class Hottopic extends Controller{
    public function main(){
        Log::visit("special","hottopic","");
        $chunk = [
            'id'         =>   '6',
            'name'       =>   '清泽心雨 - 热点凝眸',
            'template'   =>   'special/sptpl',
        ];
        $this->loader($chunk['id']);
        $this->assign([
            'title' => $chunk['name'],
            'base' => Base::baseinfo(),
        ]);
        return Template::view($chunk['template']);
    }

    public function loader($chunkid) {
        $chunklist = Chunk::loadchunk($chunkid);
        $chr['头条'] = $chunkid;
        foreach ($chunklist['chunk_lv2'] as $v) {
            $chr[$v['chunk_name']] = $v['id'];
        }
        $threadlist = Thread::loadlist($chr, 6);
        $this->assign([
            'threadlist' => $threadlist,
            'banners' => Chunk::loadbanner($chunkid),
        ]);
    }
}