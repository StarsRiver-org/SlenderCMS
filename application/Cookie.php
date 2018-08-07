<?php

namespace qzxy;

class Cookie {

    static function savecookie($key, $content, $path = '/'){
        $data = str_replace(',','%2*2%',json_encode($content,JSON_UNESCAPED_UNICODE));
        setrawcookie($key,$data,time()+3600*24*7,$path,null,null,true);
        return $content;
    }

    static function getcookie($key){
        if(isset($_COOKIE[$key])){
            return json_decode(str_replace('%2*2%',',',$_COOKIE[$key]),true);
        }
    }
}
