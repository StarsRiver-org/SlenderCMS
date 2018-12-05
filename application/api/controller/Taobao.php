<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2018-08-12
 *
 */
    namespace qzxy\api\controller;
    use qzxy\Curl;
    use qzxy\Qhelp;
    use think\Controller;


    class Taobao extends Controller {

        public static function ipinfo($ip) {
            if(empty($ip)){return Qhelp::json_en([
                    'Stat' => 'error',
                    'Message' => '请输入请求IP',
                ]
            );}
            $data =  Curl::get('http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip,'json');
            if(!empty($data) && $data['code'] == '0'){
                return Qhelp::json_en([
                    'Stat' => 'OK',
                    'Message' => '成功获取IP信息',
                    'Data' => [
                        "ip" => $data['data']['ip'],
                        "country" => $data['data']['country'],
                        "area" => $data['data']['area'],
                        "region" => $data['data']['region'],
                        "city" => $data['data']['city'],
                        "county" => $data['data']['county'],
                        "isp" => $data['data']['isp'],
                    ]
                ]);
            }
            return Qhelp::json_en([
                'Stat' => 'error',
                'Message' => '操作频繁，请重试',
            ]);
        }
    }