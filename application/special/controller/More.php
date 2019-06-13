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

use think\Controller;
use app\common\controller\Base;
use app\common\Thread;
use app\common\Chunk;
use app\common\Log;
use app\common\Qhelp;
use app\common\Template;

class More extends Controller {
    function _initialize() {
        new \app\Start();
    }

    public function main() {

        if (empty($_GET['sid']) || !Qhelp::chk_pint($_GET['sid'])) {
            $this->error('参数不合法');
        }

        Log::visit("special", "More", "");
        $chunk = ['id' => $_GET['sid'], 'name' => '清泽心雨 - 热点凝眸', 'template' => 'special/sptpl',];
        $this->loader($chunk['id']);
        $this->assign(['title' => $chunk['name'], 'base' => Base::baseinfo(),]);
        return Template::view($chunk['template']);
    }

    public function loader($chunkid) {
        $chunklist = Chunk::loadchunk($chunkid);

        if (empty($chunklist)) {
            $this->error('内容不存在');
        }

        if (!empty($chunklist['chunk_lv2'])) {
            foreach ($chunklist['chunk_lv2'] as $v) {
                $chr[$v['chunk_name']] = $v['id'];
            }
        }

        $chr['头条'] = $chunkid;

        $threadlist = Thread::loadlist($chr, 6);

        $this->assign(['threadlist' => $threadlist, 'banners' => Chunk::loadbanner($chunkid),]);
    }
}