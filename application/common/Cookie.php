<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2018-02-09
 *
 */

namespace app\common;

class Cookie {

    static function savecookie($key, $content, $path = '/', $unsafe = '0') {
        if(is_array($content)){
            $data = 'isserial*'.str_replace(',', '%2*2%', json_encode($content, JSON_UNESCAPED_UNICODE));
        } else {
            $data = $content;
        }
        setrawcookie($key, $data, time() + 3600 * 24 * 30, $path, null, null, $unsafe ? 0 : true);
        return $content;
    }

    static function getcookie($key) {
        if (isset($_COOKIE[$key])) {
            if(substr($_COOKIE[$key],0,9) == 'isserial*'){
                return json_decode(str_replace('%2*2%', ',', substr($_COOKIE[$key],9)), true);
            } else {
                return $_COOKIE[$key];
            }
        } else {
            return 0;
        }
    }
}
