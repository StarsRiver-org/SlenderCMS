<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2018-03-14
 *
 */
namespace qzxy;

use think\Controller;
use qzxy\Qhelp;
use think\Db;

class Config extends Controller{
    static function getconf($type,$name = 0,$json = false){

        if($json){
            if(empty($name)){
                $res = Db::query("select `name`, `data`,`descrip`, `issolid` from qzlit_config where `type`='".$type."' order by `order`");
            } else {
                $res = Db::query("select `data` from qzlit_config where `type`='".$type."' AND `name`='".$name."' ");
                if(!empty($res)){
                    $res = htmlspecialchars_decode($res[0]['data']);
                }
            }

            if(!empty($res)) {
                return Qhelp::json_en([
                    'Stat'=> 'OK',
                    'Message' => '配置成功读取',
                    'Data' => $res
                ]);
            } else {
                return Qhelp::json_en([
                    'Stat'=> 'error',
                    'Message' => '没有相关配置',
                ]);
            }
        } else {
            $res = Db::query("select `data` from qzlit_config where `type`='".$type."' AND `name`='".$name."' ");
            if($res){
                return $res[0]['data'];
            } else {
                return 0;
            }
        }
    }


}
