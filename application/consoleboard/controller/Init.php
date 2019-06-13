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

use app\common\Cookie;
use think\Controller;
use app\common\Re;
use app\common\User;
use app\common\Log;

class Init extends Controller {
    public function _initialize() {

        new \app\Start();

        $this->logincheck();
        $this->nav();
        Re::showecho();
        $this->cbexit();
    }

    public function logincheck() { /*管理员登陆检查*/
        $this->uid = Cookie::getcookie('uuid'); /* 获取用户id */
        $this->sid = Cookie::getcookie('deviceid'); /* 获取会话id */
        $session = User::getSession($this->uid,$this->sid);
        if (!isset($session['now_signin']) || !$session['now_signin'] ||
            !isset($session['end_time']) ||  $session['end_time'] < time() ||
            User::ufetch()['pm'] < 700
        ) {
            User::unsetSession($this->uid,$this->sid);
            $this->error('请先登陆', SITE . "/consoleboard/login.html");
            return;
        }

        /*刷新会话，保持登陆*/
        if(isset($session['keep_signin']) && $session['keep_signin']){
            $interval = 30*20*3600;
        } else {
            $interval = 15*60;
        }
        User::updateSession($this->uid,$this->sid,[
            'end_time' => time() + $interval,
        ]);
    }

    public function nav() { /*初始化导航*/
        $G_ = User::ufetch();
        $this->assign([
            'index' => $G_['pm'] >= 700 ? 'denny' : 'hidden', 'chunkmag' => User::has_pm('chunk_mag') ? 'denny' : 'hidden',
            'threadmag' => $G_['pm'] >= 700 ? 'denny' : 'hidden', 'navmag' => User::has_pm('nav_mag') ? 'denny' : 'hidden',
            'usermag' => User::has_pm('user_mag') ? 'denny' : 'hidden',
            'configmag' => User::has_pm('config_mag') ? 'denny' : 'hidden',
            'enrollmag' => User::has_pm('enroll_use') ? 'denny' : 'hidden',
            'other' => 'hidden',
        ]);
    }

    public function cbexit() {
        $this->uid = Cookie::getcookie('uuid'); /* 获取用户id */
        $this->sid = Cookie::getcookie('deviceid'); /* 获取会话id */

        if (isset($_POST['exit'])) {
            User::unsetSession($this->uid,$this->sid);
            Log::visit("consoleboard", "home", "log_out");
            $this->success('退出成功', SITE . "/portal.html");
        }
    }
}