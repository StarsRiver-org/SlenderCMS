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

    use qzxy\Config;
    use qzxy\File;
    use qzxy\Qhelp;
    use qzxy\User;
    use think\Controller;
    use think\Db;


    class Enrollmag extends Controller
    {
        public function _initialize(){

            new \qzxy\consoleboard\controller\Init();

            if(!User::has_pm('enroll_use')) {
                $this->error('操作错误，您未获得该操作权限');
                return null;
            }
        }

        public function main() {
            $this->assign([
                'enrollmag' => 'active',
                'enall' => count(Db::query("select id from qzlit_usenroll")),
                'en' => count(Db::query("select id from qzlit_usenroll where `aim` = '".User::ufetch()['party']."'")),
                'en2' => count(Db::query("select id from qzlit_usenroll where `aim2` = '".User::ufetch()['party']."'"))
            ]);
            return view('enroll/enrollmag');
        }

        /* 面试通知发送 */ //OK
        public function logic_1(){
            if(!empty($_POST['med'])){
                switch ($_POST['med']){
                    case 'refresh':
                        return Enrollmag_function1::refresh(@$_POST['num']);
                        break;
                    case 'sendsms':
                        return Enrollmag_function1::send(@$_POST["id"], @$_POST["phone"],@$_POST['msgtpl'],@$_POST['msgdat']);
                        break;
                }
            }
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数不足，无法理解行为']);
        }

        /* 面试控制管理 */
        public function logic_2(){
            if(!empty($_POST['med'])){
                switch ($_POST['med']){
                    case 'refresh':
                        return Enrollmag_function2::refresh(@$_POST['num']);
                        break;
                    case 'pass':
                        return Enrollmag_function2::pass(@$_POST["id"], @$_POST["phone"], @$_POST["score"],@$_POST["sug"]);
                        break;
                    case 'absence':
                        return Enrollmag_function2::absence(@$_POST["id"], @$_POST["phone"]);
                        break;
                    case 'turn':
                        return Enrollmag_function2::turn(@$_POST["id"], @$_POST["phone"], @$_POST['party'], @$_POST['ftime']);
                        break;
                }
            }
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数不足，无法理解行为']);
        }

        /* 录取管理 */ //OK
        public function logic_3(){
            if(!empty($_POST['med'])){
                switch ($_POST['med']){
                    case 'refresh':
                        return Enrollmag_function3::refresh(@$_POST['num'],@$_POST['page']);
                        break;
                    case 'enroll':
                        return Enrollmag_function3::enroll(@$_POST['id'],@$_POST['phone']);
                        break;
                }
            }
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数不足，无法理解行为']);

        }

        /* 录取通知管理 */ // OK
        public function logic_4(){
            if(!empty($_POST['med'])){
                switch ($_POST['med']){
                    case 'refresh':
                        return Enrollmag_function4::refresh(@$_POST['num']);
                        break;
                    case 'unenroll':
                        return Enrollmag_function4::unenroll(@$_POST["id"], @$_POST["phone"]);
                        break;
                    case 'sendsms':
                        return Enrollmag_function4::send(@$_POST["id"], @$_POST["phone"],@$_POST['msgtpl'],@$_POST['msgdat']);
                        break;
                }
            }
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数不足，无法理解行为']);
        }

        /* 录取未通过通知管理 */ // OK
        public function logic_6(){
            if(!empty($_POST['med'])){
                switch ($_POST['med']){
                    case 'refresh':
                        return Enrollmag_function6::refresh(@$_POST['num']);
                        break;
                    case 'sendsms':
                        return Enrollmag_function6::send(@$_POST["id"], @$_POST["phone"],@$_POST['msgtpl'],@$_POST['msgdat']);
                        break;
                }
            }
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数不足，无法理解行为']);
        }

        /* 录取查看 */ //OK
        public function logic_5(){
            return Enrollmag_function5::refresh();
        }

        /* 整理返回数据 */
        public static function dataFormat($res){
            $party = Qhelp::json_de(htmlspecialchars_decode(Db::query("select * from qzlit_config where `name` = 'party'")[0]['data'],ENT_QUOTES));
            $rt = [];
            foreach ($res as $v){
                $v['aim'] = $party[$v['aim']];
                $v['aim2'] = !empty($v['aim2']) ? $party[$v['aim2']] : '';
                $v['photo'] = !empty($v['photo']) ? File::fetchavt($v['photo']) : STATIC_ROOT.'/img/common/avt.png';
                $rt[] = $v;
            }
            return $rt;
        }

        /* 用于检查用户与面试者的 */
        public static function chk_pty($faim = null){
            $party = User::ufetch()['party'];
            if(empty($party)){
                return Qhelp::json_en([
                    'Stat' => 'No',
                    'Message' => '你还没有选择自己的部门，无数据返回',
                    "Data" => [],
                ]);
            }
            if(!empty($faim) && $faim != $party){
                return Qhelp::json_en([
                    'Stat' => 'No',
                    'Message' => '该面试者不属于你的部门',
                    "Data" => [],
                ]);
            }
            return 0;
        }

        /* 获取面试者信息 */
        public function ei(){
            if(!empty($_POST['id']) && Qhelp::chk_pint($_POST['id']) && !empty($_POST['phone']) && Qhelp::chk_pint($_POST['phone']) && strlen($_POST['phone']) == 11){
                $res = Db::query("select * from qzlit_usenroll where id = '".$_POST['id']."'");

                if(Enrollmag::chk_pty($res[0]['aim'])){
                    return Enrollmag::chk_pty($res[0]['aim']);
                }

                if(!empty($res) && $res[0]['phone'] == $_POST['phone']){
                    return Qhelp::json_en([
                        'Stat'=> 'OK',
                        'Message'=> '加载成功',
                        'Data'=> self::dataFormat($res)[0],
                    ]);
                }
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '数据映射失败，拒绝返回']);
            }
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数不足，无法理解行为']);
        }

        /* 获取某部门的面试时间点 */
        static function getftime($m){
            $campus = Qhelp::json_de(Config::getconf('Info','campus'));
            if(Qhelp::chk_pint($m) && $m > 0){
                $tce = Db::query("select `ftime`,`campus` from qzlit_usenroll where (hascalled = 1 OR hascalled = 2) AND isfaced = 0 AND isenrolled = 0 AND `aim` = $m order by ftime " );
                $ftimes = [];
                foreach ($tce as $value){
                    if(!empty($value['ftime']) && !in_array([
                            'cp' => $campus[$value['campus']],
                            'cpid' => $value['campus'],
                            'ft' => $value['ftime']
                        ],$ftimes)){
                        $ftimes[] = [
                            'cp' => $campus[$value['campus']],
                            'cpid' => $value['campus'],
                            'ft' => $value['ftime']
                        ];
                    }
                }
                if(!empty($_GET['JSON'])) {
                    return Qhelp::json_en($ftimes);
                }
                return $ftimes;
            } else {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数类型错误，应该为<int>']);
            }
        }

        /* 获取部门名称 */
        static function getpartyname($m){
            if(Qhelp::chk_pint($m) && $m > 0){
                $party = Qhelp::json_de(htmlspecialchars_decode(Db::query("select * from qzlit_config where `name` = 'party'")[0]['data'],ENT_QUOTES))[$m];
                return $party;
            } else {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数类型错误，应该为<int>']);
            }
        }
    }