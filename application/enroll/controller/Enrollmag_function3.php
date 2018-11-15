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
    use qzxy\Qpage;
    use qzxy\User;
    use think\Controller;
    use think\Db;

    class Enrollmag_function3 extends Controller {
        static function refresh($perpage = 30,$page){

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

            $page = (!empty($page) && Qhelp::chk_pint($page)) ? $page : 1;
            $perpage = (int)$perpage < 10 ? 10 : ((int)$perpage > 99 ? 99 : (int)$perpage);
            $count = count(Db::query("select id from qzlit_usenroll WHERE ".(!empty($cps) ? "campus = $cps AND" : "")." `hascalled` != 3 AND `isfaced` = 1 AND `isenrolled` = 0 AND `aim`=$aim"));
            $pages = ceil($count/$perpage);
            $from = ($page-1)*$perpage - 1 >=0 ? ($page-1)*$perpage : 0 ;


            $res = Db::query("select * from qzlit_usenroll WHERE ".(!empty($cps) ? "campus = $cps AND" : "")." `hascalled` != 3 AND `isfaced` = 1 AND `isenrolled` = 0 AND `aim`=$aim order by score DESC limit ".(int)$from.",".(int)$perpage);
            if(empty($res)){
                $from = 0 ;
                $page = 1;
                $res = Db::query("select * from qzlit_usenroll WHERE ".(!empty($cps) ? "campus = $cps AND" : "")." `hascalled` != 3 AND `isfaced` = 1 AND `isenrolled` = 0 AND `aim`=$aim order by score DESC limit ".(int)$from.",".(int)$perpage);
            }

            return Qhelp::json_en([
                'Stat' => 'OK',
                'Message' => '数据加载成功',
                "Data" => Enrollmag::dataFormat($res),
                'Pages' => Qpage::page($pages,$page,'#page='),
            ]);
        }

        static function enroll($id, $phone){
            if (!empty($phone) && !empty($id) && Qhelp::chk_pint($phone) && strlen($phone) == 11 && Qhelp::chk_pint($id)) {

                $res = Db::query("select * from qzlit_usenroll where id = $id");

                if (!empty($res) && $res[0]["phone"] == $phone) {

                    if(Enrollmag::chk_pty($res[0]['aim'])){
                        return Enrollmag::chk_pty($res[0]['aim']);
                    }

                    Db::execute("update qzlit_usenroll set isenrolled = 1, isfaced = 1 where id = $id");
                    return Qhelp::json_en(['Stat' => 'OK', 'Message' => '已加入到录取名单']);
                }
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '数据不存在']);
            } else {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数缺失']);
            }

        }

    }