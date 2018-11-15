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
    use qzxy\Qhelp;
    use qzxy\User;
    use think\Controller;

    use Aliyun\Core\Config;
    use Aliyun\Core\Profile\DefaultProfile;
    use Aliyun\Core\DefaultAcsClient;
    use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
    use think\Db;

    class Sms extends Controller{
        public function main(){

            /* 接口需要四个输入值 均为 POST 方法
             *  phone  电话号码
             *  msgdat 短信参数
             *  msgtpl 短信模板编号
             *  hash   系统哈希
             * */

            if (!User::has_pm('sms_use') && (empty($_POST['hash']) || $_POST['hash'] != SHASH)) {
                return Qhelp::json_en(['Stat' => 'error', "Message" => "您无权使用本接口"]);
            }

            $smsconfig = self::getSmsConfig201807151515();
            if($smsconfig['ServiceStat'] == 'on'){
                if(!empty($_POST['phone']) && !empty($_POST['msgdat'])){

                    if(empty($_POST['msgtpl'])){
                        return Qhelp::json_en(['Stat' => 'error', 'Message' => '未填写短信模板']);
                    }

                    $mobile = abs((int)$_POST['phone']);
                    $msgdat = $_POST['msgdat'];
                    $moblen = strlen($mobile);
                    $msgtpl = Qhelp::receive('msgtpl', '');

                    if($moblen == 11 || $moblen == 15){
                        require_once __DIR__.'/../../../extend/alsms/vendor/autoload.php';   //载入API
                        Config::load();   //加载区域结点配置
                        $profile = DefaultProfile::getProfile($smsconfig['SmsRegion'], $smsconfig['AccessKeyId'], $smsconfig['AccessKeySecret']); // 初始化用户Profile实例
                        DefaultProfile::addEndpoint($smsconfig['SmsEndpoint1'], $smsconfig['SmsEndpoint2'], $smsconfig['SmsProduct'], $smsconfig['SmsDomain']);   // 增加服务结点
                        $acsClient= new DefaultAcsClient($profile);  // 初始化AcsClient用于发起请求
                        $request = new SendSmsRequest();// 初始化SendSmsRequest实例用于设置发送短信的参数
                        $request->setPhoneNumbers($mobile); // 必填，设置雉短信接收号码
                        $request->setSignName($smsconfig['AccessSignName']); // 必填，设置签名名称
                        $request->setTemplateCode($msgtpl); // 必填，设置模板CODE
                        $request->setTemplateParam($msgdat); // 可选，设置模板参数
                        $acsResponse = json_decode(Qhelp::json_en($acsClient->getAcsResponse($request)),true);// 发起访问请求
                        // 本地存储短信发送纪录
                        Db::execute("INSERT INTO sms_log (
                            `time`,
                            `call`,
                            `msgtplcode`,
                            `stat`,
                            `BizId`,
                            `Code`
                            ) VALUES (
                            '" . time() . "',
                            '" . $mobile . "',
                            '" . $msgtpl . "',
                            '" . $acsResponse['Message'] . "',
                            '" . (isset($acsResponse['BizId']) ? $acsResponse['BizId'] : '发送失败') . "',
                            '" . $acsResponse['RequestId'] . "')"
                        );
                        //返回请求结果
                        return Qhelp::json_en($acsResponse);
                    } else {
                        return Qhelp::json_en(['Stat' => 'error', 'Message' => '电话号码格式错误']);
                    }
                } else {
                    return Qhelp::json_en(['Stat' => 'error', 'Message' => '短信模板动态数据未填写']);
                }
            } else {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '短信功能已关闭']);
            }
        }

        function getSmsConfig201807151515(){
            $data = Db::query("select name, data from qzlit_config where `type` = 'SmsService'");
            foreach ($data as $k){
                $res[$k['name']] = $k['data'];
            }
            return $res;
        }


    }