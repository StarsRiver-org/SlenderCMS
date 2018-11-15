<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2018-03-24
 *
 */
    namespace qzxy\consoleboard\controller;

    use think\Controller;
    use qzxy\Qhelp;
    use think\Db;

    class Configmag_function extends Controller{
        static function saveconf($data){
            $name = htmlspecialchars(Qhelp::dss($data['name']),ENT_QUOTES);
            $rd = Db::query("select * from qzlit_config where `name` ='".$name."'");

            if (empty($rd)) {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '数据无法映射，请刷新页面重试']);
            }
            if ($rd[0]['issolid']) {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '系统变量不可修改']);
            }
            if ($rd[0]['data'] == htmlspecialchars($data['data'],ENT_QUOTES)) {
                return 0;
            }
            if (in_array($rd[0]['data'], ['on', 'off']) && !in_array($data['data'], ['on', 'off'])) {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '数据类型映射错误，请刷新页面重试']);
            }

            Db::execute("update qzlit_config set `data` = '".htmlspecialchars(Qhelp::dss($data['data']),ENT_QUOTES)."' where `name`='".$name."'");
            return Qhelp::json_en([
                'Stat' => 'OK', 'Message' => '已将 <b>"'.$name.'"</b> 设置为 <b>"'.htmlspecialchars($data['data'],ENT_QUOTES).'</b>"'
            ]);
        }

        static function resetconf($data){
            $name = htmlspecialchars(Qhelp::dss($data['name']),ENT_QUOTES);
            $rd = Db::query("select * from qzlit_config where `name` ='".$name."'");

            if (empty($rd)) {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '数据无法映射，请刷新页面重试']);
            }
            if ($rd[0]['issolid']) {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '固定变量不可修改']);
            }

            Db::execute("update qzlit_config set `data` = '".Qhelp::dss($rd[0]['df'])."' where `name` ='".$name."'");
            return Qhelp::json_en([
                'Stat' => 'OK',
                'Message' => $name.' 》 值已恢复默认',
                'Data' => htmlspecialchars_decode($rd[0]['df']),
            ]);
        }

    }
    