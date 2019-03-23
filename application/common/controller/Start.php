<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2019-3-18
 *
 */
    namespace app;
	
	use think\Controller;
	use app\Limit;
	use app\Config;
    use app\User;
	use app\common\controller\Template;
	
    class Start extends Controller{
		
		function _initialize() {
			$this->checkopen();
            $this->checklimit();
        }
		
		public function checkopen() { /*维护检测*/
		
            $amending = Config::getconf('info','open') == 'off' ? 0 : 1 ;
			
			if( $amending  //网站关闭检测
				&& !User::has_pm('is_admin')  //管理员登陆检测
				&& !in_array(explode('/',$_SERVER['REQUEST_URI'])[1],['consoleboard','enroll'])//访问后台检测
			){
				/* 提示维护中 */
				return Template::view('error/maintenance');
			}
        }
		
        public function checklimit() { /*三秒内访问次数检查，最短请求间隔三秒*/
		
			if(Limit::ban() && !User::has_pm('is_admin')){ /*限制访问*/
				return Template::view('error/toofast');
			}
			
        }
    }
