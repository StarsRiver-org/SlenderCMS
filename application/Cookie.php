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
namespace qzxy;

class Cookie {

    static function savecookie($key, $content, $path = '/', $unsafe){
        $data = str_replace(',','%2*2%',json_encode($content,JSON_UNESCAPED_UNICODE));
        setrawcookie($key,$data,time()+3600*24*7,$path,null,null,$unsafe ? 0 : true);
        return $content;
    }

    static function getcookie($key){
        if(isset($_COOKIE[$key])){
            return json_decode(str_replace('%2*2%',',',$_COOKIE[$key]),true);
        }
    }
}
