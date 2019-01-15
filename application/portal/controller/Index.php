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
namespace qzxy\portal\Controller;

use qzxy\Base;
use qzxy\Log;
use qzxy\Chunk;
use qzxy\Thread;
use qzxy\common\controller\Admincheck;
use think\Controller;

class Index extends Controller{

    public function main(){

        Log::visit("portal","home","");
        $chunk = [
            'id'         =>   1,
            'name'       =>   '太原理工大学 - 清泽心雨',
            'template'   =>   'portal/home/index',
        ];

        /*此处加载网站基本内容*/
        $this->assign([
            'title' => $chunk['name'],
            'base' => Base::baseinfo(),
        ]);

        /*此处加载板块的最新文章*/
        $this->loader($chunk['id']);

        /*渲染模板，输出*/
        return Admincheck::view($chunk['template']);
    }

    public function loader($chunkid){
        /* threadlist 可以通过两种方法获取，
         *   第一： Chunk::loadthread(板块id)d
         *         这种方法将会获取该板块及子版块内部的全部文章
         *
         *   第二： Thread::loadlist($arr)
         *         这种方法将获取某个板块下的最新20条文章，不会遍历子版块。
         *         $arr 可以是一维数组或者单值变量，如 3， [1,2,3,4,...],  [new => '1', hot => '2']
         * */
        $threadlist = Thread::loadlist([
            'top'=>1,
            'new'=>2,
            'info'=>3,
            'xwjj'=>17
        ]);

        $this->assign([
            'new' => isset($threadlist['new']) ? $threadlist['new'] : [],
            'info' => isset($threadlist['info']) ? $threadlist['info'] : [],
            'sp' => Chunk::load_specials([85,68,71,81]), //这里的id是专题 id
            'xwjj' => isset($threadlist['xwjj']) ? $threadlist['xwjj'] : [],
            'banners' => Chunk::loadbanner($chunkid),
        ]);
    }

}