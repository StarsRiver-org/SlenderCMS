<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2018-08-12
 *
 */
    namespace qzxy\api\controller;
    use qzxy\Ip;
    use qzxy\User;
    use qzxy\Qhelp;
    use think\Controller;
    use think\Db;

    class Feedback extends Controller {

        public function main() {

            if (empty($_POST['hash']) || $_POST['hash'] != SHASH) {
                return Qhelp::json_en(['Stat' => 'error', "Message" => "您无权使用本接口"]);
            }

            $res = Db::query("select `time` from qzlit_feedback where ip = '".htmlspecialchars(Qhelp::dss(Ip::getip()),ENT_QUOTES)."' order by time DESC limit 1");

            if(!empty($res) && time() - $res[0]['time'] < 3600*24){
                return Qhelp::json_en(['Stat' => 'error',  "Message" => '您近期已经提交过反馈啦']);
            }

            if (!empty($_POST)) {
                if (empty($_POST['name'])) {
                    return Qhelp::json_en(['Stat' => 'error',  "Message" => '未填写名字']);
                } elseif (empty($_POST['message'])) {
                    return Qhelp::json_en(['Stat' => 'error',  "Message" => '未填反馈信息']);
                } elseif (empty($_POST['phone']) || empty($_POST['email']) || strlen($_POST['phone']) != 11 || !is_numeric($_POST['phone'])) {
                    return Qhelp::json_en(['Stat' => 'error',  "Message" => '请认真填写联系方式']);
                } else {
                    Db::execute("INSERT INTO 
                        qzlit_feedback (
                                `name`, 
                                `ip`,
                                `message`,
                                `phone`,
                                `email`, 
                                `time`
                        ) VALUES (
                             '" . htmlspecialchars(Qhelp::dss($_POST['name']),ENT_QUOTES) . "', 
                             '" . htmlspecialchars(Qhelp::dss(Ip::getip()),ENT_QUOTES) . "',
                             '" . htmlspecialchars(Qhelp::dss($_POST['message']),ENT_QUOTES) . "',
                             '" . abs((int)$_POST['phone']) . "',
                             '" . htmlspecialchars(Qhelp::dss($_POST['email']),ENT_QUOTES) . "',
                             '" . time() . "'
                        )
                    ");
                    return Qhelp::json_en(['Stat' => 'OK',  "Message" => '提交成功']);
                }
            } else {
                return "<script>location.href='//" . $_SERVER['SERVER_NAME'] . "'</script>";
            }
        }
    }