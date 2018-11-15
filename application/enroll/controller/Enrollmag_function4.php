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

    use qzxy\Curl;
    use qzxy\Qhelp;
    use qzxy\User;
    use think\Controller;
    use think\Db;

    class Enrollmag_function4 extends Controller
    {
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

            $num = (int)$num < 10 ? 10 : ((int)$num > 99 ? 99 : (int)$num) ;
            $res = Db::query("select * from qzlit_usenroll where ".(!empty($cps) ? "campus = $cps AND" : "")." isenrolled = 1 AND hascalled != 3 AND isfaced = 1 AND aim = $aim order by score DESC  limit $num");

            return Qhelp::json_en([
                'Stat' => 'OK',
                'Message' => '数据加载成功',
                "Data" => Enrollmag::dataFormat($res)
            ]);
        }

        static function send($id, $phone, $msgtpl, $msgdat){
            if (!empty($phone) && !empty($id) && Qhelp::chk_pint($phone) && strlen($phone) == 11 && Qhelp::chk_pint($id)) {

                $res = Db::query("select * from qzlit_usenroll where id = $id");

                if (!empty($res) && $res[0]["phone"] == $phone) {

                    if(Enrollmag::chk_pty($res[0]['aim'])){
                        return Enrollmag::chk_pty($res[0]['aim']);
                    }

                    if ($res[0]["hascalled"] == 3) {
                        return Qhelp::json_en(['Stat' => 'error', 'Message' => '拒绝重复发信']);
                    }

                    $smsres = Curl::post(SITE.'/api/sms', [
                        'hash' => SHASH,
                        'phone' => $phone,
                        'msgdat' => $msgdat,
                        'msgtpl' => $msgtpl,
                    ]);

                    if(Qhelp::json_de($smsres)['Message'] == 'OK'){
                        Db::execute("update qzlit_usenroll set hascalled = 3, isenrolled = 1, isfaced = 1 where id = $id");
                    }

                    return $smsres;

                }
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '数据不存在']);
            } else {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数缺失']);
            }
        }

        static function unenroll($id, $phone){
            if (!empty($phone) && !empty($id) && Qhelp::chk_pint($phone) && strlen($phone) == 11 && Qhelp::chk_pint($id)) {
                $res = Db::query("select * from qzlit_usenroll where id = $id");

                if (!empty($res) && $res[0]["phone"] == $phone) {

                    if(Enrollmag::chk_pty($res[0]['aim'])){
                        return Enrollmag::chk_pty($res[0]['aim']);
                    }

                    Db::execute("update qzlit_usenroll set isenrolled = 0, isfaced = 1 where id = $id");
                    return Qhelp::json_en(['Stat' => 'OK', 'Message' => '这位稍后考虑...']);
                }
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '数据不存在']);
            } else {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数缺失']);
            }
        }
    }