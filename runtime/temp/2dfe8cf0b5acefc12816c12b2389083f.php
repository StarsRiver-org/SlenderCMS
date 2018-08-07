<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:88:"G:\WebDesigne\APMServer\web\[QZ] qzxy.tyut.edu.cn\/template/default\admin\threadmag.html";i:1532786210;s:84:"G:\WebDesigne\APMServer\web\[QZ] qzxy.tyut.edu.cn\/template/default\admin\frame.html";i:1532071042;s:89:"G:\WebDesigne\APMServer\web\[QZ] qzxy.tyut.edu.cn\/template/default\admin\mod_header.html";i:1532858712;s:93:"G:\WebDesigne\APMServer\web\[QZ] qzxy.tyut.edu.cn\/template/default\common\thread_sender.html";i:1532950408;s:89:"G:\WebDesigne\APMServer\web\[QZ] qzxy.tyut.edu.cn\/template/default\admin\mod_footer.html";i:1532071110;}*/ ?>
<!DOCTYPE HTML><html><head><title>清泽心雨管理中心</title><meta charset="UTF-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><!--[if lt IE 9]><div class="alert2 alert-danger">您正在使用 <strong>过时的</strong> 浏览器. 请升级您的浏览器来获取更好的体验
        </br><a class="icon-ie updateexplor"
           href="http://download.microsoft.com/download/F/2/8/F2871AC4-E82B-4636-BB37-A5F2B14C8616/IE11-Windows6.1-x86-zh-cn.exe">            x86版本</a><a class="icon-ie updateexplor"
           href="http://download.microsoft.com/download/5/6/F/56FD6253-CB53-4E38-94C6-74367DA2AB34/IE11-Windows6.1-x64-zh-cn.exe">            x64版本</a></div><script src="<?php echo STATIC_ROOT; ?>/lib/ieonly/html5shiv.js"></script><script src="<?php echo STATIC_ROOT; ?>/lib/ieonly/respond.js"></script><![endif]--><link type="text/css" href="<?php echo STATIC_ROOT; ?>/css/base.css?b4f" rel="stylesheet"><link type="text/css" href="<?php echo STATIC_ROOT; ?>/css/fonts_zui.css" rel="stylesheet"><link type="text/css" href="<?php echo STATIC_ROOT; ?>/css/skin/admin.css?b4f" rel="stylesheet"><link type="text/css" href="<?php echo STATIC_ROOT; ?>/lib/datagrid/zui.datagrid.css" rel="stylesheet"><script src="<?php echo STATIC_ROOT; ?>/js/jquery.js"></script><script src="<?php echo STATIC_ROOT; ?>/js/classie.js"></script><script src="<?php echo STATIC_ROOT; ?>/js/clipboard.min.js"></script><script src="<?php echo STATIC_ROOT; ?>/js/zui.min.js"></script><script src="<?php echo STATIC_ROOT; ?>/lib/chart/zui.chart.min.js"></script><script src="<?php echo STATIC_ROOT; ?>/lib/datagrid/zui.datagrid.js"></script><script src="<?php echo STATIC_ROOT; ?>/js/common.js"></script><script>        SiteUrl = "<?php echo SITE; ?>";
        StaticUrl = "<?php echo SITE; ?>/static";
    </script></head><style>body{  box-shadow: inset 180px 0 0 #f9f9f9 , inset 181px 0 0 #ccc;}</style><body><header><div class="logo"><img src="<?php echo STATIC_ROOT; ?>/img/admin/logo.png"></div><ul class="nav nav-tabs headnav"><li class="<?php echo $index; ?>"><a class="icon-home" href="<?php echo SITE; ?>/consoleboard/index.html" data-target="#homepage">首页</a></li><li class="<?php echo $threadmag; ?>"><a href="<?php echo SITE; ?>/consoleboard/threadmag.html" data-target="#threadmanager">内容管理</a></li><li class="<?php echo $navmag; ?>"><a href="<?php echo SITE; ?>/consoleboard/navmag.html" data-target="#threadmanager">导航管理</a></li><li class="<?php echo $chunkmag; ?>"><a href="<?php echo SITE; ?>/consoleboard/chunkmag.html" data-target="#threadmanager">板块管理</a></li><li class="<?php echo $usermag; ?>"><a href="<?php echo SITE; ?>/consoleboard/usermag.html" data-target="#usermanager">用户中心</a></li><li class="<?php echo $configmag; ?>"><a class="sysconfmenu" href="<?php echo SITE; ?>/consoleboard/configmag.html" data-target="#usermanager">系统配置</a></li><li class="<?php echo $enrollmag; ?>"><a href="<?php echo SITE; ?>/enroll/enrollmag.html" data-target="#usermanager">报名管理</a></li><li class="hidden <?php echo $other; ?>"><a>其他</a></li></ul><form method="post" class="admin_exit"><input type="hidden" name="exit" value="confirm"><button type="submit" class=" icon-signout " title="安全登出"></button></form></header><nav class="menu col-xs-0 sidenav" data-ride="menu"><ul class="nav nav-primary"><li class="nav-parent show"><a><i class="icon icon-file"></i>文章管理</a><ul class="nav"><li class="active"><a data-target="#thread-all" data-toggle="tab" href="javascript:;" >全部文章</a></li><li><a data-target="#thread-add" data-toggle="tab" href="javascript:;">添加文章</a></li><li><a data-target="#thread-trash" data-toggle="tab" href="javascript:;">回收站</a></li></ul></li><li class="nav-parent show"><a><i class="icon icon-columns "></i>幻灯管理</a><ul class="nav"><li><a data-target="#thread-banner" data-toggle="tab" href="javascript:;" >管理幻灯图片</a></li></ul></li></ul></nav><section class="tab-content col-xs--1"><script src="<?php echo STATIC_ROOT; ?>/js/admin/threadmag.js"></script><div class="tab-pane fade active in" id="thread-all"><div id="thread_table" class="datagrid"><div class="datagrid-container"></div><div class="pager"></div><div class="input-control search-box search-box-circle has-icon-left has-icon-right"><input id="thread_table_search" type="search" class="form-control search-input" placeholder="搜索"><label for="thread_table_search" class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label><a href="#" class="input-control-icon-right search-clear-btn"><i class="icon icon-remove"></i></a></div></div><script>            var thread_table = $('#thread_table').datagrid({
                dataSource: {
                    cols:[
                        {name: 'id', label: '编号', width: 80},
                        {name: 'title', label: '标题'},
                        {name: 'editor', label: '编辑人', width:100},
                        {name: 'time', label: '发布时间', width: 150},
                        {name: 'sort', label: '分类', width: 100},
                        {name: 'func', label: '操作', width: 90,html:true,}
                    ],

                    array: <?php echo $threaddata; ?>                },
                states: {pager: {page: 1, recPerPage: 15}},
                height: 'page',
                sortable: true
            });
        </script></div><div class="tab-pane fade " id="thread-add"><link type="text/css" href="<?php echo STATIC_ROOT; ?>/css/skin/thread_sender.css" rel="stylesheet"><script type="text/javascript" src="<?php echo STATIC_ROOT; ?>/js/selectFx.js"></script><script type="text/javascript" src="<?php echo STATIC_ROOT; ?>/lib/datetimepicker/datetimepicker.min.js"></script><link href="<?php echo STATIC_ROOT; ?>/lib/datetimepicker/datetimepicker.min.css" rel="stylesheet"><?php
    $showspc = 0;
    foreach($splv1 as $splb){if($splb['hpm']) $showspc = 1; break;}
    foreach($splv2 as $splb){if($splb['hpm']) $showspc = 1; break;}
    foreach($splv3 as $splb){if($splb['hpm']) $showspc = 1; break;}
?><div id="forum_sender"><form id="threadform" enctype="multipart/form-data"><input type="hidden" id="newthread" name="newthread" value="post"><div class="ic"><select class="lb cs-select cs-skin-border" name="threadsort" id="threadsort"><optgroup label="板块"><option>请选择分类</option><?php if(is_array($chunklv1) || $chunklv1 instanceof \think\Collection || $chunklv1 instanceof \think\Paginator): $i = 0; $__LIST__ = $chunklv1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lv1): $mod = ($i % 2 );++$i;if($lv1['hpm']): ?><option value="<?php echo $lv1['id']; ?>">◆ <?php echo $lv1['chunk_name']; ?></option><?php endif; if(is_array($chunklv2) || $chunklv2 instanceof \think\Collection || $chunklv2 instanceof \think\Paginator): $i = 0; $__LIST__ = $chunklv2;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lv2): $mod = ($i % 2 );++$i;if($lv2['chunk_below'] == $lv1['id']): if($lv2['hpm']): ?><option value="<?php echo $lv2['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;◇ <?php echo $lv2['chunk_name']; ?></option><?php endif; if(is_array($chunklv3) || $chunklv3 instanceof \think\Collection || $chunklv3 instanceof \think\Paginator): $i = 0; $__LIST__ = $chunklv3;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lv3): $mod = ($i % 2 );++$i;if($lv3['chunk_below'] == $lv2['id']): if($lv3['hpm']): ?><option value="<?php echo $lv3['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[ <?php echo $lv3['chunk_name']; ?> ]</option><?php endif; endif; endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?></optgroup><?php if($showspc): ?><optgroup label="专题" id="sp"><?php if(is_array($splv1) || $splv1 instanceof \think\Collection || $splv1 instanceof \think\Paginator): $i = 0; $__LIST__ = $splv1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lv1): $mod = ($i % 2 );++$i;if($lv1['hpm']): ?><option value="<?php echo $lv1['id']; ?>">◆ <?php echo $lv1['chunk_name']; ?></option><?php endif; if(is_array($splv2) || $splv2 instanceof \think\Collection || $splv2 instanceof \think\Paginator): $i = 0; $__LIST__ = $splv2;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lv2): $mod = ($i % 2 );++$i;if($lv2['chunk_below'] == $lv1['id']): if($lv2['hpm']): ?><option value="<?php echo $lv2['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;◇ <?php echo $lv2['chunk_name']; ?></option><?php endif; endif; endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?></optgroup><?php endif; ?></select><select class="rb cs-select cs-skin-border" name="threadmode" id="threadmode"><option value="1" selected>保存草稿</option><option value="2">直接推送</option></select><script>                (function () {
                    [].slice.call(document.querySelectorAll('select.cs-select')).forEach(function (el) {
                        new SelectFx(el);
                    });
                })();
            </script><input autocomplete="off" type="text" name="title" id="title" placeholder="标题"></div><script type="text/javascript" src="<?php echo STATIC_ROOT; ?>/lib/ueditor/ueditor.parse.js"></script><script type="text/javascript" src="<?php echo STATIC_ROOT; ?>/lib/ueditor/ueditor.config.js"></script><script type="text/javascript" src="<?php echo STATIC_ROOT; ?>/lib/ueditor/ueditor.all.js"></script><script id="container" value="aaaaaaaaaa" name="content" type="text/plain" style="width: auto"></script><script type="text/javascript">var ue = UE.getEditor('container');ue.ready(function() {ue.setContent('');});</script><div class="infobox l"><div class="ic"><b class="l">描述</b><input autocomplete="off" type="text" name="descrip" id="descrip" placeholder="描述，文章的简介，在搜索显示结果里显示的内容"></div><div id="bar"></div><div class="ic"><b class="l">关键词</b><input autocomplete="off" type="text" name="keyword" id="keyword" placeholder="关键词，用于方便搜索引擎的索引和收录"></div></div><div class="coverimg r"><img src="<?php echo STATIC_ROOT; ?>/img/common/image.png"><script language="JavaScript" src="<?php echo STATIC_ROOT; ?>/js/imgpreview.js"></script><input type="file" onchange="previewImage(this,1)" id="coverimg" name="coverimg"><div class="preview" id="preview1"><img src="" id="imghead1" /></div></div><div id="bar"></div><div class="l ic s"><b class="l">作者</b><input type="text" name="author" id="author" placeholder="留空,作者默认为编辑者"></div><div class="l ic s"><b class="l">时间</b><input type="text" name="htime" id="datetime" placeholder="留空,默认为文章发布日期"></div><button id="submiter" class="r btn" type="button" onclick="post()">发布 \ 保存</button><div id="loading" style="display: none"><img src="/static/img/common/loading.gif"></div></form><script>        function post() {
            $('#loading').css('display','block');
            var data = new FormData(document.querySelector('#threadform'));
            console.log(data);

            $.ajax({
                processData:false,
                contentType:false,
                cache: false,
                url: location.href,
                type: "POST",
                data: data,
                success: function (status) {
                    $('#loading').css('display','none');
                    new $.zui.Messager(status || '提交成功', {
                        icon: 'bell',
                        type: status === '成功发布,你可以继续添加或刷新页面查看结果'? 'success' :'warning',
                        placement: 'bottom-left'
                    }).show();
                    if(status === '成功发布,你可以继续添加或刷新页面查看结果'){
                        $('#title').val('');
                        $('#descrip').val('');
                        $('#keyword').val('');
                        $('#coverimg').val('');
                        $('#author').val('');
                        $('#datetime').val('');
                        document.querySelector('#preview1').innerHTML = '';
                        ue.setContent('');
                    }
                },
                error: function (res) {
                    $('#loading').css('display','none');
                    dump(res);
                }
            })
        }
        $("#datetime").datetimepicker(
            {
                weekStart: 1,
                todayBtn:  1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                forceParse: 0,
                showMeridian: 1,
                format: "yyyy-mm-dd hh:ii"
            });
    </script></div></div><div class="tab-pane fade" id="thread-trash"><div id="trash_table" class="datagrid"><div class="datagrid-container"></div><div class="pager"></div><div class="input-control search-box search-box-circle has-icon-left has-icon-right"><input id="trash_table_search" type="search" class="form-control search-input" placeholder="搜索"><label for="trash_table_search" class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label><a href="#" class="input-control-icon-right search-clear-btn"><i class="icon icon-remove"></i></a></div></div><script>            var trash_table = $('#trash_table').datagrid({
                dataSource: {
                    cols:[
                        {name: 'id', label: '编号', width: 80},
                        {name: 'title', label: '标题'},
                        {name: 'editor', label: '编辑人', width:100},
                        {name: 'time', label: '发布时间', width: 150},
                        {name: 'sort', label: '分类', width: 100},
                        {name: 'func', label: '操作', width: 70,html:true,}
                    ],
                    array: <?php echo $trashdata; ?>            },
            states: {pager: {page: 1, recPerPage: 15}},
            height: 'page',
                sortable: true
            });
        </script></div><div class="tab-pane fade " id="thread-banner"><?php if(is_array($chunklv1) || $chunklv1 instanceof \think\Collection || $chunklv1 instanceof \think\Paginator): $i = 0; $__LIST__ = $chunklv1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lv1): $mod = ($i % 2 );++$i;if($lv1['hpm']): ?><div class="banner-show"><a class="title" href="<?php echo SITE; ?>/consoleboard/threadmag/setbanner/<?php echo $lv1['id']; ?>.html"><?php echo $lv1['chunk_name']; ?></a></div><?php endif; if(is_array($chunklv2) || $chunklv2 instanceof \think\Collection || $chunklv2 instanceof \think\Paginator): $i = 0; $__LIST__ = $chunklv2;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lv2): $mod = ($i % 2 );++$i;if($lv2['chunk_below'] == $lv1['id']): if($lv2['hpm']): ?><div class="banner-show"><a class="title"  href="<?php echo SITE; ?>/consoleboard/threadmag/setbanner/<?php echo $lv2['id']; ?>.html"><?php echo $lv2['chunk_name']; ?></a></div><?php endif; if(is_array($chunklv3) || $chunklv3 instanceof \think\Collection || $chunklv3 instanceof \think\Paginator): $i = 0; $__LIST__ = $chunklv3;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$lv3): $mod = ($i % 2 );++$i;if($lv3['chunk_below'] == $lv2['id']): if($lv3['hpm']): ?><div class="banner-show"><a class="title"  href="<?php echo SITE; ?>/consoleboard/threadmag/setbanner/<?php echo $lv3['id']; ?>.html"><?php echo $lv3['chunk_name']; ?></a></div><?php endif; endif; endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?></div></section><footer class="copyright">Powerd By UED Team</footer></body><script>    $('.menu .nav').on('click', function() {
        var $this = $(this);
        $('.menu .nav .active').removeClass('active');
        $this.closest('li').addClass('active');
        $this.closest('.nav-parent').addClass('show ');
    });
</script></html>