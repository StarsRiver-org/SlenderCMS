<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-08-05
 *
 */
namespace qzxy;
use think\Controller;
use think\Db;


class Thread extends Controller {
    /* 添加/更新文章 */
    public static function sender() {
        if(!User::has_pm('chunk_ct_mag',$_POST['threadsort'])){
            return "请选择正确文章的分类";
        }

        $E = User::ufetch();
        if (!empty($_POST['newthread']) || (!empty($_POST['renewthread']) && Qhelp::chk_pint($_POST['renewthread']) && $_POST['renewthread'] > 0)) { /*post*/
            if (empty($_POST['threadsort']) || !Qhelp::chk_pint($_POST['threadsort'])) {
                return "请选择正确文章的分类";
            }
            if (empty($_POST['threadmode']) || !Qhelp::chk_pint($_POST['threadmode']) || $_POST['threadmode'] < 1) {
                return "请选择文章的保存模式";
            }
            if (empty($_POST['title'])) {
                return "必须包含标题";
            }
            if (strlen(addslashes($_POST['title'])) > 255) {
                return "标题长度不得超过88个字(255字节)";
            }
            if (empty($_POST['content']) || strlen($_POST['content']) < 64) {
                return "内容不得少于64个字节";
            }

            if (request()->file('coverimg')) {/* coverimg */
                if (!empty($_POST['renewthread']) && !empty($_POST['has_coverimg'])) { /* update cover */
                    $img = Db::query("select thread_coverimg from qzlit_thread WHERE cuid = '" . (int)$_POST['renewthread'] . "'")[0]['thread_coverimg'];
                    if (!empty($img)) {
                        @unlink(ROOT_PATH . 'data/catch/temp/img/' . substr($img, 0, 8) . '/' . substr($img, 8));
                    }
                    Db::execute("update qzlit_thread set thread_coverimg = NUll WHERE cuid = '" . (int)$_POST['renewthread'] . "'");
                }
                $newimg = File::saveimg('coverimg');
                if ($newimg == 0) {
                    return '文件错误';
                } else {
                    $coverimg = $newimg;
                }
            } elseif (!empty($_POST['renewthread'])) {
                $coverimg = Db::query("select thread_coverimg from qzlit_thread WHERE cuid = '" . (int)$_POST['renewthread'] . "'")[0]['thread_coverimg'];
            }

            @$thread = [
                'title' => htmlspecialchars(Qhelp::dss($_POST['title']),ENT_QUOTES),
                'coverimg' => $coverimg,
                'content' => htmlspecialchars(Qhelp::dss($_POST['content']),ENT_QUOTES),
                'editor' => $E['uid'],
                'author' => htmlspecialchars(Qhelp::dss($_POST['author']),ENT_QUOTES),
                'time' => time(),
                'htime' => mktime(substr($_POST['htime'],'11','2'),substr($_POST['htime'],'14','2'),0,substr($_POST['htime'],'5','2'),substr($_POST['htime'],'8','2'),substr($_POST['htime'],'0','4')),
                'keyword' => htmlspecialchars(Qhelp::dss($_POST['keyword']),ENT_QUOTES),
                'descrip' => htmlspecialchars(Qhelp::dss($_POST['descrip']),ENT_QUOTES),
                'threadmode' => $_POST['threadmode'],
                'threadsort' => $_POST['threadsort'],
            ];
            if (!empty($_POST['newthread'])) { /* 如果发布新文章，收录文章 */
                Db::execute("INSERT INTO 
                qzlit_thread (
                        thread_title, 
                        thread_coverimg, 
                        thread_context,
                        thread_editor,
                        thread_author,
                        thread_ctime, 
                        thread_ptime, 
                        thread_htime, 
                        hk_keywords, 
                        hk_descrip, 
                        hk_mode, 
                        hk_sort
                )
                VALUES (
                     '" . $thread['title'] . "',
                     '" . $thread['coverimg'] . "', 
                     '" . $thread['content'] . "',
                     '" . $thread['editor'] . "',
                     '" . $thread['author'] . "',
                     '" . $thread['time'] . "',
                     '" . $thread['time'] . "',
                     '" . $thread['htime'] . "',
                     '" . $thread['keyword'] . "', 
                     '" . $thread['descrip'] . "', 
                     '" . $thread['threadmode'] . "',
                     '" . $thread['threadsort'] . "'
                )
            ");
                return'成功发布,你可以继续添加或刷新页面查看结果';
            } elseif (!empty($_POST['renewthread'])) {
                Db::execute("update qzlit_thread set
                `thread_title` = '" . $thread['title'] . "', 
                `thread_coverimg` = '" . $thread['coverimg'] . "',  
                `thread_context` ='" . $thread['content'] . "',
                `thread_editor` ='" . $thread['editor'] . "',
                `thread_author` ='" . $thread['author'] . "',
                `thread_ctime` ='" . $thread['time'] . "',
                `thread_htime` ='" . $thread['htime'] . "',
                `hk_keywords` ='" . $thread['keyword'] . "', 
                `hk_descrip` ='" . $thread['descrip'] . "', 
                `hk_mode` ='" . $thread['threadmode'] . "',
                `hk_sort` ='" . $thread['threadsort'] . "'
                where cuid = '" . $_POST['renewthread'] . "'");
                return "修改成功";
            }

        }
        return '页面出错，请重重刷新页面再次尝试';
    }

    /* 载入某篇文章 , 注意在使用该函数的地方进行安全性检查
     *
     * @parse cuid (int)
     */
    public static function loadone($cuid) {
        if(!Qhelp::chk_pint($cuid) || $cuid <= 0){
            return null;
        }
        $res = Db::query("SELECT * FROM qzlit_thread WHERE cuid = '" . $cuid . "'");
        if(!empty($res)){
            return self::format($res[0],'all');
        } else {
            return null;
        }
    }

    /* 加载文章，用于程序内部，无需安全验证 */
    public static function loadlist($arr, $limit = 20){
        $list = [];
        $argumets = '`cuid`, `thread_title`, `thread_author`, `thread_editor`, `thread_coverimg`,`thread_htime`,`thread_ctime`, `thread_ptime`, `hk_sort`,`hk_mode`, `hk_descrip`,`hk_keywords`,`ore_degree`,`ore_view`';
        if(is_array($arr)){
            foreach ($arr as $key=>$val){
                $res = Db::query("select $argumets from qzlit_thread WHERE hk_sort = $val AND hk_mode = 2 order by thread_ptime desc limit $limit");
                foreach($res as $l){
                    $list[$key][] = self::format($l,'more');
                }
            }
        } else {
            $res = Db::query("select $argumets from qzlit_thread WHERE hk_sort = $arr AND hk_mode = 2 order by thread_ptime desc limit $limit");
            foreach($res as $l){
                $list[] = self::format($l,'more');
            }
        }
        return $list;
    }

    /* 格式化文章内容
        param [$type == 0]     => 默认模式，全部格式化：用于文章展示
        param [$type == 'sim'] => 后台统计时使用,节约开销
    */
    public static function format($res, $type = 0){
        @$date = $res['thread_htime'] ? $res['thread_htime'] : ($res['thread_ctime'] ? $res['thread_ctime'] : $res['thread_ptime']);
        $ut = Db::query("SELECT * FROM qzlit_group WHERE uid = '" . $res['thread_editor'] . "'");
        $ut = !empty($ut) ? $ut[0] : $ut =['name' => '匿名','username' => '匿名'];

        switch ($type){
            case 'sample':
                $return = [
                    'id' => $res['cuid'],
                    'title' => !empty($res['thread_title']) ? htmlspecialchars_decode($res['thread_title'],ENT_QUOTES) : '',
                    'time' => date('Y-m-d H:i', $date),
                    'timestamp' => $date,
                    'sort' => !empty($res['hk_sort']) ? $res['hk_sort'] : '',
                    'mode' => !empty($res['hk_mode']) ? $res['hk_mode'] : '' ,
                    'editor' => $ut['username'],
                ];
                break;

            case 'more':
                $return = [
                    'id' => $res['cuid'],
                    'title' => !empty($res['thread_title']) ? htmlspecialchars_decode($res['thread_title'],ENT_QUOTES) : '',
                    'time' => date('Y-m-d H:i', $date),
                    'timestamp' => $date,
                    'coverimg' =>  @Qhelp::checkpic($res['thread_coverimg']) ? File::fetchimg($res['thread_coverimg']) : STATIC_ROOT.'/img/common/no-img-1.svg',
                    'keyword' => !empty($res['hk_keywords']) ? htmlspecialchars_decode($res['hk_keywords'],ENT_QUOTES) : '',
                    'descrip' => !empty($res['hk_descrip']) ? htmlspecialchars_decode($res['hk_descrip'],ENT_QUOTES) : '',
                    'sort' => !empty($res['hk_sort']) ? $res['hk_sort'] : '',
                    'hot' => !empty($res['ore_hot']) ? $res['ore_hot'] : 0,
                    'view' => !empty($res['ore_view']) ? $res['ore_view'] : 0,
                    'deg' => !empty($res['ore_degree']) ? $res['ore_degree'] :0,
                    'mode' => !empty($res['hk_mode']) ? $res['hk_mode'] : '' ,
                    'author' => !empty($res['thread_author']) ? $res['thread_author'] : ($ut['name'] ? $ut['name'] : $ut['username']),
                    'editor' => $ut['username'],
                ];
                break;

            case 'all':
                $like = Db::query("select `func` from qzlit_log where ip = '" . htmlspecialchars(Qhelp::dss(Ip::getip()), ENT_QUOTES) . "' AND target = 'article' AND `data` = '" . $res['cuid'] . "'  AND func like '%like%' order by time DESC limit 1");
                $liked = false;
                if ($like && $like[0]['func'] == 'like') {
                    $liked = true;
                }
                $return =[
                    'id' => $res['cuid'],
                    'title' => !empty($res['thread_title']) ? htmlspecialchars_decode($res['thread_title'],ENT_QUOTES) : '',
                    'time' => date('Y-m-d H:i', $date),
                    'timestamp' => $date,
                    'coverimg' =>  @Qhelp::checkpic($res['thread_coverimg']) ? File::fetchimg($res['thread_coverimg']) : STATIC_ROOT.'/img/common/no-img-1.svg',
                    'content' => !empty($res['thread_context']) ? htmlspecialchars_decode($res['thread_context'],ENT_QUOTES) : '',
                    'keyword' => !empty($res['hk_keywords']) ? htmlspecialchars_decode($res['hk_keywords'],ENT_QUOTES) : '',
                    'descrip' => !empty($res['hk_descrip']) ? htmlspecialchars_decode($res['hk_descrip'],ENT_QUOTES) : '',
                    'sort' => !empty($res['hk_sort']) ? $res['hk_sort'] : '',
                    'hot' => !empty($res['ore_hot']) ? $res['ore_hot'] : 0,
                    'view' => !empty($res['ore_view']) ? $res['ore_view'] : 0,
                    'deg' => !empty($res['ore_degree']) ? $res['ore_degree'] :0,
                    'mode' => !empty($res['hk_mode']) ? $res['hk_mode'] : '' ,
                    'author' => !empty($res['thread_author']) ? $res['thread_author'] : ($ut['name'] ? $ut['name'] : $ut['username']),
                    'editor' => $ut['username'],
                    'like'=> $liked,
                ];

        }
        return $return;
    }

    /* 文章浏览+1, 注意在使用该函数的地方进行安全性检查
     *
     * @parse cuid (int)
     */
    public static function visit($cuid){
        $func = 'visit';
        $logs = Db::query("select * from qzlit_log where ip = '".htmlspecialchars(Qhelp::dss(Ip::getip()),ENT_QUOTES)."' AND target = 'article' AND `data` = '".$cuid."' AND `time` > '".(time() - 3600*24)."' AND `func` = '".$func."' ORDER BY `time` DESC");
        /*每个ip每天可以让浏览量+1*/
        if(empty($logs) || time() - $logs[0]['time'] > 3600*24){
            $data = Db::query("select `ore_view` from qzlit_thread where cuid = '".$cuid."'");
            if($data) {
                $view = $data[0]['ore_view'];
                if(!$view){
                    $view = 1;
                } else{
                    $view +=1;
                }
                Db::execute("update qzlit_thread set `ore_view` = '" . $view . "' where cuid = '" . $cuid . "'");
                Log::visit("article", "$cuid", "$func");
            }
        } else {
            Log::visit("article", "$cuid", "");
        }
    }

    /* 文章赞操作, 注意在使用该函数的地方进行安全性检查
     *
     * @parse cuid (int)
     */
    public static function like($cuid){
        $logs = Db::query("select `time`,`func` from qzlit_log where ip = '".Ip::getip()."' AND target = 'article' AND `data` = '".$cuid."'  AND func like '%like%' ORDER BY time DESC limit 1");
        if(!$logs || $logs[0]['time']+3 < time()){
            $query = Db::query("select `ore_degree` from qzlit_thread where cuid = '".$cuid."'");
            $like = $query ? $query[0]['ore_degree'] : 0;
            $log = Ip::ipinfo();
            $log['target'] = "article";
            $log['data'] = $cuid ;
            $log['get'] = "like = $cuid" ;
            $log['post'] = '' ;
            if(!$logs || $logs[0]['func'] == 'unlike'){
                if(!$like){$like = 1;} else {$like +=1;}
                $log['func'] = 'like';
            } else {
                if($like){$like -=1;}
                $log['func'] = 'unlike';
            }
            if(Db::execute("update qzlit_thread set `ore_degree` = '" . $like . "' where cuid = '" . $cuid . "'") && Log::updata_log($log)){
                return 'success';
            } else {
                return 'error';
            }
        } else {
            return 'timeout';
        }
    }

    /*最新文章*/
    public static function newest($cuid = null){
        if(!$cuid){
            $res = Db::query("select * from qzlit_thread WHERE hk_mode = 2 ORDER BY thread_ptime DESC LIMIT 8");
        } else {
            $res = Db::query("select * from qzlit_thread WHERE hk_mode = 2 AND hk_sort = $cuid ORDER BY thread_ptime DESC LIMIT 8");
        }
        $data = [];
        for ($i = 0 ; $i < count($res); $i++){
            $data[$i] = Thread::format($res[$i],'more');
        }
        return $data;
    }
}