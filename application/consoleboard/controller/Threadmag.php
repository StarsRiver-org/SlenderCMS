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

use qzxy\Chunk;
use qzxy\Thread;
use qzxy\Re;
use qzxy\User;
use qzxy\Log;
use qzxy\Qhelp;
use think\Db;
use think\Controller;

class Threadmag extends Controller {
    public function _initialize() {
        new Init();
        if(!User::has_pm('is_admin')){
            $this->error('操作错误，您未获得该操作权限');
            return null;
        }
        $this->assign([
            'chunklv1' => Thread_function::get_chunks(Db::query("select `id`, `chunk_name`, `chunk_below` from qzlit_chunk WHERE `type` = 0 AND chunk_lv = 1")),
            'chunklv2' => Thread_function::get_chunks(Db::query("select `id`, `chunk_name`, `chunk_below` from qzlit_chunk WHERE `type` = 0 AND chunk_lv = 2")),
            'chunklv3' => Thread_function::get_chunks(Db::query("select `id`, `chunk_name`, `chunk_below` from qzlit_chunk WHERE `type` = 0 AND chunk_lv = 3")),
            'splv1' => Thread_function::get_chunks(Db::query("select `id`, `chunk_name`, `chunk_below` from qzlit_chunk WHERE `type` = 1 AND chunk_lv = 1")),
            'splv2' => Thread_function::get_chunks(Db::query("select `id`, `chunk_name`, `chunk_below` from qzlit_chunk WHERE `type` = 1 AND chunk_lv = 2")),
            'splv3' => [],
        ]);
    }

    public function main() {

        if (isset($_POST['newthread'])) {
            return Thread::sender();
        } else {
            $this->assign([
                'threadmag' => 'active',
                'editor' => User::ufetch(),
                ]);

            return view('admin/threadmag');
        }
    }

    public function gettreaddata(){
        $type = $_POST['type'];
        switch ($type){
            case 'thread': $data = Thread_function::get_threads("thread"); break;
            case 'trash': $data = Thread_function::get_threads("trash"); break;
        }
        if(!empty($data)){
            return Qhelp::json_en([
                'Stat' => 'OK',
                'Message' => '数据已刷新',
                'Data' => $data
            ]);
        } else {
            return Qhelp::json_en([
                'Stat' => 'error',
                'Message' => '数据为空',
            ]);
        }
    }

    function logic(){
        if(Thread_function::check_has_thread_pm($_POST['tid'])){
            return Qhelp::json_en(["Stat" => 'error', "Message" => "权限不足"]);
        }
        if(!Qhelp::chk_pint($_POST['tid']) || $_POST['tid'] < 1){
            return Qhelp::json_en(["Stat" => 'error', "Message" => "参数错误"]);
        }
        if(!empty($_POST['med'])){
            switch ($_POST['med']){
                case 'trashthread': return Thread_function::trashthread($_POST['tid']) ;break;
                case 'pushthread': return Thread_function::pushthread($_POST['tid']) ;break;
                case 'dpushthread': return Thread_function::dpushthread($_POST['tid']) ;break;
                case 'recoverthread':return Thread_function::recoverthread($_POST['tid']) ;break;
                case 'delthread': return Thread_function::delthread($_POST['tid']) ;break;
                default: return Qhelp::json_en(["Stat" => 'error', "Message" => "参数错误"]);
            }
        }
        return Qhelp::json_en(["Stat" => 'error', "Message" => "参数不足，当前行为无法理解"]);
    }

    /* 更新文章 */
    function renewthread($threadid) {
        if(!Qhelp::chk_pint($threadid) || $threadid < 1){
            $this->error('参数错误');
        }
        Thread_function::check_has_thread_pm($threadid);
        /* 保存文章程序 */
        if (isset($_POST['renewthread'])) {
            return Thread::sender();
        } else {
            $res = Thread::loadone($threadid);
            /* 获取文章信息 */
            $this->assign(['other' => 'active', 'id' => $res['id'], 'title' => $res['title'], 'coverimg' => $res['coverimg'], 'content' => $res['content'], 'author' => $res['author'], 'time' => $res['time'], 'keyword' => $res['keyword'], 'descrip' => $res['descrip'], 'sort' => $res['sort'], 'mode' => $res['mode'], 'editor' => User::ufetch(),]);
            return view('admin/threadmag_updatathread');
        }
    }

    function setbanner($chunk_id) {
        if(!Qhelp::chk_pint($chunk_id) || $chunk_id < 1){
            $this->error('操作错误，参数类型错误');
            return null;
        }
        $M = User::ufetch();

        if (!User::has_pm('chunk_ct_mag',$chunk_id)) {
            $this->error('操作错误，您未获得该操作权限');
            return null;
        }
        $banner_perfix = 'b';
        $maxnum = 10;
        $cb = Chunk::loadbanner($chunk_id);
        $this->assign([
            'other' => 'active',
            'editor' => $M,
            'chunk' => $chunk_id,
            'slider' => $cb['slider'],
            'banner' => $cb['banner'],
        ]);
        if (isset($_POST['updata'])) {
            $fail = 0;
            $cut = 0;
            $imgs = [];
            $postimgs = request()->file($banner_perfix);
            $num = count($postimgs);

            for ($k = 0; $k < $maxnum; $k++) { /*对 banner 图顺序赋值*/
                $imgs[$k] = '';
                if (isset($_POST['sec' . $k]) && $_POST['sec' . $k] == '1') {
                    $imgs[$k] = $postimgs[$cut];
                    ++$cut;
                };
            }

            if (!$imgdata = Db::query("select banner from qzlit_chunk WHERE id = '" . $chunk_id . "'")[0]['banner']) {
                $banners = [];
            } else {
                $banners = json_decode($imgdata, true);
            }

            for ($i = 0; $i < $maxnum; $i++) {
                if ($imgs[$i] != '') {
                    $info = $imgs[$i]->validate(['size' => 20480000, 'ext' => 'jpg,png,gif'])->move(ROOT_PATH . 'data/catch/temp/img');
                    if ($info) {
                        $tmp = $info->getSaveName();
                        $banners[$i] = substr($tmp, 0, 8) . substr($tmp, 9);
                    } else {
                        ++$fail;
                    }
                }
            }
            $bdata = Qhelp::json_en($banners);
            Db::execute("update qzlit_chunk set banner = '$bdata' where id = $chunk_id");
            Log::visit("consoleboard", "banner", "renew_chunk_" . $chunk_id);
            Re::echo('success', "文件上传结束，共修改 $num 张图片，有 $fail 张上传失败，(请上传小于20M的图片)", 0);

        }
        return view('admin/threadmag_setbanner');
    }
}