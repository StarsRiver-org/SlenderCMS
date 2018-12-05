<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-08-05
 *
 */
namespace qzxy;

class Qhelp{
    public static function receive($key ,$defaultvalue = NULL){
        return !empty($_POST[$key]) ?  htmlspecialchars(self::dss($_POST[$key]),ENT_QUOTES) : (!empty($_GET[$key]) ?  htmlspecialchars(self::dss($_GET[$key]),ENT_QUOTES): $defaultvalue);
    }

    public static function checkpic($imglog){
        if(!empty($imglog) && $imglog != 0 && file_exists(ROOT_PATH . 'data/catch/temp/img/' . substr($imglog, 0, 8) . '/' . substr($imglog, 8))){
            return 1;
        } else {
            return null;
        }
    }

    public static function json_en($data){
        if(empty($data)){
            $data = [
                "Stat" => 'error',
                "Message" => '未获取到数据'
            ];
        }
        return json_encode($data,JSON_UNESCAPED_UNICODE);
    }
    public static function json_de($data){
        if(!is_string($data)){
            return [];
        }
        $data =  json_decode($data,true);
        if(json_last_error_msg() == JSON_ERROR_NONE){
            return $data;
        } else {
            return [];
        }
    }

    /* 字符安全性清理，防止数据库注入 */
    public static function dss($str, $def = NULL){
        if (!empty($str)){
            if(get_magic_quotes_gpc()){return $str;} else {return addslashes($str);}
        } else{
            return $def;
        }
    }
    /* 检查是否为正整型字符 */
    public static function chk_pint($num){
        if(isset($num) && is_numeric($num) && $num + 0 >= 0 && ceil($num) - $num == 0){
            return 1;
        } else {
            return null;
        }
    }
    /* 检查纯文本中是否有的特殊字符 */
    public static function chk_specal_char($str){
        if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$str)){
            return 1;
        } else {
            return null;
        }
    }
}