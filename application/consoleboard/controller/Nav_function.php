<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2018-03-02
 *
 */
    namespace qzxy\consoleboard\controller;
    use qzxy\Qhelp;
    use think\Controller;
    use think\Db;

    class Nav_function extends Controller{
        public static function update($data) {
            if(!$data){
                return Qhelp::json_en(['Stat' => 'error', 'Message'=>'参数不足']);
            }
            $arr = [];
            $arr = Qhelp::json_de($data);
            foreach ($arr as $i){
                if(
                    (!empty($i['id']) && !Qhelp::chk_pint($i['id'])) ||
                    (!empty($i['active']) && (!Qhelp::chk_pint($i['active']) || $i['active'] > 1 )) ||
                    (!empty($i['blank']) && (!Qhelp::chk_pint($i['blank']) || $i['blank'] > 1 )) ||
                    !Qhelp::chk_pint($i['order']) ||
                    !Qhelp::chk_pint($i['type']) || $i['type'] < 1 ||
                    !Qhelp::chk_pint($i['bel'])
                ){
                    return Qhelp::json_en(['Stat' => 'error', 'Message'=>'保存遇到问题！请检查参数是否正确（文字里不能有特殊字符）']);
                }

                if(!empty($i['id'])){
                    $chksys =  Db::query("select `system` from qzlit_nav where id = '".$i['id']."'")[0]['system'];
                    if(!$chksys){
                        if($i['del']){
                            Db::execute( "delete from qzlit_nav WHERE id = '".$i['id']."'");
                            Db::execute( "delete from qzlit_nav WHERE bel = '".$i['id']."'");
                        } else {
                            Db::execute("update qzlit_nav set
                            `order` = '" . (int)$i['order'] . "', 
                            `type` = '" . (int)$i['type'] . "',  
                            `bel` ='" . (int)$i['bel']. "',
                            `name` ='" . htmlspecialchars(Qhelp::dss($i['name']),ENT_QUOTES) . "',
                            `key` ='" . htmlspecialchars(Qhelp::dss($i['key']),ENT_QUOTES) . "',
                            `blank` ='" . (int)($i['blank']) . "',
                            `url` ='" . htmlspecialchars(Qhelp::dss($i['url']),ENT_QUOTES) . "',
                            `active` ='" . (int)$i['active'] . "'
                            where id = '" . (int)$i['id'] . "'");
                        }
                    } else {
                        Db::execute("update qzlit_nav set
                        `order` = '" . (int)$i['order'] . "', 
                        `name` ='" . htmlspecialchars(Qhelp::dss($i['name']),ENT_QUOTES) . "',
                        `key` ='" . htmlspecialchars(Qhelp::dss($i['key']),ENT_QUOTES) . "',
                        `blank` ='" . (int)($i['blank']) . "',
                        `active` ='" . (int)$i['active'] . "'
                        where id = '" . (int)$i['id'] . "'");
                    }
                } else {
                    Db::execute("insert into 
                    qzlit_nav (
                        `order`, 
                        `type`,  
                        `bel`,
                        `name`,
                        `key`,
                        `blank`,
                        `url`
                    )
                    VALUE (
                     '" . (int)$i['order'] . "',
                     '" . (int)$i['type'] . "', 
                     '" . (int)$i['bel'] . "',
                     '" . htmlspecialchars(Qhelp::dss($i['name']),ENT_QUOTES) . "',
                     '" . htmlspecialchars(Qhelp::dss($i['key']),ENT_QUOTES) . "',
                     '" . (int)($i['blank']) . "',
                     '" . htmlspecialchars(Qhelp::dss($i['url']),ENT_QUOTES) . "'
                    )");
                }
            }
            return Qhelp::json_en(['Stat' => 'OK', 'Message'=>'保存成功']);
        }
    }
    