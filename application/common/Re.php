<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-07-12
 *
 */

namespace app\common;

class Re { /* 用于页面刷新更新 */
    public static function refrash() {
        $current_page = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/') + 1);
        echo "<script>location.href='" . $current_page . "'</script>";
    }

    /* 将操作结果存入cookie并刷新网页，防止二次提交的同时在刷新完后在页面上操作结果 */
    public static function echo($warnlevel, $word, $dontred) {
        session_start();
        $_SESSION['warning'] = urlencode($word);
        $_SESSION['warnlevel'] = $warnlevel;
        session_write_close();
        if ($dontred != 1) {
            Re::refrash();
        }
    }

    /* 负责 Re::echo 的刷新显示 */
    public static function showecho() {
        session_start();
        if (!empty($_SESSION['warning'])) {
            $data = $_SESSION['warning'];
            $_SESSION['warning'] = '';
            session_write_close();
            Turn::echo(urldecode($data));
        }
        session_write_close();
    }
}