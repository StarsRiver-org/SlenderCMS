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

class Curl {
    public static function post($url = '', $post_data = []) {
        if (empty($url) || empty($post_data)) {
            return Qhelp::json_en([
				'Stat' => 'error',
				'Message' => '请求错误或内容不完整',
				"Data" => [],
			]);
        }
        $o = "";
        foreach ( $post_data as $k => $v )
        {
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $post_data = substr($o,0,-1);

        if(substr($url,0,4) == 'http'){
            $postUrl = $url;
        } else {
            $reqtype = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https:' : 'http:';
            $reqsls = substr($url,0,2) == '//' ? '' : '//';
            $postUrl = $reqtype.$reqsls.$url;
        }

        $curlPost = $post_data;

        $cookie = '';
        foreach ($_COOKIE as $k => $value){
            $cookie .= $k.'='.$value.';';
        }

        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);//携带cookie
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        return $data;
    }

    public static function get($url,$datatype){
        $opts = [
            'http'=>[
                'method'=>"GET",
                'timeout'=>10, //十五秒超时
            ]
        ];
        $data = @file_get_contents($url, false, stream_context_create($opts));
        if(!empty($data)){
            switch ($datatype){
                case 'json':return Qhelp::json_de($data);
                default: return [];
            }
        } else {
            return [];
        }

    }
}