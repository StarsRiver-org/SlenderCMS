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

    class Enrollmag_function1 extends Controller {

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

            $res = Db::query("select * from qzlit_usenroll where ".(!empty($cps) ? "campus = $cps AND" : "")." (hascalled is null OR (isfaced = '-1' AND hascalled = 1)) AND `aim` = $aim limit ".$num);

            return Qhelp::json_en([
                'Stat' => 'OK',
                'Message' => '数据加载成功',
                "Data" => Enrollmag::dataFormat($res)
            ]);
        }

        static function send($id, $phone, $msgtpl, $msgdat){
            if (!empty($id) && Qhelp::chk_pint($id) && !empty($phone) && Qhelp::chk_pint($phone) && strlen($phone) == 11) {

                $res = Db::query("select * from qzlit_usenroll where id = $id");

                if (!empty($res) && $res[0]["phone"] == $phone) {

                    if (($res[0]["hascalled"] == 1 && $res[0]["isfaced"] != '-1') || ($res[0]["hascalled"] == 2 && $res[0]["isfaced"] == '0')) {
                        return Qhelp::json_en(['Stat' => 'error', 'Message' => '拒绝重复发信']);
                    }

                    if(Enrollmag::chk_pty($res[0]['aim'])){
                        return Enrollmag::chk_pty($res[0]['aim']);
                    }

                    $smsres = Curl::post(SITE.'/api/sms', [
                        'hash' => SHASH,
                        'phone' => $phone,
                        'msgdat' => $msgdat,
                        'msgtpl' => $msgtpl,
                    ]);

                    $hascalled = $res[0]["hascalled"] == 1 ? 2 : 1;

                    if(json_decode($smsres,true)['Message'] == 'OK'){
                        $ftime = Qhelp::json_de($msgdat)['time'];
                        Db::execute("update qzlit_usenroll set hascalled = $hascalled, isenrolled = 0, isfaced = 0, f2f = '".User::ufetch()['uid']."', ftime = '".htmlspecialchars($ftime,ENT_QUOTES)."' where id = $id");
                    }

                    return $smsres;

                }
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '数据不存在']);
            } else {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数缺失']);
            }
        }
    }