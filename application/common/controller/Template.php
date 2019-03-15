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
    use think\Controller;
	
    class Template extends Controller {
		
        public static function view($tpl) { /*管理员登陆检查*/

			if( !self::amending()  //网站关闭检测
				|| User::has_pm('is_admin')  //管理员登陆检测
				|| in_array(explode('/',$_SERVER['REQUEST_URI'])[1],['consoleboard','enroll'])//访问后台检测
			){
				/* 正常访问 */
				return self::returnTpl($tpl);
				
			} else {
				/* 提示维护中 */
				Log::visit("baned", "none", "logincheck");
				return self::returnTpl('error/maintenance');
			}
        }
		
		public static function amending() { /*维护检测*/
            $is_open = Config::getconf('info','open');
            if($is_open == 'on'){
                return 0;
            } else {
				return 1;
			}
        }
		
		public static function returnTpl($tpl){
			
			$allow_mobile = Config::getconf('info','mobiletpl');
		
			/* 条件检测 */
			if(@$_GET['device'] == 'm' && $allow_mobile == 'on' && @file_exists(ROOT_PATH.'template'.DS.'mobile'.DS.$tpl.'.html')){
				$TPLT = 'mobile';
			} else {
				$TPLT = 'default';
			}
	
			return view($TPLT.DS.$tpl);
		}
		
    }