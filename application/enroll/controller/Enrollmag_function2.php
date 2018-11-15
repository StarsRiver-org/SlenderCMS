<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2018-07-15
 *
 */
namespace qzxy\enroll\controller;

    use qzxy\Qhelp;
    use qzxy\User;
    use think\Controller;
    use think\Db;



    class Enrollmag_function2 extends Controller {

        static function refresh($num = 30){

            if(Enrollmag::chk_pty()){
                return Enrollmag::chk_pty();
            }

            if(!empty($_POST['campus']) && !Qhelp::chk_pint($_POST['campus'])){
                return Qhelp::json_en([
                    'Stat' => 'OK',
                    'Message' => '校区数据类型错误',
                ]);
            }
            $cps = Qhelp::receive('campus','');
            $aim = User::ufetch()['party'];

            $num =  (int)$num < 10 ? 10 : ((int)$num > 99 ? 99 : (int)$num) ;
            if(!empty($_POST['token']) && !empty($_POST['ftcap'])){
                $res = Db::query("select * from qzlit_usenroll where ".(!empty($cps) ? "campus = $cps AND" : "")." (hascalled = 1 OR hascalled = 2) AND isfaced = 0 AND isenrolled = 0 AND `aim` = $aim AND `ftime` = '".Qhelp::receive('token')."' AND `campus` = '".(int)$_POST['ftcap']."' limit ".$num);
            } else {
                $res = Db::query("select * from qzlit_usenroll where ".(!empty($cps) ? "campus = $cps AND" : "")." (hascalled = 1 OR hascalled = 2) AND isfaced = 0 AND isenrolled = 0 AND `aim` = $aim limit ".$num);
            }

            return Qhelp::json_en([
                'Stat' => 'OK',
                'Message' => '数据加载成功',
                "Data" => Enrollmag::dataFormat($res),
                "Tokens" => Enrollmag::getftime($aim),
            ]);
        }


        /* 保存面试信息 */
        static function pass($id,$phone,$score,$suggestion){
            if (!empty($phone) && !empty($id) && Qhelp::chk_pint($phone) && strlen($phone) == 11 && Qhelp::chk_pint($id)) {

                if(!Qhelp::chk_pint($score)){
                    return Qhelp::json_en(['Stat' => 'error', 'Message' => '分数不能为空，零分也是分啊']);
                }
                if($score > 1000){
                    return Qhelp::json_en(['Stat' => 'error', 'Message' => '这个分数未免太高了']);
                }
                if(empty($suggestion)){
                    return Qhelp::json_en(['Stat' => 'error', 'Message' => '写点东西吧，方便之后的筛选']);
                }

                $res = Db::query("select * from qzlit_usenroll where id = $id");

                if (!empty($res) && $res[0]["phone"] == $phone) {

                    if(Enrollmag::chk_pty($res[0]['aim'])){
                        return Enrollmag::chk_pty($res[0]['aim']);
                    }

                    Db::execute("update qzlit_usenroll set isfaced = 1, isenrolled = 0, score = $score, sug = '".htmlspecialchars(Qhelp::dss($suggestion),ENT_QUOTES)."' where id = $id");
                    return Qhelp::json_en(['Stat' => 'OK', 'Message' => '信息保存成功']);

                }
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '数据不存在']);
            } else {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数缺失']);
            }
        }

        /* 没来参加面试 */
        static function absence($id,$phone){
            if (!empty($phone) && !empty($id) && Qhelp::chk_pint($phone) && strlen($phone) == 11 && Qhelp::chk_pint($id)) {

                $res = Db::query("select * from qzlit_usenroll where id = $id");

                if (!empty($res) && $res[0]["phone"] == $phone) {

                    if(Enrollmag::chk_pty($res[0]['aim'])){
                        return Enrollmag::chk_pty($res[0]['aim']);
                    }

                    Db::execute("update qzlit_usenroll set hascalled = 1, isfaced = '-1', isenrolled = 0 where id = $id");
                    return Qhelp::json_en(['Stat' => 'OK', 'Message' => '已放置，需在通知短信发送栏重新操作']);

                }
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '数据不存在']);
            } else {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数缺失']);
            }
        }

        /* 转部门 */
        static function turn($id,$phone,$taim,$ont = null){
            if (!empty($phone) && !empty($id) && Qhelp::chk_pint($phone) && strlen($phone) == 11 && Qhelp::chk_pint($id) && Qhelp::chk_pint($taim)) {

                $res = Db::query("select * from qzlit_usenroll where id = $id");

                if (!empty($res) && $res[0]["phone"] == $phone) {

                    if(Enrollmag::chk_pty($res[0]['aim'])){
                        return Enrollmag::chk_pty($res[0]['aim']);
                    }

                    if($taim == $res[0]["aim"]){
                        return Qhelp::json_en(['Stat' => 'error', 'Message' => '禁止转让给自己部门']);
                    }
                    if($res[0]["hascalled"] == 3 && $res[0]["isenrolled"] == 1){
                        return Qhelp::json_en(['Stat' => 'error', 'Message' => '对象已被录用，无法进行该操作']);
                    }

                    if(empty($taim)){
                        Db::execute("update qzlit_usenroll set hascalled = 1, isfaced = 1, isenrolled = '-1', f2f = '-1', ftime = '-1', aim = '-1', aim2 = '-1' where id = $id");
                        return Qhelp::json_en(['Stat' => 'OK', 'Message' => '已移除，再也看不到了哦']);
                    }

                    $pn = Enrollmag::getpartyname($res[0]['aim']);
                    if(empty($ont)){
                        Db::execute("update qzlit_usenroll set hascalled = null, isfaced = null, isenrolled = null, f2f = null, ftime = '".htmlspecialchars(Qhelp::dss($ont),ENT_QUOTES)."', aim = $taim, aim2 = '', sug = '转移自".$pn."' where id = $id");
                    } else {
                        Db::execute("update qzlit_usenroll set isfaced = 0, isenrolled = 0, ftime = '".htmlspecialchars(Qhelp::dss($ont),ENT_QUOTES)."', aim = $taim, aim2 = '', sug = '转移自".$pn."' where id = $id");
                    }
                    return Qhelp::json_en(['Stat' => 'OK', 'Message' => '转让成功']);
                }
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '数据不存在']);
            } else {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数缺失']);
            }
        }
    }