<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-06-21
 *
 */
namespace qzxy;
use think\Controller;
use think\Db;

class User extends Controller {

    /* 获取当前管理员权限 */
    public static function ufetch() {
        session_start();
        if(!empty($_SESSION['uid'])){
            $user = Db::query("select * from qzlit_group where uid= '" . $_SESSION['uid'] . "'")[0];
            session_write_close();
            return [
                'uid' => $user['uid'],
                'username' => $user['username'],
                'party' => $user['party'],
                'pm' => $user['promise'],
                'pml' => Qhelp::json_de(htmlspecialchars_decode($user['pml'],ENT_QUOTES)),
                'ban' => $user['unreg']
            ];
        } else {
            session_write_close();
            return 0;
        }
    }

    /* 获取用户信息 */
    public static function get_info($uid){
        if(empty($uid) || !Qhelp::chk_pint($uid)){
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数不合法']);
        }
        $res = Db::query("select `uid`,`unreg`,`party`,`username`,`promise`,`regtime`,`phone`,`email`,`name` from qzlit_group where uid = '".$uid."'");
        if(!empty($res)){
            return Qhelp::json_en([
                'Stat' => 'OK',
                'Data' => Qhelp::json_en($res[0])
            ]);
        } else {
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '用户数据不存在']);
        }
    }

    /* 更新，注册用户 */
    public static function userup($username, $promise = 100, $password, $name, $email, $phone, $party = 0) {
        if($promise > 999 || !Qhelp::chk_pint($promise) ||
            (!empty($phone) && (!Qhelp::chk_pint($phone) || strlen($phone) > 15 || strlen($phone) < 6 || $phone < 1000000)) ||
            (!empty($party) && (!Qhelp::chk_pint($party) || $party > 20)) ||
            (!empty($_POST['upuser']) && !Qhelp::chk_pint($_POST['upuser']))){
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数不合法，请检查电话号码或者其他内容是否填写有误']);
        }

        $username = htmlspecialchars(Qhelp::dss($username),ENT_QUOTES);
        $name = htmlspecialchars(Qhelp::dss($name),ENT_QUOTES);
        $email = htmlspecialchars(Qhelp::dss($email),ENT_QUOTES);

        $pml_token = [1 => 'df_user',7 => 'df_editor',8 => 'df_admin',9 => 'df_all',];
        $pml = Db::query("select `data` from qzlit_config where `type` = 'Pm' AND `name` = '".$pml_token[(int)($promise/100)]."'")[0]['data'];


        @$self = self::ufetch();
        @$selfpromise = $self['pm'] ? $self['pm'] : 0;
        @$haveuser = Db::query("select username from qzlit_group where username= '" . $username . "'")[0];/* 返回是否存在用户名- 用户添加 */
        @$udres = Db::query("select * from qzlit_group where uid= '" . $_POST['upuser'] . "'")[0];/* 返回被操作用户的信息- 用户更新 */
        @$renew_pm = $promise == $udres['promise'] ? 0 : 1;
        $isself = $self['uid'] != $udres['uid'] ? 0 : 1; /* 判断操作对象是否是自己 */

        if (empty($username)) {
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '请填写用户名再执行操作']);
        } elseif ($self['pm'] != $selfpromise) {
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '页面出错，请确认自己的网络环境安全再进行操作']);
        } elseif (!empty($_POST['register']) && $promise != 0) { /*注册用户*/
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '页面出错，请确认自己的网络环境安全再进行操作']);
        } elseif (!empty($_POST['register']) && $haveuser) {
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '用户名已被使用，请重新填写']);
        } elseif (!empty($_POST['adduser']) && $haveuser) {     /*添加用户*/
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '用户名已被使用，请重新填写']);
        } elseif (!empty($_POST['adduser']) && $promise >= $selfpromise){
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '你不能将被操作者的权限提升至自己等级之上']);
        } elseif (!empty($_POST['upuser']) && !$isself && $selfpromise <= $udres['promise']) {  /*更新用户*/
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '当前权限不足以执行该操作']);
        } elseif (!empty($_POST['upuser']) && !$isself && $promise >= $selfpromise ){
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '你不能将被操作者的权限提升至自己等级以及之上']);
        } elseif (!empty($_POST['upuser']) && !$isself && $udres['username'] != $username && $haveuser) {
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '用户名已被使用，请重新填写']);
        } elseif (!empty($_POST['upuser']) && $isself && $selfpromise != $promise){
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '禁止更新自己的权限等级']);
        } elseif (!empty($_POST['upuser']) && $isself && $self['username'] != $username && $haveuser){
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '用户名已被使用，请重新填写']);
        } else {
            if (!empty($name) || !empty($email) || !empty($phone)) {
                if(!empty($_POST['upuser']) && empty($password)){
                    if($renew_pm){
                        Db::execute("update qzlit_group set `pml` = '$pml' where uid = '" . $_POST['upuser'] . "'");
                    }
                    Db::execute("update qzlit_group set `username` = '" . $username . "', `promise`= '" . $promise . "', `phone`='" . $phone . "', `email`='" . $email . "', `name`='" . $name . "', `party`='" . $party . "' where uid = '" . $_POST['upuser'] . "'");
                    Log::visit("consoleboard","manageuser","renew_".$username);
                    return Qhelp::json_en(['Stat' => 'OK', 'Message' => '用户信息更新成功']);
                } elseif (strlen($password) > 7 && !is_numeric($password)) {
                    $tmp = Sec::passhash($password);
                    $salt = $tmp['salt'];
                    $key = $tmp['key'];
                    if (!empty($_POST['adduser'])) {
                        Db::execute("INSERT INTO `qzlit_group`(`username`, `promise`,`pml`, `party`, `salt`, `key`, `phone`, `email`, `name`, `regtime`) VALUE ('" . $username . "','" . $promise . "','" . $pml . "','" . $party . "','" . $salt . "','" . $key . "','" . $phone . "','" . $email . "','" . $name . "','" . date("YmdHi", time()) . "')");
                        Log::visit("consoleboard","manageuser","add_".$username);
                        return Qhelp::json_en(['Stat' => 'OK', 'Message' => '用户添加成功']);
                    } elseif (!empty($_POST['register'])) {
                        Db::execute("INSERT INTO `qzlit_group`(`username`, `promise`,`pml`, `party`, `salt`, `key`, `phone`, `email`, `name`, `regtime`) VALUE ('" . $username . "','100','" . $pml . "','" . $party . "','" . $salt . "','" . $key . "','" . $phone . "','" . $email . "','" . $name . "','" . date("YmdHi", time()) . "')");
                        Log::visit("consoleboard","manageuser","reg_".$username);
                        return Qhelp::json_en(['Stat' => 'OK', 'Message' => '成功注册']);
                    } elseif (!empty($_POST['upuser'])) {
                        if($renew_pm){
                            Db::execute("update qzlit_group set `pml` = '" . $pml . "' where uid = '" . $_POST['upuser'] . "'");
                        }
                        Db::execute("update qzlit_group set `username` = '" . $username . "', `promise`= '" . $promise . "', `party`= '" . $party . "', `salt`='" . $salt . "', `key`='" . $key . "', `phone`='" . $phone . "', `email`='" . $email . "', `name`='" . $name . "' where uid = '" . $_POST['upuser'] . "'");
                        Log::visit("consoleboard","manageuser","renew_".$username);
                        return Qhelp::json_en(['Stat' => 'OK', 'Message' => '用户信息成功更新']);
                    } else {
                        return Qhelp::json_en(['Stat' => 'error', 'Message' => '未知错误']);
                    }

                } else {
                    return Qhelp::json_en(['Stat' => 'error', 'Message' => '密码强度太低，请保证密码至少为八位且不全为数字']);
                }
            } else {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '请填写 【姓名 / 邮箱 / 电话号码】 中至少一个信息，便于以后的安全设置']);
            }
        }
    }

    /* 删除用户 */
    public static function userdel($uid) {
        if(empty($uid) || !Qhelp::chk_pint($uid)){
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数不合法']);
        }

        $res = Db::query("select promise from qzlit_group where uid = '" . $uid . "'");
        if(!empty($res)){
            if ($res[0]['promise'] != 999) {
                if ($res[0]['promise'] < User::ufetch()['pm']) {
                    Db::execute("delete from qzlit_group WHERE uid = '" .$uid . "'");
                    Log::visit("consoleboard","manageuser","del_".$uid);
                    return Qhelp::json_en(['Stat' => 'OK', 'Message' => '删除成功']);
                } else {
                    return Qhelp::json_en(['Stat' => 'error', 'Message' => '无权操作比自己权限高的用户']);
                }
            } else {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '该用户拥有最高权限，无法删除']);
            }
        } else {
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '数据不存在']);
        }
    }

    /* 切换用户的禁用状态 */
    public static function userreg_switcher($uid) {
        if(empty($uid) || !Qhelp::chk_pint($uid)){
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数不合法']);
        }

        $res = Db::query("select * from qzlit_group where uid = '" . $uid . "'");
        if ($res[0]['promise'] != 999) {
            if ($res[0]['promise'] < User::ufetch()['pm']) {
                if($res[0]['unreg'] == 1){
                    Db::execute("update qzlit_group set `unreg` = NULL where `uid` = $uid");
                    Log::visit("consoleboard","manageuser","un_reg_".$uid);
                    return Qhelp::json_en(['Stat' => 'OK', 'Message' => '用户解禁成功']);
                } else {
                    Db::execute("update qzlit_group set `unreg` = 1 where `uid` = $uid");
                    Log::visit("consoleboard","manageuser","re_reg_".$uid);
                    return Qhelp::json_en(['Stat' => 'OK', 'Message' => '用户禁用成功']);
                }
            } else {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '无权操作比自己权限高的用户']);
            }
        } else {
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '该用户拥有最高权限，无法禁封']);
        }
    }

    /*------------------------------------------------------------------------------------------------------------------
     * 用户细分权限设定
     *--------------------------------------------------------------------------------------------------------------- */

    /* 定义细分权限 */
    public static $pml_setting = [
        /* 你可以在 pml_setting 里面添加新的条目来为用户添加新的权限值。注意按照规则来写。
         * 添加的权限需要在 has_pm 里面写入判定规则，请务必小心！
         * ------------------------------------------------------------------
         * 权限基本设定 type权限存储类型，pmin使用该权限的最低值，psolid高于该权限必拥有*/
        'site_visite'=>     ['type' => 'boolean', 'name'=>'访问网站',    'pmin' => '0',  'psolid' => '700','df' => false],
        'thread_visite'=>   ['type' => 'boolean', 'name'=>'查看文章',    'pmin' => '0',  'psolid' => '700','df' => false],
        'thread_subscrib'=> ['type' => 'boolean', 'name'=>'评论文章',    'pmin' => '0',  'psolid' => '700','df' => false],
        'user_subscrip'=>   ['type' => 'boolean', 'name'=>'评论用户评论', 'pmin' => '0',  'psolid' => '700','df' => false],

        'thread_mag'=>      ['type' => 'boolean', 'name'=>'增改文章',    'pmin' => '700',  'psolid' => '800','df' => false],
        'user_mag'=>        ['type' => 'boolean', 'name'=>'管理用户',    'pmin' => '800',  'psolid' => '900','df' => false],
        'chunk_mag'=>       ['type' => 'boolean', 'name'=>'板块管理',    'pmin' => '900',  'psolid' => '999','df' => false],
        'nav_mag'=>         ['type' => 'boolean', 'name'=>'修改导航',    'pmin' => '900',  'psolid' => '999','df' => false],
        'sms_use'=>         ['type' => 'boolean', 'name'=>'发送短信',    'pmin' => '800',  'psolid' => '999','df' => false],
        'enroll_use'=>      ['type' => 'boolean', 'name'=>'使用报名系统', 'pmin' => '800',  'psolid' => '999','df' => false],
        'config_mag'=>      ['type' => 'boolean', 'name'=>'修改网站配置', 'pmin' => '900',  'psolid' => '999','df' => false],
        'chunk_ct_mag'=>    ['type' => 'char',    'name'=>'管理板块内容', 'pmin' => '700',  'psolid' => '800','df' => '']
    ];

    /* 检查用户权限 */
    public static function has_pm($pname,$data = 0){
        $ud = self::ufetch();
        if(empty($ud)){return 0;}
        $upm = $ud['pm'];
        /*----------------------------------------
         *  纯值依赖的判断，用于网站通行
         * */

        if(in_array($pname,['is_admin'])){
            switch ($pname){
                case 'is_admin':/*是否管理员*/
                    return $upm >= 700 ? 1 : 0;
                    break;
                default: return 0;
            }
        }
        /*----------------------------------------
         *  细分权限判断
         * */
        /* 用户的细分权限值 *//* 获取细分权限对应的最小权限值和固有权限值 */
        $res = $ud['pml'][$pname];
        $pm_min = self::$pml_setting[$pname]['pmin'];
        $pm_std = self::$pml_setting[$pname]['psolid'];

        switch ($pname) {
            /* 普通用户 */
            case 'site_visite': /*浏览网站*/
            case 'thread_visite':/*浏览文章*/
            case 'thread_subscrib':/*评论*/
            case 'user_subscrip':/*评论用户*/
                return ($res['value'] || $upm >= $pm_min) ? 1 : 0;
                break;

            /*管理员*/
            case 'sms_use':/*使用短信权力*/
            case 'enroll_use':/*参与报名管理权力*/
            case 'config_mag':/*配置管理*/
            case 'user_mag':/*用户管理*/
            case 'nav_mag':/*导航管理*/
            case 'thread_mag':/*文章管理*/
            case 'chunk_mag':/*板块管理*/
                return (($res['value'] && $upm >= $pm_min) || $upm >= $pm_std) ? 1 : 0;
                break;
            case 'chunk_ct_mag':/*板块内容管理*/
                /* 需要同时拥有 增删改文章 权限 */
                if ($upm >= $pm_std){
                    return 1;
                }
                if ($upm >= $pm_min){
                    $cid = $data;
                    $tes = []; /* keep foreach to run normarly */
                    $tes = array_merge($tes,explode(',',$res['value']));
                    foreach ($tes as  $v){
                        $cs = Filter::sample(Db::query("select id from qzlit_chunk where chunk_below = '“”".$v."'"),'id');
                        $tes = array_merge($tes,$cs);
                        foreach ($cs as $v2){
                            $cs2 = Filter::sample(Db::query("select id from qzlit_chunk where chunk_below = '".$v2."'" ),'id');
                            $tes = array_merge($tes,$cs2);
                        }
                    }
                    if(in_array($cid,$tes) && self::has_pm('thread_mag')){
                        return 1;
                    }
                }
                return 0;
                break;
            default: return 0;
        }
    }

    /* 获取用户细分权限 */
    public static function get_pml($uid){
        if(empty($uid) || !Qhelp::chk_pint($uid)){
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数不合法']);
        }

        $res = Db::query("select `pml` from qzlit_group where uid = '".$uid."'");
        if(!empty($res)){
            Log::visit("consoleboard","manageuser","getpml_".$uid);
            return Qhelp::json_en([
                'Stat' => 'OK',
                'Message' => '获取成功',
                'Data' => htmlspecialchars_decode($res[0]['pml'],ENT_QUOTES)
            ]);
        } else {return Qhelp::json_en(['Stat' => 'error', 'Message' => '用户数据不存在']);}
    }

    /* 保存用户细分权限 */
    public static function set_pml($uid,$pml_json){
        if(empty($uid) || !Qhelp::chk_pint($uid)){
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '参数不合法']);
        }
        // 防止篡改ID导致的权限错乱
        if($upm = Db::query("select `promise` from qzlit_group where uid= '".$uid."'")){
            $upm = $upm[0]['promise'];
        } else {
            return Qhelp::json_en(['Stat' => 'error', 'Message' => '用户不存在']);
        }

        $pml = Qhelp::json_de($pml_json);

        /* 获取系统权限条目 */
        $pml_keys = [];
        foreach (self::$pml_setting as $key => $value) {$pml_keys[] = $key;}

        /* 清除篡改页面产生的权限 */
        foreach($pml as $key => $value) {
            if (!in_array($key, $pml_keys)) {
                unset($pml[$key]);
            }
        }

        /* 获取清理后的权限条目 */
        $pml_ukeys = [];
        foreach ($pml as $key => $value) {$pml_ukeys[] = $key;}

        /* 恢复被篡改前的默认值 */
        foreach($pml_keys as $value) {
            if (!in_array($value, $pml_ukeys)) {
                $pml[$value] = [
                    'value' => self::$pml_setting[$key]['df'],
                ];
            }
        }

        foreach ($pml as $key => $value) {
            if (!in_array($key, $pml_keys)) {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '保存超时，请刷新页面后在进行操作']);
            }
            if (preg_match("/[\'a-zA-Z.。:：;；*?？·~‘’`!@#$%^&+=)(《》<>{}]|\]|\[|\/|\\\|\"|\|/", $pml[$key]['value'])) {
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '输入的文本内容不规范，请重写填写']);
            }

            /* 格式化权限数据内容 */
            $pml[$key]['token'] = $key;
            $pml[$key]['type'] = self::$pml_setting[$key]['type'];
            $pml[$key]['name'] = self::$pml_setting[$key]['name'];
            $pml[$key]['sort'] = self::$pml_setting[$key]['pmin'] < 700 ? 'user' : 'admin';
            $pml[$key]['value'] = str_replace('，', ',', $pml[$key]['value']);

            /* 判断固有权限 */
            if ($upm < self::$pml_setting[$key]['pmin']) {
                $pml[$key]['solid'] = true;
                $pml[$key]['value'] = self::$pml_setting[$key]['type'] == 'boolean' ? false : '';
            } elseif ($upm >= self::$pml_setting[$key]['psolid']) {
                $pml[$key]['solid'] = true;
                $pml[$key]['value'] = self::$pml_setting[$key]['type'] == 'boolean' ? true : '';
            }
        }

        // 存储权限设置
        $pml_json = htmlspecialchars(Qhelp::json_en($pml), ENT_QUOTES);
        Db::execute("update qzlit_group set `pml` = '" . $pml_json . "' where uid = '" . $uid . "'");
        Log::visit("consoleboard", "manageuser", "setpml_" . $uid);
        return Qhelp::json_en(['Stat' => 'OK', 'Message' => '保存成功']);
    }
}