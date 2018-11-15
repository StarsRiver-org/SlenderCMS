<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-12-18
 *
 */
namespace qzxy;

class Qpage{

    /* 分页助手， 参数分别为 总页数，当前页，url匹配
     * */

    public static function page($pages, $page, $url){
        if($pages != 1){
            $prew = $page != 1 ? $page - 1 : '';
            $next = $page != $pages ? $page + 1 : '';
            $isstart = $isend = '';
            if($page <= 1) {$isstart = 'disabled';}
            if($page >= $pages) {$isend = 'disabled';}
            $tmp = [
                'start' => '<ul class="pager pager-pills">
                                <li class="first '.$isstart.'"><a class="pager-item icon icon-step-backward" data-page="1" href="'.$url.'1"></a></li>
                                <li class="previous '.$isstart.'"><a class="pager-item" data-page="'.$prew.'" href="'.$url.$prew.'">«</a></li>',
                'end' => '<li class="next '.$isend.'"><a class="pager-item" data-page="'.$next.'" href="'.$url.$next.'">»</a></li>
                          <li class="end '.$isend.'"><a class="pager-item icon icon-step-forward" data-page="'.$pages.'" href="'.$url.$pages.'"></a></li>
                    </ul>',
                '...' => '<li><a>...</a></li>',
            ];
            $mphtml = '';
            if($pages <= 12){
                for($i = 1; $i <=$pages; $i++){
                    $active = '';
                    if ($page == $i) {
                        $active = 'class=\'active\'';
                    }
                    $mphtml .= "<li $active><a class='pager-item' data-page='$i' href='$url$i'>$i</a></li>";
                }
            } else {
                if ($page < 6) {
                    $mphtml .= self::page_c(1, 6, $url, $page);
                    $mphtml .= $tmp['...'];
                    $mphtml .= self::page_c($pages - 3, $pages, $url, $page);
                } elseif ($pages - $page < 3) {
                    $mphtml .= self::page_c(1, 3, $url, $page);
                    $mphtml .= $tmp['...'];
                    $mphtml .= self::page_c($pages - 6, $pages, $url, $page);
                } else {
                    $mphtml .= self::page_c(1, 3, $url, $page);
                    $mphtml .= $tmp['...'];
                    $mphtml .= self::page_c($page - 2, $page + 2, $url, $page);
                    $mphtml .= $tmp['...'];
                    $mphtml .= self::page_c($pages - 2, $pages, $url, $page);
                }
            }
            return $tmp['start'].$mphtml.$tmp['end'];
        }
        return '';
    }

    public static function page_c($start, $end, $url, $page) {
        $mphtml = '';
        for ($i = $start; $i <= $end; $i++) {
            $active = '';
            if ($page == $i) {
                $active = 'class=\'active\'';
            }
            $mphtml .= "<li $active><a class='pager-item' data-page='$i' href='$url$i'>$i</a></li>";
        }
        return $mphtml;
    }
}