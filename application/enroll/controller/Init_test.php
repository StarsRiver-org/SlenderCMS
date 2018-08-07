<?php
    namespace qzxy\enroll\controller;
    use qzxy\Re;
    use qzxy\User;
    use qzxy\Log;
    use qzxy\Qhelp;
    use think\Controller;

    class Init_test extends Controller {
        public function _initialize() {
            $this->nav();
        }

        public function nav() { /*初始化导航*/
            $G_ = User::ufetch();
            $this->assign ([
                'index' => $G_['pm'] >= 700 ? 'denny' : 'hidden',
                'chunkmag' => User::has_pm('chunk_mag') ? 'denny' : 'hidden',
                'threadmag' => $G_['pm'] >= 700 ? 'denny' : 'hidden',
                'navmag' => User::has_pm('nav_mag') ? 'denny' : 'hidden',
                'usermag' => User::has_pm('user_mag') ? 'denny' : 'hidden',
                'configmag' => User::has_pm('config_mag') ? 'denny' : 'hidden',
                'enrollmag' => User::has_pm('enroll_use') ? 'denny' : 'hidden',
                'other' => 'hidden',
            ]);
        }

    }