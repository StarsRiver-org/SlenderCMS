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
namespace qzxy\consoleboard\controller;

use qzxy\Log;
use qzxy\Re;
use qzxy\Qhelp;
use think\Controller;
use think\Db;

class Chunkmag_function extends Controller {
    public static function addchunk() {
        $type = isset($_POST['type']) ? '1' : '0';
        if (!empty($_POST['chunk_name'])) {
            if ($_POST['add_chunk'] == 'lv1') {
                $check = Db::query("select * from qzlit_chunk WHERE `type` = '" . $type . "' AND chunk_name = '" . Qhelp::receive('chunk_name') . "' AND chunk_lv = '1'");
                if (!empty($check)) {
                    Re::echo ('warning', '名称不能重复', 0);
                } else {
                    Db::execute("insert into qzlit_chunk(`type`, chunk_name, chunk_lv) VALUE ('" . $type . "', '" . Qhelp::receive('chunk_name') . "','1')");
                    Log::visit("consoleboard", "managechunk", "add_" . Qhelp::receive('chunk_name'));
                    Re::echo ('success', '添加成功', 0);
                }
            } elseif ($_POST['add_chunk'] == 'lv2' && Qhelp::receive('chunk_below')) {
                $check = Db::query("select * from qzlit_chunk WHERE `type` = '" . $type . "' AND chunk_below='" . Qhelp::receive('chunk_below') . "' AND chunk_name = '" . Qhelp::receive('chunk_name') . "'");
                if (!empty($check)) {
                    Re::echo ('warning', '名称不能重复', 0);
                } else {
                    Db::execute("insert into qzlit_chunk(`type`, chunk_name, chunk_lv, chunk_below) VALUE ('" . $type . "', '" . Qhelp::receive('chunk_name') . "','2' , '" . Qhelp::receive('chunk_below') . "')");
                    Log::visit("consoleboard", "managechunk", "add_" . Qhelp::receive('chunk_name'));
                    Re::echo ('success', '添加成功', 0);
                }
            } elseif ($_POST['add_chunk'] == 'lv3' && Qhelp::receive('chunk_below') && !$type) {
                $check = Db::query("select * from qzlit_chunk WHERE `type` = '" . $type . "' AND chunk_below='" . Qhelp::receive('chunk_below') . "' AND chunk_name = '" . Qhelp::receive('chunk_name') . "'");
                if (!empty($check)) {
                    Re::echo ('warning', '名称不能重复', 0);
                } else {
                    Db::execute("insert into qzlit_chunk(`type`, chunk_name, chunk_lv, chunk_below) VALUE ('" . $type . "', '" . Qhelp::receive('chunk_name') . "','3', '" . Qhelp::receive('chunk_below') . "')");
                    Log::visit("consoleboard", "managechunk", "add_" . Qhelp::receive('chunk_name'));
                    Re::echo ('success', '添加成功', 0);
                }
            } else {
                Re::echo ('warning', "数据错误", 0);
            }
        } else {
            Re::echo ('warning', "数据错误", 0);
        }
    }

    public static function delchunk() {
        if(empty($_POST['del_chunk']) || Qhelp::chk_pint($_POST['del_chunk']) || $_POST['del_chunk'] < 1 ){
            Re::echo ('danger', '参数错误', 0);
        }

        $pr = Db::query("select * from qzlit_chunk WHERE id = '" . $_POST['del_chunk'] . "'");
        if(!empty($pr)){
            if ($pr[0]['chunk_lv'] == 1) {
                $chunk_lv2 = Db::query("select * from qzlit_chunk WHERE chunk_below = '" . $_POST['del_chunk'] . "'");
                foreach ($chunk_lv2 as $value) {
                    $chunk_lv3 = Db::query("select * from qzlit_chunk WHERE chunk_below = '" . $value['id'] . "'");
                    foreach ($chunk_lv3 as $v) {
                        Chunkmag_function::del_thread($v['id']);
                        Db::execute("delete from qzlit_chunk WHERE id = '" . $v['id'] . "'");
                    }
                    Chunkmag_function::del_thread($value['id']);
                    Db::execute("delete from qzlit_chunk WHERE id = '" . $value['id'] . "'");
                }
                Chunkmag_function::del_thread($_POST['del_chunk']);
                Db::execute("delete from qzlit_chunk WHERE id = '" . $_POST['del_chunk'] . "'");
                Log::visit("consoleboard", "managechunk", "del_" . $_POST['del_chunk']);
                Re::echo ('success', '删除成功', 0);
            } elseif ($pr[0]['chunk_lv'] == 2) {
                $chunk_lv3 = Db::query("select * from qzlit_chunk WHERE chunk_below = '" . $_POST['del_chunk'] . "'");
                foreach ($chunk_lv3 as $value) {
                    Chunkmag_function::del_thread($value['id']);
                    Db::execute("delete from qzlit_chunk WHERE id = '" . $value['id'] . "'");
                }
                Chunkmag_function::del_thread($_POST['del_chunk']);
                Db::execute("delete from qzlit_chunk WHERE id = '" . $_POST['del_chunk'] . "'");
                Log::visit("consoleboard", "managechunk", "del_" . $_POST['del_chunk']);
                Re::echo ('success', '删除成功', 0);
            } elseif ($pr[0]['chunk_lv'] == 3) {
                Chunkmag_function::del_thread($_POST['del_chunk']);
                Db::execute("delete from qzlit_chunk WHERE id = '" . $_POST['del_chunk'] . "'");
                Log::visit("consoleboard", "managechunk", "del_" . $_POST['del_chunk']);
                Re::echo ('success', '删除成功', 0);
            }
        } else {
            Re::echo ('danger', '板块不存在', 0);
        }
    }

    public static function combchunk() {
        if(
            (empty($_POST['chunk_comber']) || Qhelp::chk_pint($_POST['chunk_comber']) || $_POST['chunk_comber'] < 1) ||
            (empty($_POST['chunk_becomb']) || Qhelp::chk_pint($_POST['chunk_becomb']) || $_POST['chunk_becomb'] < 1)
        ){
            Re::echo ('danger', '参数错误', 0);
        }

        $cb = Db::query("select chunk_lv from qzlit_chunk WHERE id = '" . $_POST['chunk_comber'] . "'");
        $bc = Db::query("select chunk_lv from qzlit_chunk WHERE id = '" . $_POST['chunk_becomb'] . "'");
        if(!empty($cb) && !empty($bc)){
            if (Qhelp::receive('chunk_becomb') != Qhelp::receive('chunk_comber')) {
                $chunk_lv = $bc[0]['chunk_lv'];
                if ($chunk_lv == 1) {
                    $chunk_lv2 = Db::query("select * from qzlit_chunk WHERE chunk_below = '" . Qhelp::receive('chunk_becomb') . "'");
                    foreach ($chunk_lv2 as $value) {
                        $chunk_lv3 = Db::query("select * from qzlit_chunk WHERE chunk_below = '" . $value['id'] . "'");
                        foreach ($chunk_lv3 as $v) {
                            Chunkmag_function::comb_thread($v['id'], Qhelp::receive('chunk_comber'));
                            Db::execute("delete from qzlit_chunk WHERE id = '" . $v['id'] . "'");
                        }
                        Chunkmag_function::comb_thread($value['id'], Qhelp::receive('chunk_comber'));
                        Db::execute("delete from qzlit_chunk WHERE id = '" . $value['id'] . "'");
                    }
                    Chunkmag_function::comb_thread(Qhelp::receive('chunk_becomb'), Qhelp::receive('chunk_comber'));
                    Db::execute("delete from qzlit_chunk WHERE id = '" . Qhelp::receive('chunk_becomb') . "'");
                    Log::visit("consoleboard", "managechunk", "comb_" . Qhelp::receive('chunk_becomb') . "_TO_" . Qhelp::receive('chunk_comber'));
                    Re::echo ('success', '合并成功', 0);
                } elseif ($chunk_lv == 2) {
                    $chunk_lv3 = Db::query("select * from qzlit_chunk WHERE chunk_below = '" . Qhelp::receive('chunk_becomb') . "'");
                    foreach ($chunk_lv3 as $value) {
                        Chunkmag_function::comb_thread($value['id'], Qhelp::receive('chunk_comber'));
                        Db::execute("delete from qzlit_chunk WHERE id = '" . $value['id'] . "'");
                    }
                    Chunkmag_function::comb_thread(Qhelp::receive('chunk_becomb'), Qhelp::receive('chunk_comber'));
                    Db::execute("delete from qzlit_chunk WHERE id = '" . Qhelp::receive('chunk_becomb') . "'");
                    Log::visit("consoleboard", "managechunk", "comb_" . Qhelp::receive('chunk_becomb') . "_TO_" . Qhelp::receive('chunk_comber'));
                    Re::echo ('success', '合并成功', 0);
                } elseif ($chunk_lv == 3) {
                    Chunkmag_function::comb_thread(Qhelp::receive('chunk_becomb'), Qhelp::receive('chunk_comber'));
                    Db::execute("delete from qzlit_chunk WHERE id = '" . Qhelp::receive('chunk_becomb') . "'");
                    Log::visit("consoleboard", "managechunk", "comb_" . Qhelp::receive('chunk_becomb') . "_TO_" . Qhelp::receive('chunk_comber'));
                    Re::echo ('success', '合并成功', 0);
                }
            } else {
                Re::echo ('danger', '请选择两个不同的版块', 0);
            }
        } else {
            Re::echo ('danger', '参数错误', 0);
        }
    }

    public static function renchunk() {
        $type = isset($_POST['type']) ? '1' : '0';
        if (!empty($_POST['chunk_rename']) || !empty($_POST['new_name'])) {
            $pr = Db::query("select * from qzlit_chunk WHERE `type` = '" . $type . "' AND id = '" . Qhelp::receive('chunk_rename') . "'");
            if (!empty($pr)) {
                if (Qhelp::receive('new_name') == $pr[0]['chunk_name']) {
                    Re::echo ('warning', '名称未修改', 0);
                } else {
                    if ($pr[0]['chunk_lv'] == 1) {
                        $regd = Db::query("select * from qzlit_chunk WHERE `type` = '" . $type . "' AND chunk_lv =  1 AND chunk_name = '" . Qhelp::receive('new_name') . "'");
                    } else {
                        $regd = Db::query("select * from qzlit_chunk WHERE `type` = '" . $type . "' AND chunk_below = '" . $pr[0]['chunk_below'] . "' AND chunk_name = '" . Qhelp::receive('new_name') . "'");
                    }
                    if (empty($regd)) {
                        Db::execute("update qzlit_chunk set `chunk_name` = '" . Qhelp::receive('new_name') . "' where id = '" . Qhelp::receive('chunk_rename') . "'");
                        Log::visit("consoleboard", "managechunk", "rename_" . Qhelp::receive('new_name'));
                        Re::echo ('success', '修改成功', 0);
                    } else {
                        Re::echo ('warning', '同级专题名不能重复', 0);
                    }
                }
            }
        } else {
            Re::echo ('danger', '参数不足', 0);
        }
    }

    public static function del_thread($sort) {
        if(!Qhelp::chk_pint($sort) || $sort < 1){
            Re::echo ('danger', '参数错误', 0);
        }
        $img = Db::query("select thread_coverimg from qzlit_thread WHERE hk_sort = $sort");
        foreach ($img as $value) {
            if (!empty($value['thread_coverimg'])) {
                unlink(ROOT_PATH . 'data/catch/temp/img/' . substr($value['thread_coverimg'], 0, 8) . '/' . substr($value['thread_coverimg'], 8));
            }
        }
        Db::execute("delete from qzlit_thread where hk_sort = $sort");
    }

    public static function comb_thread($becomb, $comber) {
        if(
            (empty($becomb) || Qhelp::chk_pint($becomb) || $becomb < 1) ||
            (empty($comber) || Qhelp::chk_pint($comber) || $comber < 1)
        ){
            Re::echo ('danger', '参数错误', 0);
        }

        Db::execute("update qzlit_thread set hk_sort = '" . $comber . "' where hk_sort = $becomb");
    }
}