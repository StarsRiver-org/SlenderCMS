<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2018-03-25
 *
 */
namespace qzxy;

use think\Db;
use qzxy\Ip;


class Log {
    /*存储log*/
    public static function updata_log($log = 0){
        /* 插入 XX 来避免未获取到IP信息而弹错 */

        if(!$log){return null;}
        if(
            Db::execute("INSERT INTO 
            qzlit_log (
                    `time`, 
                    `ip`, 
                    `country`,
                    `area`,
                    `city`,
                    `region`, 
                    `county`, 
                    `target`, 
                    `get`, 
                    `post`, 
                    `data`,
                    `isp`,
                    `func`
            )
            VALUES (
                 '" . time() . "',
                 '" . $log['ip'] . "', 
                 '" . $log['country'] . "',
                 '" . $log['area'] . "',
                 '" . $log['city'] . "',
                 '" . $log['region'] . "',
                 '" . $log['county'] . "',
                 '" . $log['target'] . "', 
                 '" . $log['get'] . "', 
                 '" . $log['post'] . "',
                 '" . $log['data'] . "',
                 '" . $log['isp']. "', 
                 '" . $log['func'] . "'
            )
        ")){
            return 1;
        } else{
            return null;
        }
    }

    /*访问操作纪录
        page: portal     article       consoleboard
        data  门户名      文章id         管理类型
        func  无         like/visit     add/del/renew
    */
    public static function visit($page = 0, $data = '', $func='') {
        $log = Ip::ipinfo();
        $log['target'] = $page ? $page : 'home' ;
        $log['data'] = $data ;
        $log['func'] = $func ;
        $log['get'] = '' ;
        $log['post'] = '' ;
        Log::updata_log($log);
        return null;
    }

    /* 根据时间筛选纪录/文章
     * 时间段为 今天-gap 到 今天
     * */
    public static function filt_by_time($arr,$gap){
        $today = mktime(23,59,59,date("m"),date("d"),date("Y"));
        $new_arr = [];
        $tp = 0;
        switch ($gap){
            case '': break;
            case 'day' :$tp = $today - 3600*24;break;
            case 'week':$tp = $today - 3600*24*7;break;
            case 'moon':$tp = $today - 3600*24*30;break;
            case 'year':$tp = $today - 3600*24*365;break;
        }
        foreach ($arr as $key){
            if(isset($key['time']) && $key['time']> $tp){ /*针对纪录类型*/
                array_push($new_arr,$key);
            } elseif (isset($key['thread_ptime']) && $key['thread_ptime']> $tp){ /*针对文章类型*/
                array_push($new_arr,$key);
            }
        }
        return $new_arr;
    }

    /*根据操作类型筛选纪录
      func由人为规定，目前的操作类型有
         * 用户(精准)   ：visit ,like,
         * 登陆(精准)   ：try_login_nouser, try_login_nopromiss， log_in, log_out,
         * 板块管理(模糊)：add, comb, del, rename, change_editor
         * 用户管理(模糊)：add, reg, renew, del
         * */
    public static function filt_by_func($arr, $func) {
        $new_arr = [];
        foreach ($arr as $key) {
            if ($key['func'] == $func) {
                array_push($new_arr, $key);
            }
        };
        return $new_arr;
    }

    /*根据访问目标筛选纪录
      目前的目标类型
         * article, home, consoleboard
         * */
    public static function filt_by_target($arr, $target) {
        $new_arr = [];
        foreach ($arr as $key) {
            if ($key['target'] == $target) {
                array_push($new_arr, $key);
            }
        };
        return $new_arr;
    }

    /*根据时间间隔返回每个间隔的访问量及总量*/
    /*时：默认获取24小时的纪录*/
    /*天：最多获取30天的纪录*/
    /*周：最多获取12周的纪录*/
    /*月：最多获取12月的纪录*/
    public static function count_by_timegap($arr, $gap = 0, $loop = 0) {
        $today  = mktime(0,0,0,date("m"),date("d")+1,date("Y"));
        $tomon = mktime(0,0,0,date("m")+1,1,date("Y"));
        $toyea = mktime(0,0,0,1,1,date("Y"));
        $mouth = [31,31,28,31,30,31,30,31,31,30,31,30,31];
        $gp = 0; $autoloop = 0; $current = 0; $short = 0; $new_arr = []; $total = 0;
        switch ($gap){
            case '': return null; break;
            case 'hour':$gp = 3600;$autoloop = 24;$current = 24;$short = 0;break;
            case 'day' :$gp = 3600*24;$autoloop = 30;$current = date("d");$short = (date("Y")/4 == 0 && $mouth[date("m")-1] == 2) ? $mouth[date("m")-1]+1 : $mouth[date("m")-1];break;
            case 'week':$gp = 3600*24*7;$autoloop = 15;$current = ceil(($today - $toyea)/(3600*24*7)) ;$short = date("Y")/4 == 0 ? 53:52;break;
            case 'moon':$gp = 3600*24*30;$autoloop = 12;$current = date("m");$short = 12;break;
        }
        $autoloop = $loop ? $loop : $autoloop;
        for($i = 0; $i<$autoloop; $i++){
            $count = 0;
            foreach ($arr as $key) {
                if ($key["time"] > $today - $gp * ($i + 1) && $key["time"] <= $today - $gp * ($i)) {
                    $count += 1;
                } elseif ($key["time"] <= $today - $gp * ($i + 1)) {
                    break;
                }
            }
            $total += $count;
            if($current-$i <= 0){
                $new_arr[$i] = [
                    "date" => $current-$i+$short,
                    "value" => $count,
                ];
            }
            else {
                $new_arr[$i] = [
                    "date" => $current-$i,
                    "value" => $count,
                ];
            }
        }
        $new_arr["count"] = $total;
        return array_reverse($new_arr);
    }
}