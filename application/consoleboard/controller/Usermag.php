<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-08-13
 *
 */
    namespace qzxy\consoleboard\controller;

    use qzxy\User;
    use qzxy\Qhelp;
    use think\Db;
    use think\Controller;


    class Usermag extends Controller {
        public function _initialize() {
            new Init();
            if (!User::has_pm('user_mag')) {
                $this->error('操作错误，您未获得该操作权限');
                return null;
            }
        }

        public function main() {

            $this->assign([
                'usermag' => 'active',
                'appm' => User::ufetch()['pm'],
            ]);
            return view('admin/usermag');
        }

        public function get_usernum(){
            $num     = Usermag_function::usernum();
            $reg_num = Usermag_function::recentreg();
            return Qhelp::json_en([
                'num_creator'  => $num['num_creator'],
                'num_admin'    => $num['num_admin'],
                'num_editor'   => $num['num_editor'],
                'num_cuser'    => $num['num_user'],
                'reg_inday'    => $reg_num['regd'],
                'reg_inmonth'  => $reg_num['regm'],
            ]);
        }

        public function renwud($type){

            if($type < 1 || $type > 4){
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '信息不存在']);
            }

            $data = [];
            switch ($type) {
                case 1:
                    $data = Usermag_function::usercreator();
                    break;
                case 2:
                    $data = Usermag_function::useradmin();
                    break;
                case 3:
                    $data = Usermag_function::usereditor();
                    break;
                case 4:
                    $data = Usermag_function::cuser();
                    break;
            };

            foreach ($data as $k => $v){
                if($data[$k]['uid'] == User::ufetch()['uid']){
                    $data[$k]['is'] = 1;
                }
            }
            return Qhelp::json_en($data);
        }

        public function logic(){
            if (!empty($_POST['uid']) && is_numeric($_POST['uid']) && $_POST['uid'] > 0) { //判断是否发送了UID
                $current_user_pm = User::ufetch()['pm'];
                $beopped_user_pm = Db::query("select `promise` from qzlit_group where uid = '" . $_POST['uid'] . "'");
                if (!empty($beopped_user_pm)) { //判断是否在操作用户对象
                    if ($current_user_pm > $beopped_user_pm[0]['promise']) { //判断自身权限是否高于被操作用户权限  （用于只能由高于被操作者的权限者操作的信息）

                        /*******************************/
                        if (!empty($_POST['getuserpml'])) {return User::get_pml($_POST['uid']); } //获取用户细分权限
                        if (!empty($_POST['setuserpml'])) {return User::set_pml($_POST['uid'], $_POST['pml']);} //更新用户细分权限
                        if (!empty($_POST['getuserinfo'])) {return User::get_info($_POST['uid']); } //获取用户信息
                        if (!empty($_POST['deluser'])) {return User::userdel($_POST['uid']); } //删除用户
                        if (!empty($_POST['setuserregstat'])) {return User::userreg_switcher($_POST['uid']); } //切换禁封状态
                        return Qhelp::json_en(['Stat' => 'error', 'Message' => '权限不足']);
                        /*******************************/

                    } elseif ($_POST['uid'] == User::ufetch()['uid']) { //判断是否自己处理自己的信息

                        /*******************************/
                        if (!empty($_POST['setuserregstat'])) {return Qhelp::json_en(['Stat' => 'error', 'Message' => '禁止禁封/解禁自己']); } //切换禁封状态
                        if (!empty($_POST['getuserinfo'])) {return User::get_info($_POST['uid']); } //获取用户信息
                        return Qhelp::json_en(['Stat' => 'error', 'Message' => '权限不足']);
                        /*******************************/

                    } else {return Qhelp::json_en(['Stat' => 'error', 'Message' => '权限不足']);}
                } else {return Qhelp::json_en(['Stat' => 'error', 'Message' => '用户不存在']);}
            } elseif((!empty($_POST['upuser']) && is_numeric($_POST['upuser']) && $_POST['upuser'] > 0) || !empty($_POST['adduser'])) {

                /*******************************/
                if (!empty($_POST['saveuserinfo'])) {return User::userup($_POST['username'],$_POST['usergroup'],$_POST['password'],$_POST['realname'],$_POST['email'],$_POST['phone'],$_POST['party']); } //设置用户信息
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '修改用参数不完整']);
                /*******************************/

            } else {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '用户参数不合法']);
            }
        }
    }