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
namespace qzxy\special\Controller;
use qzxy\Base;
use qzxy\Thread;
use qzxy\Chunk;
use qzxy\Log;
use qzxy\common\controller\Admincheck;
use think\Controller;

class Studentscenter extends Controller{
    public function main(){
        Log::visit("special","studentscenter","");
        $chunk = [
            'id'         =>   '6',
            'name'       =>   '清泽心雨 - 以学生为中心',
            'template'   =>   'special/sptpl',
        ];
        $this->loader($chunk['id']);
        $this->assign([
            'title' => $chunk['name'],
            'base' => Base::baseinfo(),
        ]);
        return Admincheck::view($chunk['template']);
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