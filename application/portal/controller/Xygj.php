<?php
namespace qzxy\portal\Controller;
use qzxy\Base;
use qzxy\Log;
use qzxy\Chunk;
use qzxy\Thread;
use think\Controller;

class Xygj extends Controller{
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
        return view($chunk['template']);
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