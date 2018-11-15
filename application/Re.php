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
namespace qzxy;

class Re { /* 用于页面刷新更新 */
    public static function refrash() {
        $current_page = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/') + 1);
        echo "<script>location.href='" . $current_page . "'</script>";
    }

    /* 将操作结果存入cookie并刷新网页，防止二次提交的同时在刷新完后在页面上操作结果 */
    public static function echo($lel, $word, $dontred) {
        session_start();
        $_SESSION['warning'] = urlencode($word);
        $_SESSION['lel'] = $lel;
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
            Turn::echo (urldecode($data));
        }
        session_write_close();
    }
    /* 操作次数限制递增 */ /* 下方的 SESSION 是 adminm模块 全局的 */
    public static function fuclimitadd() {
        session_start();
        if (!isset($_SESSION['action'])) {
            $_SESSION['action'] = 0;
        }
        $_SESSION['logtime'] = time();/* 设置当前操作时间，用于登陆延时判定 */
        $_SESSION['action']++;
        session_write_close();
        Re::echo ('danger', "错误，请检查您的信息", 0);
    }

    /* 超过操作限制，强制退出方法 */
    public static function fucstop() {
        session_start();
        $timeout = 5;
        $m = 15;
        if (isset($_SESSION['action']) && $_SESSION['action'] >= $timeout - 1) {
            // 判断是否超时
            if (isset($_SESSION['logtime']) && time() - $_SESSION['logtime'] <= 60 * $m) {
                $left = 60 * $m - time() + $_SESSION['logtime'];
                session_write_close();
                Re::echo ('danger', "操作次数过多,请 $left 秒后尝试", 0);
                return 1;
            } else { // 超时则重置时间和操作次数
                $_SESSION['logtime'] = time();
                $_SESSION['action'] = 0;
                session_write_close();
                return null;
            }
        }
        session_write_close();
        return null;
    }

    /* 强制登出 */
    public static function forcexit() {
        setrawcookie('qaalo');
    }
}