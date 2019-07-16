<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2019-3-18
 *
 */

namespace app\common;

use think\Controller;
use think\Db;
use app\common\Log;
use app\common\Ip;
use app\common\Config;

class Limit {

    public static function ban() { /*三秒内访问次数检查，最短请求间隔三秒*/

        $sec = (int)Config::getconf('info', 'visitlimit');
        $sec = ($sec == '') ? 0 : $sec;

        $len = time() - $sec;
        $ip = Ip::getip();
        $rec = Db::query("select `vid` from slender_log where ip = '" . $ip . "' and time > '" . $len . "' order by vid desc limit 5");
        return (count($rec) > 1) ? 1 : 0;
    }
}