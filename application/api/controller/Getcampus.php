<?php
    namespace qzxy\api\controller;
    use qzxy\Ip;
    use qzxy\Qhelp;
    use think\Controller;
    use think\Db;


    class Getcampus extends Controller {

        public function main() {
            return htmlspecialchars_decode(Db::query("select * from qzlit_config where name = 'campus'")[0]['data'],ENT_QUOTES);
        }
    }