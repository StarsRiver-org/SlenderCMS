<?php



namespace qzxy\portal\Controller;
use qzxy\Base;
use qzxy\Log;
use qzxy\Chunk;
use qzxy\Thread;
use think\Controller;
use think\Db;

class Paxy extends Controller{
    public function main(){
        Log::visit("portal","sztd","");
        $chunk = [
            'id'         =>   16,
            'name'       =>   '清泽心雨 - 平安校园',
            'template'   =>   'portal/paxy/paxy',
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
            'safe' => 51,
            'rule' => 52,
            'life' => 53,
            'web' => 54,
            'translation' => 55,
            'food' => 56,
            'selfsave' => 57,
            'fire' => 58,
        ]);
        $this->assign([
            'threadlist' => $threadlist,
            'banners' => Chunk::loadbanner($chunkid),
        ]);
    }
}