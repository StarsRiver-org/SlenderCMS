<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-07-05
 *
 */
    namespace qzxy\consoleboard\controller;
    use qzxy\Thread;
    use think\Db;
    use qzxy\User;
    use qzxy\Qhelp;
    use qzxy\Log;
    class Thread_function{

        /* 获取用户可管理的文章 */
        /* 处理文章信息 */
        static function get_threads($type) {
            $M = User::ufetch();
            $limit = 5000;

            $arg = '`cuid`,`thread_title`,`thread_ptime`,`thread_editor`,`hk_sort`,`hk_mode`';

            switch ($type){
                case 'trash': $argS = 'hk_mode = 3'; break;
                case 'thread': $argS = '(hk_mode = 1 OR hk_mode = 2)'; break;
                default : $argS = '0';
            }

            $arr = [];
            if($M['pm'] >= User::$pml_setting['chunk_ct_mag']['psolid']){
                $res = Db::query("SELECT ".$arg." FROM qzlit_thread WHERE ".$argS." order by thread_ptime desc limit ".$limit."");
                foreach ($res as $t ){
                    array_push($arr , Thread::format($t,'sample'));
                }
            } else {
                $chunks = Db::query("select `id` from qzlit_chunk");
                foreach ($chunks as $k){
                    if(User::has_pm('chunk_ct_mag',$k['id'])){
                        $res = Db::query("SELECT ".$arg." FROM qzlit_thread WHERE hk_sort = ".$k['id']." AND  ".$argS." order by thread_ptime desc  limit ".($limit/5)."");
                        foreach ($res as $t){
                            array_push($arr , Thread::format($t,'sample'));
                        }
                    }
                }
            }

            $chunk = Db::query("select * from qzlit_chunk");

            for ($i = 0; $i < count($arr); $i++) {
                foreach ($chunk as $value) {
                    if ($value['id'] == $arr[$i]['sort']) {
                        $arr[$i]['sort'] = $value['chunk_name'];
                    }
                }
                $arr[$i]['timestamp'] = date("Y-m-d H:i", $arr[$i]['timestamp']);
                switch ($temp = $arr[$i]['mode']) {
                    case 1:
                    case 2:
                        $arr[$i]['func'] = '
                            <a class="threadfuncicon icon-edit text-primary" href="' . SITE . '/consoleboard/threadmag/renewthread/' . $arr[$i]["id"] . '.html" title="编辑文章" target="_blank"></a>
                            <a class="threadfuncicon icon-trash text-warning" onclick="Threadmag.setstat(\'trashthread\',' . $arr[$i]["id"] . ')" title="回收文章"></a>';

                        $temp = $arr[$i]['mode'] == 1 ? '<a class="threadfuncicon icon-eye-close text-danger" onclick="Threadmag.setstat(\'pushthread\',' . $arr[$i]["id"] . ')" title="推送文章"></a>' : '<a class="threadfuncicon icon-eye-open text-success" onclick="Threadmag.setstat(\'dpushthread\',' . $arr[$i]["id"] . ')" title="取消推送""></a>';
                        $arr[$i]['func'] = "<div>".$temp . $arr[$i]['func']."</div>";
                        break;
                    case 3:
                        $arr[$i]['func'] = '<div>
                            <a class="threadfuncicon icon-times text-danger" onclick="Threadmag.setstat(\'delthread\',' . $arr[$i]["id"] . ')" title="删除文章"></a>
                            <a class="threadfuncicon icon-reply text-success" onclick="Threadmag.setstat(\'recoverthread\',' . $arr[$i]["id"] . ')" title="恢复文章"></a></div>';
                        break;
                }
            }
            return $arr;
        }

        /* 获取用户管理的板块 */
        static function get_chunks($arr) {
            foreach ($arr as $key => $v) {
                $arr[$key]['hpm'] = User::has_pm('chunk_ct_mag',$arr[$key]['id']) ? 1 : 0;
            }
            return $arr;
        }

        static function check_has_thread_pm($threadid) {
            if(!Qhelp::chk_pint($threadid)){
                return Qhelp::json_en(["Stat" => 'error', "Message" => "数据类型错误！"]);
            } else {
                $res = Db::query("select `hk_sort` from qzlit_thread where cuid = $threadid");
                if(!empty($res)){
                    $td = $res[0];
                    if (!User::has_pm('chunk_ct_mag',$td['hk_sort'])) {
                        Log::visit("consoleboard", "thread", "trytoedit_" . $threadid);
                        return Qhelp::json_en(["Stat" => 'error', "Message" => "权限不足"]);
                    }
                } else {
                    Log::visit("consoleboard", "thread", "trytodel_" . $threadid);
                    return Qhelp::json_en(["Stat" => 'error', "Message" => "文章不存在，可能已被删除"]);
                }
            }
        }

        /* 回收文章 */
        static function trashthread($threadid) {
            Db::execute("update qzlit_thread set hk_mode = 3 where cuid = $threadid");
            Log::visit("consoleboard", "thread", "trash_" . $threadid);
            return Qhelp::json_en(["Stat" => 'OK', "Message" => "回收成功"]);
        }

        /* 推送文章 */
        static function pushthread($threadid) {
            Db::execute("update qzlit_thread set hk_mode = 2 where cuid = $threadid");
            Log::visit("consoleboard", "thread", "open_" . $threadid);
            return Qhelp::json_en(["Stat" => 'OK', "Message" => "推送成功"]);
        }

        /* 取消推送文章 */
        static function dpushthread($threadid) {
            Db::execute("update qzlit_thread set hk_mode = 1 where cuid = $threadid");
            Log::visit("consoleboard", "thread", "close_" . $threadid);
            return Qhelp::json_en(["Stat" => 'OK', "Message" => "文章已禁止访问"]);
        }

        /* 恢复文章 */
        static function recoverthread($threadid) {
            Db::execute("update qzlit_thread set hk_mode = 1 where cuid = $threadid");
            Log::visit("consoleboard", "thread", "recovery_" . $threadid);
            return Qhelp::json_en(["Stat" => 'OK', "Message" => "恢复成功"]);
        }

        /* 删除文章 */
        static function delthread($threadid) {
            $img = Db::query("select thread_coverimg from qzlit_thread WHERE cuid = $threadid")[0]['thread_coverimg'];
            @unlink(ROOT_PATH . 'data/catch/temp/img/' . substr($img, 0, 8) . '/' . substr($img, 8));
            Db::execute("delete from qzlit_thread where cuid = $threadid");
            Log::visit("consoleboard", "thread", "del_" . $threadid);
            return Qhelp::json_en(["Stat" => 'OK', "Message" => "成功删除"]);
        }
    }