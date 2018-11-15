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

    use qzxy\Chunk;
    use think\Controller;
    use think\Db;
    use qzxy\Re;
    use qzxy\User;

    class Chunkmag extends Controller{
        public function _initialize() {
            new Init();
            if(!User::has_pm('chunk_mag')){
                $this->error('操作错误，您未获得该操作权限');
                return null;
            }
        }

        public function main(){
            if(!empty($_POST)){
                if(!empty($_POST['chunk_rename']) || !empty($_POST['new_name'])){
                    Chunkmag_function::renchunk();
                } elseif(!empty($_POST['add_chunk'])) {
                    Chunkmag_function::addchunk();
                } elseif (!empty($_POST['del_chunk'])){
                    Chunkmag_function::delchunk();
                } elseif (!empty($_POST['chunk_becomb']) && !empty($_POST['chunk_comber'])){
                    Chunkmag_function::combchunk();
                } else {
                    Re::echo('danger','参数错误',0);
                }
            }
            /* 循环赋值 */
            $this->assign([
                'chunklv1' => Db::query("select `id`, `chunk_name`, `chunk_below` ,`chunk_lv` from qzlit_chunk WHERE `type` = 0 AND chunk_lv = 1"),
                'chunklv2' => Db::query("select `id`, `chunk_name`, `chunk_below` ,`chunk_lv` from qzlit_chunk WHERE `type` = 0 AND chunk_lv = 2"),
                'chunklv3' => Db::query("select `id`, `chunk_name`, `chunk_below` ,`chunk_lv` from qzlit_chunk WHERE `type` = 0 AND chunk_lv = 3"),

                'splv1' => Db::query("select `id`, `chunk_name`, `chunk_below`,`chunk_lv`  from qzlit_chunk WHERE `type` = 1 AND chunk_lv = 1"),
                'splv2' => Db::query("select `id`, `chunk_name`, `chunk_below`,`chunk_lv`  from qzlit_chunk WHERE `type` = 1 AND chunk_lv = 2"),
            ]);
            $this->assign(['chunkmag'   => 'active',]);
            return view('admin/chunkmag');
        }
    }
    