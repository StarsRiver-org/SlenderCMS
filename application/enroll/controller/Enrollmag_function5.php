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

    class Enrollmag_function5 extends Controller {

        static function refresh(){

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

            $res = Db::query("select * from qzlit_usenroll where ".(!empty($cps) ? "campus = $cps AND" : "")." isenrolled = 1 AND hascalled = 3 AND aim = $aim");
            return Qhelp::json_en([
                'Stat' => 'OK',
                'Message' => '数据加载成功',
                "Data" => Enrollmag::dataFormat($res)
            ]);
        }
    }
