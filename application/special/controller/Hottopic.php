<?php
namespace qzxy\special\Controller;
use qzxy\Base;
use qzxy\Thread;
use qzxy\Chunk;
use qzxy\Log;
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
        return view($chunk['template']);
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