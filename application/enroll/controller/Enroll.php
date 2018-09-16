<?php

 namespace qzxy\enroll\controller;

    use qzxy\Config;
    use qzxy\Qhelp;
    use think\Controller;
    use think\Db;
    use think\response\Json;


    class Enroll extends Controller {

        public function main() {
            return view('enroll/enroll');
        }

        public function check() {

            $qqlist = [
                1 => '98987546',  //运营
                2 => '98987546',  //卡乐坊
                3 => '98987546',  //新闻中心
                4 => '98987546',  //清泽微视
                5 => '98987546',  //综合媒体
                6 => '98987546',  //公关
                7 => '98987546',  //UED
                8 => '98987546',  //蓝之青
            ];

            $plist = Qhelp::json_de(Config::getconf('Info','party'))['Data'];


            if(empty($_POST['name'])){
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '请输入你的名字',]);
            }
            if(empty($_POST['phone']) || !Qhelp::chk_pint($_POST['phone']) || strlen($_POST['phone']) != 11){
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '请正确输入你的报名用的电话号码',]);
            }


            $name = Qhelp::dss($_POST['name']);
            $phone = Qhelp::dss($_POST['phone']);

            $res = Db::query("select * from qzlit_usenroll where name = '".$name."' AND phone= '".$phone."'");


            if(empty($res)){
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '未找到报名信息',]);
            } else {
                if(empty($res[0]['isfaced']) || empty($res[0]['hascalled'])){
                    return Qhelp::json_en(['Stat' => 'OK', 'Message' => '请耐心等待面试通知',]);
                }

                if(!empty($res[0]['hascalled']) && empty($res[0]['isfaced'])){
                    return Qhelp::json_en(['Stat' => 'OK', 'Message' => '面试通知已经发送,请在'.$res[0]['ftime'].'在学生活动中心参加面试，具体地点请查看短信']);
                }

                if($res[0]['isenrolled'] == '-1'){
                    return Qhelp::json_en(['Stat' => 'OK', 'Message' => '抱歉，你未通过面试',]);
                }

                if($res[0]['isfaced'] == 1 && $res[0]['isenrolled'] == 0){
                    return Qhelp::json_en(['Stat' => 'OK', 'Message' => '面试官们正在深思熟虑中，请耐心等待',]);
                }

                if($res[0]['isenrolled'] == 1){
                    $party = Qhelp::json_de($plist)[$res[0]['aim']];
                    $qq = $qqlist[$res[0]['aim']];
                    return Qhelp::json_en(['Stat' => 'OK', 'Message' => '恭喜你，你顺利地通过了【'.$party.'】的面试。接下来请加入QQ群【'.$qq.'】,在那里你将和其他小伙伴们与我们一同学习']);
                }
                return 0;
            }
        }
    }