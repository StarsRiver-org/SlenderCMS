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
    namespace qzxy\common\controller;
    use qzxy\Log;
    use qzxy\Config;
    use qzxy\User;
    use think\Controller;
	
    class Admincheck extends Controller {
		
        public static function view($tpl) { /*管理员登陆检查*/
			
			$amending = self::amendcheck();

			if($amending && User::has_pm('is_admin')){
				/* 正常访问 */
				return self::returnTpl($tpl);
				
			} else {
				/* 提示维护中 */
				Log::visit("baned", "none", "logincheck");
				return self::returnTpl('error/maintenance');
				
			}
        }
		
		public static function amendcheck() { /*维护检测*/
            $is_open = Config::getconf('info','open');
            if($is_open == 'on'){
                return 1;
            }
        }
		
		public static function returnTpl($tpl){
			
			$allow_mobile = Config::getconf('info','mobiletpl');
		
			/* 条件检测 */
			if($_GET['mobile'] == 'm' && $allow_mobile && @file_exist(ROOT_PATH.'/template/'.$TPLT_M.DS.$tpl.'html')){
				$TPLT = 'mobile';
			} else {
				$TPLT = 'default';
			}
			
			define('TPL_PATH', ROOT_PATH.'/template/'.$TPLT . DS);
			
			return view($tpl);
		}
		
    }
