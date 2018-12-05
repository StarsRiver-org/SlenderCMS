<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-09-04
 *
 */
namespace qzxy;

use qzxy\api\Controller\Taobao; //淘宝接口格式化调用

class Ip{
    public static function getip() {

        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $ip = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $ip = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                $ip = getenv("HTTP_X_FORWARDED_FOR");
            } else if (getenv("HTTP_CLIENT_IP")) {
                $ip = getenv("HTTP_CLIENT_IP");
            } else {
                $ip = getenv("REMOTE_ADDR");
            }
        }

        /*匹配IP是否规范，否则返回 0.0.0.0 记录用IP*/
        if(filter_var($ip,FILTER_VALIDATE_IP)){
            return $ip;
        } else {
            return '0.0.0.0';
        }
    }


    public static function ipinfo($ip = 0) {
        /* 目前基于PHP请求IP信息，安全性强 */
        /* 如果为了快速，建议通过JS设置客户端IP来处理信息 - 直接增加 ipinfo cookie项 （将json字符串中的 ， 替换为 %2*2%）*/

        $ip = $ip ? $ip : Ip::getip();

        if ($ip == '0.0.0.0') {return ["ip" => "0.0.0.0", "country" => "", "area" => "", "region" => "", "city" => "", "county" => "", "isp" => "",];}
        if ($ip == '127.0.0.1') {return ["ip" => "127.0.0.1", "country" => "", "area" => "", "region" => "", "city" => "", "county" => "", "isp" => "",];}

        if (isset($_COOKIE['ipinfo']) && Cookie::getcookie('ipinfo')['ip'] == $ip) {
            return Cookie::getcookie('ipinfo');
        } else {

            $data = Qhelp::json_de(Taobao::ipinfo($ip)); //获取IP信息，内网服务器需要更改为静态IP解析器

            if (empty($data) || $data['Stat'] !== 'OK') {return [];}
            Cookie::savecookie('ipinfo', $data['Data']);
            return $data['Data'];
        }
    }
}