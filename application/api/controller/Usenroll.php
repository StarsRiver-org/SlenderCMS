<?php
    namespace qzxy\api\controller;
    use qzxy\Config;
    use qzxy\File;
    use qzxy\Ip;
    use qzxy\Qhelp;
    use qzxy\User;
    use think\Controller;
    use think\Db;


    class Usenroll extends Controller {

        public function main() {

            if (!User::has_pm('enroll_use') && (empty($_POST['hash']) || (!empty($_POST['hash']) && $_POST['hash'] != SHASH))) {
                return Qhelp::json_en(['Stat' => 'error', "Message" => "您无权使用本接口"]);
            }


            $starttime = Config::getconf('Enroll','startdate');
            $endtime = Config::getconf('Enroll','enddate');

            $starttime = mktime(0,0,0,substr($starttime,4,2),substr($starttime,6,2),substr($starttime,0,4));
            $endtime = mktime(0,0,0,substr($endtime,4,2),substr($endtime,6,2),substr($endtime,0,4));
            $time = time();

            if($endtime <= $starttime){
                return Qhelp::json_en(['Stat' => 'error', "Message" => '现在不是报名时间，']);
            }

            if( $time > $endtime){
                return Qhelp::json_en(['Stat' => 'error', "Message" => '报名已经结束，']);
            }
            if( $time < $starttime ){
                return Qhelp::json_en(['Stat' => 'error', "Message" => '报名还未开始，']);
            }


            if (empty($_POST['sex']) || strlen($_POST['sex']) != 1 || !Qhelp::chk_pint($_POST['sex']) || $_POST['sex'] > 2) {
                return Qhelp::json_en(['Stat' => 'error', "Message" => '不填性别会导致没有未来']);
            }
            if (empty($_POST['name'])) {
                return Qhelp::json_en(['Stat' => 'error', "Message" => '请输入姓名']);
            }
            if (empty($_POST['studentid']) || strlen($_POST['studentid']) != 10 ||  !Qhelp::chk_pint($_POST['studentid']) || $_POST['studentid'] < 2017000000) {
                return Qhelp::json_en(['Stat' => 'error', "Message" => '老人不要凑热闹谢谢']);
            }
            if (empty($_POST['college'])) {
                return Qhelp::json_en(['Stat' => 'error', "Message" => '请问你的福利院是哪个？']);
            }
            if (empty($_POST['major'])) {
                return Qhelp::json_en(['Stat' => 'error', "Message" => '不填专业会导致没有未来']);
            }
            if (empty($_POST['phone']) || strlen($_POST['phone']) != 11 || !Qhelp::chk_pint($_POST['phone']) || $_POST['phone'] < 1000000) {
                return Qhelp::json_en(['Stat' => 'error', "Message" => '请准确填写你的常用联系方式，不然就找不了哟']);
            }

            if (empty($_POST['aim']) || strlen($_POST['aim']) != 1 || !Qhelp::chk_pint($_POST['aim']) || $_POST['aim'] > 8) {
                return Qhelp::json_en(['Stat' => 'error', "Message" => '怎么也要选一个的志愿部门吧']);
            }

            $rrep = Db::query("select * from qzlit_usenroll where phone = '".$_POST['phone']."'");

            if(!empty($rrep)){
                return Qhelp::json_en(['Stat' => 'error', "Message" => '你已经报名过了，请耐心等待结果']);
            }

            $rpip = Db::query("select * from qzlit_usenroll where ip = '".htmlspecialchars(Qhelp::dss(Ip::getip()),ENT_QUOTES)."' ORDER by time DESC limit 1");
            if(!empty($rpip) && time()-24*3600 < $rpip[0]['time']){
                return Qhelp::json_en(['Stat' => 'error', "Message" => '这波操作过于频繁，隔天再试？']);
            }


            if (empty($_POST['reasion']) || strlen($_POST['reasion']) < 30) {
                return Qhelp::json_en(['Stat' => 'error', "Message" => '故事不能少于三十个字']);
            }

            $avtName = File::saveavt();
            if(!empty($avtName) && !empty($avtName['Stat']) && $avtName['Stat'] == 'error'){
                return Qhelp::json_en($avtName);
            }

            Db::execute("INSERT INTO 
            qzlit_usenroll (
                    `sex`,
                    `name`,
                    `phone`,
                    `photo`,
                    `email`,
                    `aim`,
                    `aim2`,
                    `studentid`,
                    `college`,
                    `major`,
                    `class`,
                    `reasion`,
                    `reasion2`,
                    `ip`,
                    `time`
            ) VALUES (
                     '" . ($_POST['sex'] == 1 ? '男' : '女') . "', 
                     '" . Qhelp::receive('name','') . "',
                     '" . $_POST['phone'] ."',
                     '" . $avtName ."',
                     '" . Qhelp::receive('email','') ."',
                     '" . $_POST['aim'] . "',
                     '" . Qhelp::receive('aim2','') ."',
                     '" . $_POST['studentid'] . "',
                     '" . Qhelp::receive('college','') . "',
                     '" . Qhelp::receive('major','') . "',
                     '" . Qhelp::receive('class','') . "',
                     '" . Qhelp::receive('reasion','') . "',
                     '" . Qhelp::receive('reasion2','') . "',
                     '" . htmlspecialchars(Qhelp::dss(Ip::getip()),ENT_QUOTES) . "',
                     '" . time() . "'
                )
            ");

            return Qhelp::json_en([
                'Stat' => 'OK',
                "Message" => '报名成功,请耐心等待短信通知',
                'Data' => [
                    'sex' => $_POST['sex'] == 1 ? '男' : '女',
                    'name' => Qhelp::receive('name',''),
                    'phone' => $_POST['phone'],
                    'photo'=> "<img src='".SITE."/data/catch/temp/avatar/".$avtName."'>",
                    'aim' => $_POST['aim'],
                    'aim2' => $_POST['aim2']
                ]
            ]);


        }
    }