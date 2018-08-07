<?php

namespace qzxy;

use think\Controller;
use think\Db;

class Base extends Controller{
    public static function baseinfo(){
        $allvisite = count(Db::query("select vid from qzlit_log"));
        $navarg = '`id`, `name`, `bel`, `url`, `key`,`blank`';
        $mainnav =  Db::query("select ".$navarg." from qzlit_nav WHERE active != 0 AND type = '1' ORDER BY `order` ASC ");
        $searchnav =  Db::query("select ".$navarg."  from qzlit_nav WHERE active != 0 AND type = '2' ORDER BY `order` ASC ");
        $foonav =  Db::query("select ".$navarg."  from qzlit_nav WHERE active != 0 AND type = '3' ORDER BY `order` ASC ");


        return [
            'allvisite' => $allvisite,
            'mainnav' => $mainnav,
            'searchnav' => $searchnav,

            'foonav' => $foonav,
        ];
    }
}