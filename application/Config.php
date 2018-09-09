<?php
namespace qzxy;

use think\Controller;
use qzxy\Qhelp;
use think\Db;

class Config extends Controller{
    static function getconf($type,$name = 0){

        if(empty($name)){
            $res = Db::query("select `name`, `data`,`descrip`, `issolid` from qzlit_config where `type`='".$type."'");
        } else {
            $res = Db::query("select `data` from qzlit_config where `type`='".$type."' AND `name`='".$name."' ");
            if(!empty($res)){
                $res = $res[0]['data'];
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
                'Data' => $res
            ]);
        }
    }


}
