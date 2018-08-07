<?php

    class all {
        public static function refrash() {
            $current_page = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/') + 1);
            echo "<script>location.href='" . $current_page . "'</script>";
        }

        public static function reset() {
            setrawcookie('progress');
        }

        public static function start() {
            include('int_lang.php');
            $lock_file = dirname(__FILE__) . '\install.lock';
            if (file_exists($lock_file)) {
                echo '<a href="//'.SITE.'/" class="bigbtn">' . $lang['hasinstall'] . '</a>';
            } else {
                echo '<h3>' . $lang['noinstall'] . '</h3>
                      <div class="lic">
                        <a>' . $lang['lic_content'] . '</a>
                      </div>
                      <form method="post" >
                        <a><input name="agree" type="checkbox" class="line" style="vertical-align: -5%">' . $lang['agree'] . '</a>
                        <button type="submit" class="btn r">' . $lang['nowinstall'] . '</button>
                      </form>';
                if (empty($_POST['agree'])) {
                    echo $lang['read'];
                } else {
                    setrawcookie('progress', 1);
                    all::refrash();
                }
            }
        }

        public static function dbset() {
            include('int_lang.php');
            echo '<h3>' . $lang['pdsetting'] . '</h3>
                    <form method="post">
                        <div class="lic">
                            <input name="testing" value="ysert7834otrjf" type="hidden">
                            <a class="perbox">' . $lang['pdbname'] . '</a><input name="pdbname" type="text"><a class="tip">' . $lang['tipofdbname'] . '<a/></br>
                            <a class="perbox">' . $lang['pdbhost'] . '</a><input name="pdbhost" type="text"><a class="tip">' . $lang['tipofdbhost'] . '<a/></br>
                            <a class="perbox">' . $lang['pdbuser'] . '</a><input name="pdbuser" type="text"><a class="tip">' . $lang['tipofdbuser'] . '<a/></br>
                            <a class="perbox">' . $lang['pdbpass'] . '</a><input name="pdbpass" type="password"><a class="tip">' . $lang['tipofdbpass'] . '<a/></br>
                        </div>
                        <button type="submit" class="btn r">' . $lang['continue'] . '</button>
                    </form>
                ';
            if (!empty($_POST['pdbname']) && !empty($_POST['pdbhost']) && !empty($_POST['pdbuser']) && !empty($_POST['pdbpass'])) {
                if (!empty($_POST['testing']) && $_POST['testing'] == 'ysert7834otrjf') {
                    echo '<a class="btn r cover">' . $lang['waiting'] . '</a>';
                    $__config   =
                        '<?php
    // 直接引用设置
    define( \'DB_host\' ,\'' . $_POST['pdbhost'] . '\');
    define( \'DB_user\' ,\'' . $_POST['pdbuser'] . '\');
    define( \'DB_name\' ,\'' . $_POST['pdbname'] . '\');
    define( \'DB_pass\' ,\'' . $_POST['pdbpass'] . '\');
    define( \'DB_perfix\' ,\'qzlit_\');
    define( \'DB_haset\' ,\'utf8\');

    return [
        // 数据库类型
        \'type\'            => \'mysql\',
        // 服务器地址
        \'hostname\'        => \'' . $_POST['pdbhost'] . '\',
        // 数据库名
        \'database\'        => \'' . $_POST['pdbname'] . '\',
        // 用户名
        \'username\'        => \'' . $_POST['pdbuser'] . '\',
        // 密码
        \'password\'        => \'' . $_POST['pdbpass'] . '\',
        // 端口
        \'hostport\'        => \'\',
        // 连接dsn
        \'dsn\'             => \'\',
        // 数据库连接参数
        \'params\'          => [],
        // 数据库编码默认采用utf8
        \'charset\'         => \'utf8\',
        // 数据库表前缀
        \'prefix\'          => \'qzlit_\',
        // 数据库调试模式
        \'debug\'           => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        \'deploy\'          => 0,
        // 数据库读写是否分离 主从式有效
        \'rw_separate\'     => false,
        // 读写分离后 主服务器数量
        \'master_num\'      => 1,
        // 指定从服务器序号
        \'slave_no\'        => \'\',
        // 是否严格检查字段是否存在
        \'fields_strict\'   => true,
        // 数据集返回类型
        \'resultset_type\'  => \'array\',
        // 自动写入时间戳字段
        \'auto_timestamp\'  => false,
        // 时间字段取出后的默认时间格式
        \'datetime_format\' => \'Y-m-d H:i:s\',
        // 是否需要进行SQL性能分析
        \'sql_explain\'     => false,
        // Builder类
        \'builder\'         => \'\',
        // Query类
        \'query\'           => \'\\think\\db\\Query\',
    ];
?>';
                    $configfile = fopen("../config/database.php", 'w');
                    fwrite($configfile, $__config);
                    fclose($configfile);
                    setrawcookie("progress", "2");
                    all::refrash();
                }
            }
        }

        public static function checkev() {
            include('int_lang.php');
            include('../config/database.php');

            $reqphp    = '5.3';
            $reqapache = '2';
            $reqmysql  = '5.02';
            $reqroom   = '0.2';


            $apacheinfo    = apache_get_version();
            $apacheversion =  $apacheinfo ;

            $link   = @mysqli_connect(DB_host, DB_user, DB_pass);
            $mvtemp = @sprintf("Server version:%s\n", mysqli_get_server_info($link));
            $mv     = substr($mvtemp, 15, 20);

            $roomtemp = disk_free_space("../");
            $roomtemp = $roomtemp / 1024 / 1024 / 1024;
            $room     = substr($roomtemp, 0, 5);

            $testfile   = '../favicon.ico';
            $phpable    = $reqphp < PHP_VERSION ? '<a class="ok r">通过</a>' : '<a class="error r">错误：php版本过低</a>';
            $apacheable = $reqapache < $apacheversion ? '<a class="ok r">通过</a>' : '<a class="error r">错误：apache版本过低</a>'.$apacheversion;
            $mysqlable  = $reqmysql < $mv ? '<a class="ok r">通过</a>' : '<a class="error r">错误，数据库访问失败</a>';
            $roomable   = $reqroom < $room ? '<a class="ok r">通过</a>' : '<a class="error r">错误：存储空间不足</a>';
            $read       = is_readable($testfile) ? '<a class="ok r">可写</a>' : '<a class="error r">错误：程序没有写入权限</a>';
            $wite       = is_writable($testfile) ? '<a class="ok r">可读</a>' : '<a class="error r">错误：程序没有读取权限</a>';
            $GD         = function_exists('gd_info') ? '<a class="ok r">可用</a>' : '<a class="error r">请开启GD库</a>';
            $pdo_mysql  = @ new pdo('mysql:host = ' . DB_host . '', DB_user, DB_pass);
            $PDO        = @ $pdo_mysql ? '<a class="ok r">可用</a>' : '<a class="error r">请开启PDO支持</a>';
    
            echo '<a class="tb">' . $lang['phpversion'] . '</a>  <a class="tb">' . $lang['prev'] . PHP_VERSION . '</a>' . $phpable . '</br>';
            echo '<a class="tb">' . $lang['mysqlversion'] . '</a><a class="tb">' . $lang['prev'] . $mv . '</a>' . $mysqlable . '</br>';
            echo '<a class="tb">' . $lang['apachserver'] . '</a> <a class="tb">' . $lang['prev'] . $apacheversion . '</a>' . $apacheable . '</br>';
            echo '<a class="tb">' . $lang['room'] . '</a> <a>' . $lang['prev'] . $room . 'GB</a>' . $roomable . '</br>';
            echo '<a class="tb">' . $lang['rpermission'] . '</a><a class="tb">&nbsp;</a>' . $read . '</br>';
            echo '<a class="tb">' . $lang['wpermission'] . '</a><a class="tb">&nbsp;</a>' . $wite . '</br>';
            echo '<a class="tb">&nbsp;</a><a class="tb">&nbsp;</a></br>';
            echo '<a class="tb">' . $lang['openGD'] . '</a><a class="tb">&nbsp;</a>' . $GD . '</br>';
            echo '<a class="tb">' . $lang['openpdo'] . '</a><a class="tb">&nbsp;</a>' . $PDO . '</br>';

            if (isset($_POST['backdbset']) && $_POST['backdbset'] == '1') {
                setrawcookie("progress", '1');
                all::refrash();
            }
            if ($reqphp < PHP_VERSION && $reqapache < $apacheversion && $reqmysql < $mv && $reqroom < $room && is_readable($testfile) && is_writable($testfile) && function_exists('gd_info')) {
                echo '<form method="post">
                        <button class="bigbtn" name="next" value="insert">' . $lang['caninstall'] . '</button>
                      </form>';
                if (isset($_POST['next']) && $_POST['next'] == 'insert') {
                    setrawcookie('progress', 3);
                    all::refrash();
                }
            } elseif ($reqphp < PHP_VERSION && $reqapache < $apacheversion && $reqroom < $room && is_readable($testfile) && is_writable($testfile) && function_exists('gd_info')) {
                echo '<form method="post"><button class="bigbtn" name="backdbset" value="1">数据库访问失败，返回修改参数</button></form>';
            } else {
                echo '<a class="bigbtn">' . $lang['cantinstall'] . '</a>';
                all::reset();
            }

        }

        public static function insert() {
            include 'int_lang.php';
            include '../config/database.php';
            $connection = new pdo('mysql:host=' . DB_host . '', DB_user, DB_pass);
            $db         = 'create database ' . DB_name;
            $connection->exec("$db");
            echo '<div class = "lic">正在检查数据库...</br>';
            $dns        = 'mysql:host=' . DB_host . ';dbname=' . DB_name;
            $connection = new pdo("$dns", DB_user, DB_pass);
            $_sql       = file_get_contents('data/install.sql');
            $_arr       = explode(';'.PHP_EOL, $_sql);
            echo '正在写入数据库关键信息</br>';
            foreach ($_arr as $_value) {
                $connection->query("$_value".";");
            }
            echo '数据库配置完成</br></div>';
            echo '<form method="post"><button class="bigbtn" name="next" value="addadmin">继续</button></form>';
            if (isset($_POST['next']) && $_POST['next'] == 'addadmin') {
                setrawcookie('progress', 4);
                all::refrash();
            }
        }

        public static function addadmin() {
            include '../config/database.php';
            echo '<form method="post" class="lic">
                    <a class="perbox">管理员昵称</a><input name="admin" type="text"><a class="tip">用户名登陆也是作为登陆的一种类型</a><br/>
                    <a class="perbox">管理员密码</a><input name="pass" type="password"><a class="tip">由于管理员账号的特殊性，这里不对密码进行安全性检验，请自行使用复杂密码</a><br/>
                    <a class="perbox">管理员名字</a><input name="name" type="text"><a class="tip">管理员真实姓名</a><br/>
                    <a class="perbox">管理员邮箱</a><input name="email" type="text"><a class="tip">管理员账号的邮箱，用于收集建议和反馈</a><br/>
                    <a class="perbox">管理员电话</a><input name="phone" type="text"><a class="tip">请输入有效的电话号码</a><br/>
                    <button type="submit" class="bigbtn">提交</button>
                  </form>';
            if(!empty($_POST['admin'])){
                $str      = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQLSTUVWXYZ0123456789';
                $salt     = '';
                for ($i = 1; $i < 16; $i++) {
                    $od     = rand(0, 61);
                    $letter = substr($str, $od, 1);
                    $salt   = $salt . $letter;
                }
                $mdpass = md5(md5($salt . $_POST['pass'] . 'tyutqzxy'));
                $dns        = 'mysql:host=' . DB_host . ';dbname=' . DB_name;
                $connection = new pdo("$dns", DB_user, DB_pass);
                $res = $connection -> exec("UPDATE `qzlit_group` SET `username`='".$_POST['admin']."', `salt` = '".$salt."', `key`= '".$mdpass."', `email`= '".$_POST['email']."',phone= '".$_POST['phone']."', name= '".$_POST['name']."' WHERE username = 'admin'");

                setrawcookie('progress', 5);
                all::refrash();
            }
        }
        public static function lockinstall() {
            $lock = fopen("install.lock", 'w');
            fwrite($lock, "system has been installed");
            fclose($lock);
            include 'int_lang.php';
            echo '<form method="post"><button class="bigbtn" name="next" value="finish">' . $lang['finished'] . '</button></form>';
            if (isset($_POST['next']) && $_POST['next'] == 'finish') {
                all::reset();
                all::refrash();
            }
        }
    }
