<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-08-13
 *
 */
    namespace qzxy\consoleboard\controller;

    use qzxy\User;
    use qzxy\Qhelp;
    use think\Db;
    use think\Controller;


    class Usermag_function extends Controller {

        public static function recentreg() {
            $td = Db::query("select count(1) from qzlit_group WHERE regtime > '" . date('Ymd' . '0000', time()) . "'");
            $tm = Db::query("select count(1) from qzlit_group WHERE regtime > '" . date('Ym' . '000000', time()) . "'");
            return ['regd' => $td[0]['count(1)'], 'regm' => $tm[0]['count(1)'],];
        }

        public static function usernum() {
            $creator = Db::query("select `uid` from qzlit_group where promise >= 900 and username != 'zhangyu'");
            $admin = Db::query("select `uid` from qzlit_group where promise >= 800 and promise < 900 ");
            $editor = Db::query("select `uid` from qzlit_group where promise >= 700 and promise < 800");
            $user = Db::query("select `uid` from qzlit_group where promise >= 100 and promise < 700");
            return [
                'num_creator' => count($creator),
                'num_admin' => count($admin),
                'num_editor' => count($editor),
                'num_user' => count($user)
            ];
        }

        public static function usercreator() {
            return Db::query("select `uid`,promise,`unreg`,`party`,username,lastlogin,`phone`,`email`,`name` from qzlit_group where promise >= 900 and username != 'zhangyu'");
        }

        public static function useradmin() {
            return Db::query("select `uid`,promise,`unreg`,`party`,username,lastlogin,`phone`,`email`,`name` from qzlit_group where promise >= 800 and promise < 900");
        }

        public static function usereditor() {
            return Db::query("select `uid`,promise,`unreg`,`party`,username,lastlogin,`phone`,`email`,`name` from qzlit_group where promise >= 700 and promise < 800");
        }

        public static function cuser() {
            return Db::query("select `uid`,promise,`unreg`,`party`,username,lastlogin,`phone`,`email`,`name` from qzlit_group where promise >= 100 and promise < 700");
        }
    }