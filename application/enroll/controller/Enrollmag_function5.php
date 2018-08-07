<?php

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

            $aim = User::ufetch()['party'];

            $res = Db::query("select * from qzlit_usenroll where isenrolled = 1 AND hascalled = 3 AND aim = $aim");
            return Qhelp::json_en([
                'Stat' => 'OK',
                'Message' => '数据加载成功',
                "Data" => Enrollmag::dataFormat($res)
            ]);
        }
    }
