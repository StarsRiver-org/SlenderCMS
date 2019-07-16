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

namespace app\common;

use think\Db;

class Sec {
    /* 验证登陆操作 */
    public static function passcheck($method, $usermark, $password) {
        /* 判断登陆方式，并返回用户信息 */
        switch ($method){
            case 'username' :$pick = Db::query("select `salt`,`key` from slender_group WHERE username = '" . $usermark . "'"); break;
            case 'email'    :$pick = Db::query("select `salt`,`key` from slender_group WHERE email = '" . $usermark . "'"); break;
            case 'phone'    :$pick = Db::query("select `salt`,`key` from slender_group WHERE phone = '" . $usermark . "'"); break;
            default:Re::echo('danger', "致命错误！", 0); return;
        }
        /* 判断是否存在用户 */
        if (empty($pick)) {
            return false;  /* 未查询到数据，用户不存在 */
        }

        /* 判断密码是否正确 */
        if ($pick['0']['key'] == Sec::mdpass($pick['0']['salt'], $password)) {
            return true;
        } else {
            return false;
        }
    }

    /* 返回用户的盐和密匙 */
    public static function passhash($password) {
        $salt = Sec::createsalt();
        $mdpass = Sec::mdpass($salt, htmlspecialchars(addslashes($password), ENT_QUOTES));
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