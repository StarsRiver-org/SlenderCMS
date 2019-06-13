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

namespace app\common;

use think\Config as Tconfig;
use think\Exception;

class Lang {

    /*
     * This would load a model's langs file
     *
     * @param $dir:  model name
     * @param $includcom:  Will loading the common language list acquiescently. if false, there would be only a stereotype langs file
     * */

    public static function load($dir, $includcom = true) {

        $root = $_SERVER['DOCUMENT_ROOT'] . '/../application/';

        $lang = Cookie::getcookie('lang');

        $defl = Tconfig::get('default_lang');

        if (file_exists($root . $dir . '/lang/' . $lang . '.php')) {
            $langs = require($root . $dir . '/lang/' . $lang . '.php');
        } elseif (file_exists($root . $dir . '/lang/' . $defl . '.php')) {
            $langs = require($root . $dir . '/lang/' . $defl . '.php');
        } else {
            throw new Exception('语言文件“...' . $dir . '/lang/' . $lang . '.php”与“...' . $dir . '/lang/' . $defl . '.php”不存在');
        }

        $langs = require($root . $dir . '/lang/' . $defl . '.php');

        if ($includcom) {
            if (file_exists($root . 'common/lang/' . $lang . '.php')) {
                $cml = require($root . 'common/lang/' . $lang . '.php');
            } elseif (file_exists($root . 'common/lang/' . $defl . '.php')) {
                $cml = require($root . 'common/lang/' . $defl . '.php');
            } else {
                $cml = [];
            }
            $langs += $cml;
        }

        return $langs;
    }

    public static function say($dir, $str) {
        self::load($dir);

        if (empty($langs[$str])) {
            throw new Exception('语言文件中没有“' . $str . '”参量');
        }

        return $langs[$str];
    }

}