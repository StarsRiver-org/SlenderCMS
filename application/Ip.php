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

class Ip {
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

        /*匹配IP是否规范，否则返回 2.2.2.2 记录用IP*/
        if(filter_var($ip,FILTER_VALIDATE_IP)){
            return $ip;
        } else {
            return '2.2.2.2';
        }
    }

    public static function localsaveip($ip = 0){
        $ip = $ip ? $ip : Ip::getip();
        $data = Filter::de_json('http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip);
        return $data['code'] !== '1' ? Cookie::savecookie('ipinfo', $data) : 0;
    }

    public static function ipinfo($ip = 0) {
        $ip = $ip ? $ip : Ip::getip();
        if($ip !== '127.0.0.1'){
            if(isset($_COOKIE['ipinfo']) && Cookie::getcookie('ipinfo')['data']['ip'] == $ip) {
                $data = Cookie::getcookie('ipinfo');
            } else {
                $data = self::localsaveip();
            }
            return $data["data"];
        } else {
            return [
                "ip" => "127.0.0.1",
                "country" =>  "localhost",
                "country_id" => "localhost",
                "area" => "localhost",
                "area_id" => "localhost",
                "region" => "localhost",
                "region_id" => "localhost",
                "city" => "localhost",
                "city_id" => "localhost",
                "county" => "localhost",
                "county_id" => "localhost",
                "isp" => "localhost",
                "isp_id" => "localhost",
            ];
        }
    }
}