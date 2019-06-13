<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2019-3-2
 *
 */

namespace app\common;

use think\Controller;
use think\Request;
use app\common\Log;
use app\common\Config;
use app\common\User;
use app\common\Limit;

class Template extends Controller {

    public static function view($tpl, $arg = '') {
        return self::make($tpl, $arg);
    }

    public static function initTpl($tpl) {
        $allow_mobile = Config::getconf('info', 'mobiletpl');

        if ((@$_GET['device'] == 'm' || Request::instance()->isMobile()) && $allow_mobile == 'on' && @file_exists(ROOT_PATH . 'template' . DS . 'mobile' . DS . $tpl . '.html')) {
            return 'mobile';
        } else {
            return 'default';
        }
    }

    public static function make($tpl, $arg = '') {



        return view(self::initTpl($tpl) . DS . $tpl, $arg);
    }

}
