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
    use think\Controller;
    use qzxy\Sec;
    use qzxy\Log;
    use qzxy\Re;
    use think\Db;

    class Login extends Controller{
        public function _initialize(){
            Re::showecho();
            if($_POST){
                if(Re::fucstop()){
                    //检查是否超过错误登陆次数
                    //由于php7.1以上版本必须要函数返回值才能进行刷新操作，所以必须采取判断形式来进行因此返回值验证
                } elseif (!empty($_POST['isadmin']) || empty($_POST['method'])){
                    if(empty($_POST['usermark']) || empty($_POST['password'])){
                        Re::echo('danger',"请填入完整信息",0);
                    } else {
                        $res = Sec::passcheck($_POST['method'],$_POST['usermark'],$_POST['password']);
                        @$uid = $res['uid'];
                        @$promise = $res['promise'];
                        if(!isset($res)){
                            Log::visit("consoleboard","home","try_login_nouser");
                            Re::echo('danger',"用户不存在",0);
                        } elseif ( $res == 'error'){
                            Log::visit("consoleboard","home","try_login_passwrong");
                            Re::fuclimitadd(); //增加错误登陆次数
                        } elseif ( $res == 'unreg'){
                            Log::visit("consoleboard","home","try_login_passwrong");
                            Re::echo('danger',"用户已停用",0);
                        } elseif($promise  >= 700){
                            /* 自动登录功能 为了安全，已经注释掉了. login 模板中也有被注释的内容
                            if(!empty($_POST['keeplogin'])){
                                session_start();
                                $_SESSION['uid'] = $uid;
                            session_write_close();
                            }else{*/
                            session_start();
                            $_SESSION['uid'] = $uid;
                            session_write_close();
                            /*}*/
                            Db::execute("update qzlit_group set `lastlogin` = '".date('YmdHi',time())."', `lastip` ='".$_SERVER['REMOTE_ADDR']."' where uid = '".$uid."'");
                            Log::visit("consoleboard","home","log_in");
                            Re::echo('success',"欢迎登陆",1);
                            echo '<script>window.location.href=\''.SITE.'/consoleboard/index.html\'</script>';
                        } else {
                            Log::visit("consoleboard","home","try_login_nopromiss");
                            Re::echo('danger',"用户权限不足，如果你认为你应该可以登陆，请联系管理员申请权限",0);
                        }
                    }
                } else {
                    abort(500,'页面错误');
                }
            }
        }

        public function main(){
            return view('admin/login');
        }
    }