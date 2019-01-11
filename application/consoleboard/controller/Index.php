<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-06-13
 *
 */
    namespace qzxy\consoleboard\controller;
    use qzxy\Log;
    use qzxy\User;
    use think\Controller;
    use think\Db;

    class Index extends Controller{

        public function _initialize(){
            new Init();
            if(!User::has_pm('is_admin')){
                $this->error('操作错误，您未获得该操作权限');
                return null;
            }
        }
        public function main() {
            /* 文章统计 */
            Log::visit("consoleboard", "home", "watch");
            $article_all  = Db::query("select `thread_ptime` from qzlit_thread WHERE hk_mode = '2' ORDER By thread_ptime DESC ");
            $article_like = Db::query("select `ore_degree` from qzlit_thread WHERE ore_degree != ''");
            $article_view = Db::query("select `ore_view` from qzlit_thread WHERE ore_view != ''");
            $article_today= Log::filt_by_time($article_all,"day");
            $count_view = 0; $count_like = 0;
            foreach ($article_view as $key){$count_view +=  $key['ore_view'];}
            foreach ($article_like as $key){$count_like +=  $key['ore_degree'];}
            $info_article = [
                'today' => count($article_today),
                'all'   => count($article_all),
                'visit' => $count_view,
                'like'  => $count_like,
            ];

            /*浏览统计*/
            $visit_logs = Db::query("select `vid`,`time`,`target`,`data`,`func` from qzlit_log ORDER BY `time` DESC ");
            $visit_log_portal = Log::filt_by_target($visit_logs,"portal");
            $visit_log_article = Log::filt_by_target($visit_logs,"article");
            $visit_log_consoleboard = Log::filt_by_target($visit_logs,"consoleboard");

            $visit_log_portal = [
                'all'  => $visit_log_portal,
            ];

            $visit_log_consoleboard = [
                'all'   => $visit_log_consoleboard,
            ];

            $visit_log = [
                'portal'   => [
                    'allsite'  => [
                        'all'  => count($visit_log_portal['all']),
                        'hour' => Log::count_by_timegap($visit_log_portal['all'],"hour"),
                        'day'  => Log::count_by_timegap($visit_log_portal['all'],"day"),
                        'week' => Log::count_by_timegap($visit_log_portal['all'],"week"),
                        'moon' => Log::count_by_timegap($visit_log_portal['all'],"moon"),
                    ],
                ],
                'article' => [
                    'allsite'  =>[
                        'all'  => count($visit_log_article),
                        'hour' => Log::count_by_timegap($visit_log_article,"hour"),
                        'day'  => Log::count_by_timegap($visit_log_article,"day"),
                        'week' => Log::count_by_timegap($visit_log_article,"week"),
                        'moon' => Log::count_by_timegap($visit_log_article,"moon"),
                    ],
                ],
                'consoleboard' => [
                    'allsite'  =>[
                        'all'  => count($visit_log_consoleboard['all']),
                        'hour' => Log::count_by_timegap($visit_log_consoleboard['all'],"hour"),
                        'day'  => Log::count_by_timegap($visit_log_consoleboard['all'],"day"),
                        'week' => Log::count_by_timegap($visit_log_consoleboard['all'],"week"),
                        'moon' => Log::count_by_timegap($visit_log_consoleboard['all'],"moon"),
                    ],
                ],
            ];
            $this->assign([
                'info_article'   => $info_article,
                'info_visit' => $visit_log,
                'index' => 'active',
            ]);
            return view('admin/index');
        }
    }