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

use qzxy\api\controller\Taobao; //淘宝接口格式化调用

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
        Cookie::savecookie('ip', $ip,'/',true);
        $cook = Cookie::getcookie('ipinfo') ? Cookie::getcookie('ipinfo') : [] ;

        /*
         * 初始化IP信息数组
         *
         * 默认提取Cookie内容
         * 否则用 XX 填充
         */
        $data = (
            count($cook,COUNT_RECURSIVE) == 7 &&
            !empty($cook['ip']) && $cook['ip'] == $ip &&
            isset($cook['country']) &&
            isset($cook['area']) &&
            isset($cook['county']) &&
            isset($cook['region']) &&
            isset($cook['isp']) &&
            isset($cook['city'])
        ) ? [
            "ip" => "$ip",
            "country" => Qhelp::dss($cook['country'],'XX'),
            "area" => Qhelp::dss($cook['area'],'XX'),
            "region" => Qhelp::dss($cook['region'],'XX'),
            "city" => Qhelp::dss($cook['city'],'XX'),
            "county" => Qhelp::dss($cook['county'],'XX'),
            "isp" => Qhelp::dss($cook['isp'],'XX')
        ] : [
            "ip" => "",
            "country" => "XX",
            "area" => "XX",
            "region" => "XX",
            "city" => "XX",
            "county" => "XX",
            "isp" => "XX"
        ];


        if ($ip == '127.0.0.1') {
            $data['ip'] = '127.0.0.1';
        } elseif (
            /* 安全性检查 - 信息长度及特殊字符检查，如果信息有问题，则通过后台来检测IP*/
            count($cook,COUNT_RECURSIVE) !== 7 ||
            @empty($cook['ip']) || $cook['ip'] !== $ip ||
            @empty($cook['country']) ||
            @empty($cook['isp']) ||
            Qhelp::chk_specal_char($cook['country']) ||
            Qhelp::chk_specal_char($cook['area']) ||
            Qhelp::chk_specal_char($cook['region']) ||
            Qhelp::chk_specal_char($cook['county']) ||
            Qhelp::chk_specal_char($cook['city'])
        ) {
            /* 预设IP信息默认值 */
            $data = ["ip" => "0.0.0.0", "country" => "XX", "area" => "XX", "region" => "XX", "city" => "XX", "county" => "XX", "isp" => "XX"];

            /* 检测是否开启后台检查IP功能 */
            $getAddr = Config::getconf('info','getAddr');
            if($getAddr == 'on'){
                $get = Qhelp::json_de(Taobao::ipinfo($ip)); //获取IP信息，内网服务器需要更改为静态IP数据库
                if (!empty($get) && $get['Stat'] == 'OK') {
                    Cookie::savecookie('ipinfo', $get['Data'],'/',true);
                    $data = $get['Data'];
                }
            }
        }

        return $data;
    }
}