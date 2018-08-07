<?php
namespace qzxy\index\controller;
use think\Controller;
use think\Db;
class Init extends Controller{
    public function main() {
        $lock = ROOT_PATH.'install/install.lock';
        if(!file_exists($lock)){
            $this->error('程序未安装，请联系管理员');
            exit;
        }

        $HOME_PAGE = "portal";

        header('Location: '.SITE.'/'.$HOME_PAGE.'.html');
        exit;
    }
}
