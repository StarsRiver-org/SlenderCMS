<?php
namespace qzxy\threads\Controller;
use qzxy\Chunk;
use qzxy\Base;
use qzxy\Thread;
use qzxy\Qhelp;
use think\Controller;

class View extends Controller{
    public function main(){
        if(!empty($_GET['id']) && Qhelp::chk_pint($_GET['id'])){
            Thread::visit($_GET['id']);
            $this->assign([
                'thread'=>Thread::loadone($_GET['id']),
                'new'=>Thread::newest(),
                'base' => Base::baseinfo(),
            ]);
            if(!empty($_GET['from']) && Qhelp::chk_pint($_GET['from'])){
                $this->assign(['banners'=>Chunk::loadbanner($_GET['from'])]);
            }
            return view('common/common_article'); /*这里是文章显示模板，可用于自定义你的模板文件*/
        }
        $this->error('您要查看的内容不存在');
    }
}