<?php
    namespace qzxy\consoleboard\controller;
    use think\Controller;
    use think\Db;
    use qzxy\User;

    class Navmag extends Controller{
        public function _initialize() {
            new Init();
            if(!User::has_pm('nav_mag')){
                $this->error('操作错误，您未获得该操作权限');
                return null;
            }
        }

        public function main(){

            if(!empty($_POST['navdata'])){
                return Nav_function::update($_POST['navdata']);
            } else {
                $mainnav =  Db::query("select * from qzlit_nav WHERE type = '1' ORDER BY `order` ASC ");
                $searchnav =  Db::query("select * from qzlit_nav WHERE type = '2' ORDER BY `order` ASC ");
                $foonav =  Db::query("select * from qzlit_nav WHERE type = '3' ORDER BY `order` ASC ");
                $this->assign([
                    'navmag' => 'active',
                    'mainnav' => $mainnav,
                    'searchnav' => $searchnav,
                    'foonav' => $foonav,
                ]);
                return view('admin/navmag');
            }
        }
    }
    