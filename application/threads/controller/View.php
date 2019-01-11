<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-08-21
 *
 */
namespace qzxy\threads\Controller;
use qzxy\Chunk;
use qzxy\Base;
use qzxy\Thread;
use qzxy\Qhelp;
use think\Controller;

class View extends Controller{
    public function main(){
        if(!empty($_GET['id']) && Qhelp::chk_pint($_GET['id'])){
            $thread = Thread::loadone($_GET['id']);
            if($thread){
                Thread::visit($_GET['id']);
                $this->assign([
                    'thread'=>$thread,
                    'new'=>Thread::newest($thread['sort']),
                    'base' => Base::baseinfo(),
                ]);
                if(!empty($_GET['from']) && Qhelp::chk_pint($_GET['from'])){
                    $this->assign(['banners'=>Chunk::loadbanner($_GET['from'])]);
                }
                return view('portal/thread_article'); /*这里是文章显示模板，可用于自定义你的模板文件*/
            } else {
                $this->error('您要查看的内容不存在');
            }
        }
        $this->error('您要查看的内容不存在');
    }
}