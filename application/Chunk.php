<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-08-06
 *
 */
namespace qzxy;

use think\Controller;
use think\Db;


class Chunk extends Controller {
    /* 用于外部时注意对 $chunkid 合法性校验 */
    static function getchunk_lv($chunkid) {
        $res = Db::query("select chunk_lv from qzlit_chunk WHERE id = '" . $chunkid . "'");
        if(!empty($res)){
            return $res[0]['chunk_lv'];
        }
        return 0;
    }

    /* 用于外部时注意对 $chunkid 合法性校验 */
    static function getchunk($chunkid) {
        $res = Db::query("select * from qzlit_chunk WHERE id = '" . $chunkid . "'");
        return $res ? $res[0] : '' ;
    }

    /* 用于外部时注意对 $chunkid 合法性校验 */
    static function loadchunk($chunkid) {
        $chunk = [];
        if (Chunk::getchunk_lv($chunkid) == 1) {
            $chunk_lv2 = Db::query("select * from qzlit_chunk WHERE chunk_below = '" . $chunkid . "'");
            $chunk_lv3 = [];
            foreach ($chunk_lv2 as $v) {
                $res = Db::query("select * from qzlit_chunk WHERE chunk_below = '" . $v['id'] . "'");
                foreach ($res as $k) {
                    array_push($chunk_lv3, $k);
                }
            }
            $chunk = ['chunk_lv2' => $chunk_lv2, 'chunk_lv3' => $chunk_lv3];
        } elseif (Chunk::getchunk_lv($chunkid) == 2) {
            $chunk_lv3 = Db::query("select * from qzlit_chunk WHERE chunk_below = '" . $chunkid . "'");
            $chunk = ['chunk_lv2' => '', 'chunk_lv3' => $chunk_lv3];
        }
        return $chunk;
    }

    /* 用于外部时注意对 $chunkid 合法性校验 */
    static function loadthread($chunkid, $face_to_user = false) {
        /*！对数据库读取进行限制*/
        if($face_to_user){
            $face_to_user = 'AND hk_mode = 2';
        }
        $chunklv = Chunk::getchunk_lv($chunkid);
        $chunk = Chunk::loadchunk($chunkid);
        $argumets = '`cuid`, `thread_title`, `thread_author`, `thread_editor`, `thread_coverimg`,`thread_htime`,`thread_ctime`, `thread_ptime`, `hk_sort`, `hk_descrip`,`hk_keywords`,`ore_degree`,`ore_view`';
        $thread = [];
        $threadlist = [];
        if ($chunklv == 1) {
            $thread_lv1 = Db::query("select $argumets from qzlit_thread  WHERE hk_sort = $chunkid $face_to_user order by thread_ptime desc limit 1000");
            foreach ($thread_lv1 as $l) {
                array_push($thread, $l);
            }
            if ($chunk['chunk_lv2']) {
                foreach ($chunk['chunk_lv2'] as $v) {
                    $thread_lv2 = Db::query("select $argumets from qzlit_thread WHERE hk_sort = '" . $v['id'] . "' $face_to_user order by thread_ptime desc limit 1000");
                    foreach ($thread_lv2 as $k) {
                        array_push($thread, $k);
                    }
                }
                if ($chunk['chunk_lv3']) {
                    foreach ($chunk['chunk_lv3'] as $m) {
                        $thread_lv3 = Db::query("select $argumets from qzlit_thread WHERE hk_sort = '" . $m['id'] . "' $face_to_user order by thread_ptime desc limit 1000");
                        foreach ($thread_lv3 as $j) {
                            array_push($thread, $j);
                        }
                    }
                }
            }
        } elseif ($chunklv == 2) {
            $thread_lv2 = Db::query("select $argumets from qzlit_thread WHERE hk_sort = $chunkid $face_to_user order by thread_ptime desc limit 1000");
            foreach ($thread_lv2 as $c) {
                array_push($thread, $c);
            }
            if ($chunk['chunk_lv3']) {
                foreach ($chunk['chunk_lv3'] as $a) {
                    $thread_lv3 = Db::query("select $argumets from qzlit_thread WHERE hk_sort = '" . $a['id'] . "' $face_to_user order by thread_ptime desc limit 2000");
                    foreach ($thread_lv3 as $b) {
                        array_push($thread, $b);
                    }
                }
            }
        } else {
            $thread = Db::query("select $argumets from qzlit_thread WHERE hk_sort = $chunkid $face_to_user order by thread_ptime desc limit 3000");
        }
        foreach ($thread as $u) {
            array_push($threadlist, Thread::format($u,'more'));
        }
        return $threadlist;
    }

    /* 加载板块的幻灯和横幅 */
    /* 用于外部时注意对 $chunkid 合法性校验 */
    static function loadbanner($chunkid) {
        $num = 10; /*最多10*/
        $arr = [];
        @$bg = json_decode(Db::query("select `banner` from qzlit_chunk WHERE id = $chunkid")[0]['banner'],true);
        for($i = 0; $i<$num; $i++){
            array_push($arr,(isset($bg[$i]) && $bg[$i] != "empty") ? (Qhelp::checkpic($bg[$i]) ? File::fetchimg($bg[$i]) : STATIC_ROOT.'/img/common/no-img-2.png') : STATIC_ROOT.'/img/common/no-img-2.png' );
        }
        return [
            'slider' => [$arr[0],$arr[1],$arr[2],$arr[3],$arr[4]],
            'banner' => [$arr[5],$arr[6]],
        ];
    }

    static function load_specials($cks) {
        $arr = [];
        foreach ($cks as $value){
            $chck = self::getchunk($value);
            if(!empty($chck)){
                $imgs = self::loadbanner($value);
                $arr[] = [
                    "id" => $chck['id'],
                    "name" => $chck['chunk_name'],
                    "banner" => $imgs['banner'],
                    "slider" =>  $imgs['slider'],
                ];
            }
        }
        return $arr;
    }
}