<?php
namespace qzxy\special\Controller;
use qzxy\Base;
use qzxy\Thread;
use qzxy\Chunk;
use qzxy\Log;
use qzxy\Qhelp;
use think\Controller;

class More extends Controller{
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
        return view($chunk['template']);
    }

    public function loader($chunkid) {
        $chunklist = Chunk::loadchunk($chunkid);

        if(empty($chunklist)){
            $this->error('内容不存在');
        }

        if(!empty($chunklist['chunk_lv2'])){
            $chr['头条'] = $chunkid;
            foreach ($chunklist['chunk_lv2'] as $v) {
                $chr[$v['chunk_name']] = $v['id'];
            }
            $threadlist = Thread::loadlist($chr, 6);
        }
        $this->assign([
            'threadlist' => $threadlist,
            'banners' => Chunk::loadbanner($chunkid),
        ]);
    }
}