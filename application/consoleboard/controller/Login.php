<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-06-25
 *
 */

namespace app\consoleboard\controller;


use think\Controller;
use think\Db;
use app\common\Template;
use app\common\Sec;
use app\common\Log;
use app\common\Re;
use app\common\Lang;
use app\common\controller\Base;
use app\common\Cookie;
use app\common\Qhelp;
use app\common\User;

class Login extends Controller {
    public function _initialize() {

        new \app\Start();

        Re::showecho();

        /*
         * 登陆检测用到的会话信息：
         *
         * @param $int error_login  //因密码错误导致的登陆失败错误次数
         * @param $string last_try_signin_time //最后一次尝试登陆的时间
         * @param $int keep_signin //保持登陆标记
         * @param $int now_signin //允许登陆标志
         * @param $end_time //保持登陆截至时间
         * */

        if ($_POST) {

            /*登陆方法*/
            $this->method = $_POST['method'];

            /*账户*/
            $this->umark = htmlspecialchars(Qhelp::dss($_POST['usermark']), ENT_QUOTES);

            /*密码*/
            $this->pswd = $_POST['password'];

            /*保持登陆选项*/
            $this->keeplogin = !empty($_POST['keeplogin']) ? $_POST['keeplogin'] : false;

            if (empty($this->method)){
                Re::echo('danger', "页面错误", 0);
                return;
            }

            if (empty($this->umark) || empty($this->pswd)) {
                Re::echo('danger', "请填入完整信息", 0);
                return;
            }

            switch ($this->method){
                case 'username' :$pick = Db::query("select `uid`,`promise` from slender_group WHERE username = '" . $this->umark . "'"); break;
                case 'email'    :$pick = Db::query("select `uid`,`promise` from slender_group WHERE email = '" . $this->umark . "'"); break;
                case 'phone'    :$pick = Db::query("select `uid`,`promise` from slender_group WHERE phone = '" . $this->umark . "'"); break;
                default         :Re::echo('danger', "致命错误！", 0);return;
            }

            if(count($pick) == 0 ){
                Re::echo('warning', "用户不存在", 0);
                return;
            }

            /*用户ID*/
            $this->uid = $pick[0]['uid'];

            /*用户禁用标识*/
            $this->unreg = !empty($pick[0]['unreg']) ? $pick[0]['unreg'] : 0;

            /*用户权限*/
            $this->pem = !empty($pick[0]['promise']) ? $pick[0]['promise'] : 0;

            /*权限检测*/
            if ($this->pem < 700) {
                Log::visit("consoleboard", "home", "try_login_nopromiss");
                Re::echo('warning', "权限不足！", 0);
                return;
            }

            /*用户禁用检测*/
            if ($this->unreg == true) {
                Re::echo('warning', "用户已被禁用", 0);
                return;
            }

            /*当前会话ID*/
            $this->sessionid = Cookie::getcookie('deviceid');

            /*当前Session数据*/
            $this->session = User::getSession($this->uid, $this->sessionid);

            //最多登陆错误次数
            $this->outsign = 5;

            /*当前登陆失败次数*/
            $this->outlet = isset($this->session['error_login']) ? $this->session['error_login'] : 0;

            /*上次尝试登陆时间*/
            $this->last_try = isset($this->session['last_try_signin_time']) ? $this->session['last_try_signin_time'] : 0;

            /*等待时间*/
            $this->interval = 60*15;

            /*超出登陆限制检测*/
            if ($this->outlet > $this->outsign && (time() - $this->last_try) <= $this->interval) {
                Re::echo('warning', "登陆错误次数过多，请".ceil(($this->interval + $this->last_try - time())/60)."分钟后再试", 0);
                return;
            }

            /*密码检测结果*/
            $this->passcheck = Sec::passcheck($this->method, $this->umark, $this->pswd);

            /*密码匹配检测*/
            if($this->passcheck == false){
                /*失败，增加登陆失败次数*/

                User::updateSession($this->uid,$this->sessionid,[
                    'error_login'=> $this->outlet + 1,
                    'last_try_signin_time' => time(),
                ]);
                Log::visit("consoleboard", "home", "try_login_passwrong");
                Re::echo('warning', "密码输入错误", 0);
                return;
            }

            /*登陆成果后预处理*/

            /*会话过期时间*/
            if($this->keeplogin == true){
                $this->prelong = 3600*24*30;
            } else {
                $this->prelong = 60*15;
            }

            /*设置引导*/
            Cookie::savecookie('uuid',$this->uid);

            User::updateSession($this->uid,$this->sessionid,[
                'error_login' => 0,
                'now_signin' => 1,
                'keep_signin' => $this->keeplogin,
                'last_try_signin_time' => time(),
                'end_time' => time()+$this->prelong,
            ]);

            Db::execute("update slender_group set `lastlogin` = '" . date('YmdHi', time()) . "', `lastip` ='" . $_SERVER['REMOTE_ADDR'] . "' where uid = '" . $this->uid . "'");
            Log::visit("consoleboard", "home", "log_in");
            Re::echo('success', "欢迎登陆", 1);
            echo '<script>window.location.href=\'' . SITE . '/consoleboard/index.html\'</script>';
        }
    }

    public function main() {

        $this->assign('lang', Lang::load('consoleboard'));
        $this->assign('webbase', Base::baseinfo(false));
        return Template::view('consoleboard/login');
    }
}