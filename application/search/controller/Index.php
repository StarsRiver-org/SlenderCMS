<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-11-24
 *
 */
namespace qzxy\search\controller;
use qzxy\Base;
use qzxy\Qhelp;
use qzxy\Qpage;
use qzxy\Thread;
use think\Controller;
use qzxy\Search;
use qzxy\common\controller\Admincheck;

class Index extends Controller{
    public function main() {

        $page = (int)Qhelp::receive('page',1) ;
        $perpage = (int)Qhelp::receive('perpage',15);

        if(
            !Qhelp::chk_pint($page) || $page < 1 ||
            !Qhelp::chk_pint($perpage) || $perpage < 5 || $perpage > 30
        ){
            $this->error('无法理解的搜索语句...');
        }

        $keyword = Qhelp::receive('keyword','');

        $res = Search::getall($page, $perpage,$keyword,'desc','thread_ptime');
        $data = [];
        for($i = 0; $i < count($res['res']); $i++){
            $data[$i] = Thread::format($res['res'][$i],'more');
            $data[$i]['title'] = str_replace($keyword,"<em>$keyword</em>",$data[$i]['title']);
            $data[$i]['descrip'] = str_replace($keyword,"<em>$keyword</em>",$data[$i]['descrip']);
        }
        $count = $res['count'];
        $pages = ceil($count/$perpage);
        $url = "search?keyword=$keyword&page=";

        if($page > $pages && $res['count'] !== 0){
            return "<script>location.href='".$url."1';</script>";
        }
        $this->assign([
            'count' => $count,
            'thread_list' => $data,
            'keyword' => $keyword,
            'multipage' => Qpage::page($pages,$page,$url),
            'new' => Thread::newest(),
			'base' => Base::baseinfo(),
        ]);
        return Admincheck::view('search/search');
    }
}