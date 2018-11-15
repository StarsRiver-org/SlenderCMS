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
namespace qzxy;
use think\Controller;
use think\Db;

class Search extends Controller{
    /* 搜索 */
    public static function getall($page , $perpage, $search_keyword, $search_order, $search_orderby) {
        $keyword = htmlspecialchars($search_keyword,ENT_QUOTES);
        $from = ($page-1)*$perpage - 1 >=0 ? ($page-1)*$perpage : 0 ;
        $res = Db::query("select * from qzlit_thread WHERE `hk_mode`='2' AND (thread_title like '%". $keyword ."%' OR thread_context like '%". $keyword ."%' OR hk_descrip like '%". $keyword ."%') ORDER BY ".$search_orderby." ".$search_order." limit ".(int)$from.",".(int)$perpage);
        $count = count(Db::query("select cuid from qzlit_thread WHERE `hk_mode`='2' AND (thread_title like '%". $keyword ."%' OR thread_context like '%". $keyword ."%' OR hk_descrip like '%". $keyword ."%')"));
        return [
            'res' => $res,
            'count' => $count
        ];
    }
}