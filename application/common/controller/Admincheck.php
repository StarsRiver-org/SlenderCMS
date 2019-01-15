<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-06-25
 *
 */
    namespace qzxy\common\controller;
    use qzxy\Log;
    use qzxy\Config;
    use think\Controller;

    class Admincheck extends Controller {

        public static function view($tpl = 'error/maintenance') { /*管理员登陆检查*/

            $is_open = Config::getconf('info','open');
            if($is_open == 'off'){
                session_start();
                if(empty($_SESSION['uid'])){
                    Log::visit("baned", "none", "logincheck");
                    session_write_close();
                    return view('error/maintenance');
                } else {
                    session_write_close();
                    return view($tpl);
                }
            } else {
                return view($tpl);
            }

        }

    }