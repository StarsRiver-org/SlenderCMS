<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2019-3-2
 *
 */
    namespace app\common\controller;
    use app\Log;
    use app\Config;
    use app\User;
    use app\Limit;
    use think\Controller;
	
    class Template extends Controller {
		
		public static function view($tpl, $arg='') {
			return self::make($tpl, $arg);
        }
		
		public static function initTpl(){
			$allow_mobile = Config::getconf('info','mobiletpl');
			/* 条件检测 */
			if(@$_GET['device'] == 'm' && $allow_mobile == 'on' && @file_exists(ROOT_PATH.'template'.DS.'mobile'.DS.$tpl.'.html')){
				return 'mobile';
			} else {
				return 'default';
			}
		}
		
		public static function make($tpl, $arg=''){
			return view(self::initTpl().DS.$tpl, $arg);
		}
		
    }