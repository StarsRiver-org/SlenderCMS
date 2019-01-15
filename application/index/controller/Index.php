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
namespace qzxy\index\controller;
use think\Controller;
class Index extends Controller{
    public function main() {
        $lock = ROOT_PATH.'install/install.lock';
        if(!file_exists($lock)){
            $this->error('程序未安装，请联系管理员');
            exit;
        }

        $HOME_PAGE = "portal";

        header('Location:'.SITE.'/'.$HOME_PAGE.'.html');
        exit;
    }
}
