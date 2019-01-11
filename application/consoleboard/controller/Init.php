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
    namespace qzxy\consoleboard\controller;
    use qzxy\Re;
    use qzxy\User;
    use qzxy\Log;
    use qzxy\Qhelp;
    use think\Controller;

    class Init extends Controller {
        public function _initialize() {
            $this->logincheck();
            $this->nav();
            Re::showecho();
            $this->exit();
            $this->uid = User::ufetch()['uid']; /* 获取用户id */
        }

        public function logincheck() { /*管理员登陆检查*/
            Log::visit("consoleboard", "home", "logincheck");
            session_start();
            if(empty($_SESSION['uid'])){$this->error('请先登陆', SITE . "/consoleboard/login.html");}
            session_write_close();
        }

        public function nav() { /*初始化导航*/
            $G_ = User::ufetch();
            $this->assign ([
                'index' => $G_['pm'] >= 700 ? 'denny' : 'hidden',
                'chunkmag' => User::has_pm('chunk_mag') ? 'denny' : 'hidden',
                'threadmag' => $G_['pm'] >= 700 ? 'denny' : 'hidden',
                'navmag' => User::has_pm('nav_mag') ? 'denny' : 'hidden',
                'usermag' => User::has_pm('user_mag') ? 'denny' : 'hidden',
                'configmag' => User::has_pm('config_mag') ? 'denny' : 'hidden',
                'enrollmag' => User::has_pm('enroll_use') ? 'denny' : 'hidden',
                'other' => 'hidden',
            ]);
        }
        public function exit(){
            if(isset($_POST['exit'])){
                Log::visit("consoleboard","home","log_out");
                session_start();
                $_SESSION['uid'] = '';
                session_write_close();
                $this->success('退出成功', SITE . "/portal.html");
            }
        }
    }