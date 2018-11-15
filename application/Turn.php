<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-06-17
 *
 */
namespace qzxy;

class Turn { /* 当页面刷新跳回后要加载的动作 */
    public static function echo($word) {
        session_start();
        $lel = $_SESSION['lel'];
        $_SESSION['lel'] = '';
        session_write_close();
        echo "<div class='poptip " . $lel . " shadow1'>" . $word . "</div>";
    }
}