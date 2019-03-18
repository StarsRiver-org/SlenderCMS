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
use app\Qhelp;
use app\common\controller\Template;
use think\Controller;

class More extends Controller{
	function _initialize() {
		new \app\Start();
	}
	
    public function main(){

        if(empty($_GET['sid']) || !Qhelp::chk_pint($_GET['sid'])){
            $this->error('参数不合法');
        }

        Log::visit("special","More","");
        $chunk = [
            'id'         =>   $_GET['sid'],
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

        if(empty($chunklist)){
            $this->error('内容不存在');
        }

        if(!empty($chunklist['chunk_lv2'])){
            foreach ($chunklist['chunk_lv2'] as $v) {
                $chr[$v['chunk_name']] = $v['id'];
            }
        }

        $chr['头条'] = $chunkid;

        $threadlist = Thread::loadlist($chr, 6);

        $this->assign([
            'threadlist' => $threadlist,
            'banners' => Chunk::loadbanner($chunkid),
        ]);
    }
}