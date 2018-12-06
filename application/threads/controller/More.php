<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-08-05
 *
 */
    namespace qzxy\threads\controller;
    use qzxy\Chunk;
    use qzxy\Base;
    use qzxy\Qhelp;
    use qzxy\Qpage;
    use qzxy\Re;
    use qzxy\Thread;
    use think\Controller;
    class More extends Controller{
        function main(){
            $this->assign([
                'new' => Thread::newest(),
                'base' => Base::baseinfo(),
            ]);

            if(empty($_GET['cid']) || !Qhelp::chk_pint($_GET['cid'])){
                $this->error('参数错误');
            }
            if(!empty($_GET['from']) && (!Qhelp::chk_pint($_GET['from']))){
                $this->error('参数错误');
            }

            if(!empty($_GET['page']) && (!Qhelp::chk_pint($_GET['page']) || $_GET['page'] < 1)){
                $this->error('参数错误');
            }
            if(!Chunk::getchunk($_GET['cid'])){
                $this->error('您要查看的内容不存在');
            }
            if(isset($_GET['from'])){
                if($_GET['from'] >= 0 && Qhelp::chk_pint($_GET['from'])){
                    $this->assign(['banners'=>Chunk::loadbanner(Qhelp::receive('from'))]);
                }
            }
            $chunk = $_GET['cid'];
            $perpage = 10;
            $all = Chunk::loadthread($chunk, true);
            $from = empty($_GET['from']) ? '' : $_GET['from'];
            $page = empty($_GET['page']) ? 1 : $_GET['page'];
            $pages = ceil(count($all)/$perpage);
            if($pages == 0){
                $this->assign([
                    'thread_list' => '',
                    'chunk' => '',
                    'multipage' => ''
                ]);
            } elseif ($pages >= $page && $page > 0){
                $current = [];
                for ($i = ($page-1)*$perpage; $i < (count($all) <= $page*$perpage ?  count($all) : $page*$perpage); $i++){
                    array_push($current, $all[$i]);
                }
                $this->assign([
                    'thread_list' => $current,
                    'chunk' => Chunk::getchunk($chunk),
                    'multipage' => Qpage::page($pages,$page,'/threads/more?from='.$from.'&cid='.$_GET['cid'].'&page='),
                ]);
            } else {
                Re::echo('danger','页面错误',true);
                return '<script>window.location.href=\''.SITE.'/threads/more?from='.$from.'&cid='.$_GET['cid'].'&page='.$pages.'\'</script>';
            }
            return view('portal/thread_more');
        }
    }
