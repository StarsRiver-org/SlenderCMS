<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-06-15
 *
 */
namespace qzxy;
use think\Db;

class Sec {
    /* 验证登陆操作 */
    public static function passcheck($method, $usermark, $password) {
        /* 判断登陆方式，并返回用户信息 */
        if ($method == 'username') {
            $pick = Db::query("select `uid`,`unreg`,`username`,`promise`,`salt`,`key`,`phone`,`email` from qzlit_group WHERE username = '" . htmlspecialchars(Qhelp::dss($usermark),ENT_QUOTES) . "'");
        } elseif ($method == 'email') {
            $pick = Db::query("select `uid`,`unreg`,`username`,`promise`,`salt`,`key`,`phone`,`email` from qzlit_group WHERE email = '" . htmlspecialchars(Qhelp::dss($usermark),ENT_QUOTES) . "'");
        } elseif ($method == 'phone') {
            $pick = Db::query("select `uid`,`unreg`,`username`,`promise`,`salt`,`key`,`phone`,`email` from qzlit_group WHERE phone = '" . htmlspecialchars(Qhelp::dss($usermark),ENT_QUOTES) . "'");
        } else {
            Re::echo ('danger', "致命错误！", 0);
        }
        /* 判断是否存在用户 */
        if (empty($pick)) {
            return null;  /* 未查询到数据，用户不存在 */
        } else {
            /* 判断密码是否正确 */
            if ($pick['0']['unreg']){ //判断是否用户被注销
                return 'unreg'; /* 信息匹配错误，密码或用户名错误 */
            }
            if ($pick['0'][$method] == htmlspecialchars(Qhelp::dss($usermark),ENT_QUOTES)  && $pick['0']['key'] == Sec::mdpass($pick['0']['salt'], $password)) {
                return ['uid' => $pick['0']['uid'], 'promise' => $pick['0']['promise'],]; //判断正确，返回用户 uid 和 权限值
            } else {
                return 'error'; /* 信息匹配错误，密码或用户名错误 */
            }
        }
    }

    /* 返回用户的盐和密匙 */
    public static function passhash($password) {
        $salt = Sec::createsalt();
        $mdpass = Sec::mdpass($salt, htmlspecialchars(addslashes($password),ENT_QUOTES));
        return ['salt' => $salt, 'key' => $mdpass,];
    }

    /* 混淆密码，获得密匙 */
    public static function mdpass($salt, $cpw) {
        return md5(md5($salt . $cpw . 'tyutqzxy'));
    }

    /* 生成随机 十六 位数 */
    public static function createsalt() {
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQLSTUVWXYZ0123456789';
        $salt = '';
        for ($i = 1; $i < 16; $i++) {
            $od = rand(0, 61);
            $letter = substr($str, $od, 1);
            $salt = $salt . $letter;
        }
        return $salt;
    }
}