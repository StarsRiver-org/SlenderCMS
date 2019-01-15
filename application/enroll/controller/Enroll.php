<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2018-07-14
 *
 */

namespace qzxy\enroll\controller;

use qzxy\Config;
use qzxy\Qhelp;
use qzxy\common\controller\Admincheck;
use think\Controller;
use think\Db;


class Enroll extends Controller {

    public function main() {
        return Admincheck::view('enroll/enroll');
    }

    public function check() {

        /* 号码顺序按部门顺序来填写 */
        $qqlist = [
            1 => '904254887',  //--运营
            2 => '879014365',  //--卡乐坊
            3 => '720127750',  //--新闻中心
            4 => '721406851',  //--清泽微视
            5 => '639848200',  //--综合媒体
            6 => '906648423',  //--公关
            7 => '906733977',  //--UED
            8 => '906759599',  //--蓝之青
        ];

        $plist = Config::getconf('Info', 'party');


        if (empty($_POST['name'])) {
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '请输入你的名字',]);
        }
        if (empty($_POST['phone']) || !Qhelp::chk_pint($_POST['phone']) || strlen($_POST['phone']) != 11) {
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '请正确输入你的报名用的电话号码',]);
        }


        $name = Qhelp::dss($_POST['name']);
        $phone = Qhelp::dss($_POST['phone']);

        $res = Db::query("select * from qzlit_usenroll where name = '" . $name . "' AND phone= '" . $phone . "'");


        if (empty($res)) {
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '未找到报名信息',]);
        } else {

            if ($res[0]['isenrolled'] == '-1') {
                return Qhelp::json_en(['Stat' => 'OK', 'Message' => '抱歉，你未通过面试',]);
            }

            if ($res[0]['isfaced'] == 1 && $res[0]['isenrolled'] == 1) {
                if ($res[0]['hascalled'] == 3) {
                    $party = Qhelp::json_de($plist)[$res[0]['aim']];
                    $qq = $qqlist[$res[0]['aim']];
                    if (!empty($qq)) {
                        return Qhelp::json_en(['Stat' => 'OK', 'Message' => '恭喜你，你顺利地通过了【' . $party . '】的面试。接下来请加入QQ群【' . $qq . '】,在那里你将和其他小伙伴们与我们一同学习']);
                    }
                    return Qhelp::json_en(['Stat' => 'OK', 'Message' => '恭喜你，你顺利地通过了【' . $party . '】的面试。但是群聊还没准备好，请联系' . $party . '的学长或3小时后再查']);
                } else {
                    return Qhelp::json_en(['Stat' => 'OK', 'Message' => '面试官们正在深思熟虑中，请耐心等待',]);
                }
            }

            if (!empty($res[0]['hascalled']) && ($res[0]['hascalled'] == 1 || $res[0]['hascalled'] == 2) && empty($res[0]['isfaced'])) {
                return Qhelp::json_en(['Stat' => 'OK', 'Message' => '面试通知已经发送，请在' . $res[0]['ftime'] . ',到短信或面试官通知的指定地点参加面试。']);
            }

            if (empty($res[0]['isfaced']) && empty($res[0]['hascalled'])) {
                return Qhelp::json_en(['Stat' => 'OK', 'Message' => '请耐心等待面试通知',]);
            }

            return Qhelp::json_en(['Stat' => 'OK', 'Message' => '请耐心等待面试结果的公布',]);
        }
    }
}