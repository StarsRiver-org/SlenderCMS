<?php if (!defined('THINK_PATH')) exit(); /*a:6:{s:84:"G:\WebDesigne\APMServer\web\[QZ] qzxy.tyut.edu.cn\/template/default\admin\index.html";i:1532063141;s:84:"G:\WebDesigne\APMServer\web\[QZ] qzxy.tyut.edu.cn\/template/default\admin\frame.html";i:1532071042;s:89:"G:\WebDesigne\APMServer\web\[QZ] qzxy.tyut.edu.cn\/template/default\admin\mod_header.html";i:1532858712;s:95:"G:\WebDesigne\APMServer\web\[QZ] qzxy.tyut.edu.cn\/template/default\admin\mod_single_count.html";i:1517130180;s:95:"G:\WebDesigne\APMServer\web\[QZ] qzxy.tyut.edu.cn\/template/default\admin\mod_double_count.html";i:1517130144;s:89:"G:\WebDesigne\APMServer\web\[QZ] qzxy.tyut.edu.cn\/template/default\admin\mod_footer.html";i:1532071110;}*/ ?>
<!DOCTYPE HTML><html><head><title>清泽心雨管理中心</title><meta charset="UTF-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><!--[if lt IE 9]><div class="alert2 alert-danger">您正在使用 <strong>过时的</strong> 浏览器. 请升级您的浏览器来获取更好的体验
        </br><a class="icon-ie updateexplor"
           href="http://download.microsoft.com/download/F/2/8/F2871AC4-E82B-4636-BB37-A5F2B14C8616/IE11-Windows6.1-x86-zh-cn.exe">            x86版本</a><a class="icon-ie updateexplor"
           href="http://download.microsoft.com/download/5/6/F/56FD6253-CB53-4E38-94C6-74367DA2AB34/IE11-Windows6.1-x64-zh-cn.exe">            x64版本</a></div><script src="<?php echo STATIC_ROOT; ?>/lib/ieonly/html5shiv.js"></script><script src="<?php echo STATIC_ROOT; ?>/lib/ieonly/respond.js"></script><![endif]--><link type="text/css" href="<?php echo STATIC_ROOT; ?>/css/base.css?b4f" rel="stylesheet"><link type="text/css" href="<?php echo STATIC_ROOT; ?>/css/fonts_zui.css" rel="stylesheet"><link type="text/css" href="<?php echo STATIC_ROOT; ?>/css/skin/admin.css?b4f" rel="stylesheet"><link type="text/css" href="<?php echo STATIC_ROOT; ?>/lib/datagrid/zui.datagrid.css" rel="stylesheet"><script src="<?php echo STATIC_ROOT; ?>/js/jquery.js"></script><script src="<?php echo STATIC_ROOT; ?>/js/classie.js"></script><script src="<?php echo STATIC_ROOT; ?>/js/clipboard.min.js"></script><script src="<?php echo STATIC_ROOT; ?>/js/zui.min.js"></script><script src="<?php echo STATIC_ROOT; ?>/lib/chart/zui.chart.min.js"></script><script src="<?php echo STATIC_ROOT; ?>/lib/datagrid/zui.datagrid.js"></script><script src="<?php echo STATIC_ROOT; ?>/js/common.js"></script><script>        SiteUrl = "<?php echo SITE; ?>";
        StaticUrl = "<?php echo SITE; ?>/static";
    </script></head><style>body{  box-shadow: inset 180px 0 0 #f9f9f9 , inset 181px 0 0 #ccc;}</style><body><header><div class="logo"><img src="<?php echo STATIC_ROOT; ?>/img/admin/logo.png"></div><ul class="nav nav-tabs headnav"><li class="<?php echo $index; ?>"><a class="icon-home" href="<?php echo SITE; ?>/consoleboard/index.html" data-target="#homepage">首页</a></li><li class="<?php echo $threadmag; ?>"><a href="<?php echo SITE; ?>/consoleboard/threadmag.html" data-target="#threadmanager">内容管理</a></li><li class="<?php echo $navmag; ?>"><a href="<?php echo SITE; ?>/consoleboard/navmag.html" data-target="#threadmanager">导航管理</a></li><li class="<?php echo $chunkmag; ?>"><a href="<?php echo SITE; ?>/consoleboard/chunkmag.html" data-target="#threadmanager">板块管理</a></li><li class="<?php echo $usermag; ?>"><a href="<?php echo SITE; ?>/consoleboard/usermag.html" data-target="#usermanager">用户中心</a></li><li class="<?php echo $configmag; ?>"><a class="sysconfmenu" href="<?php echo SITE; ?>/consoleboard/configmag.html" data-target="#usermanager">系统配置</a></li><li class="<?php echo $enrollmag; ?>"><a href="<?php echo SITE; ?>/enroll/enrollmag.html" data-target="#usermanager">报名管理</a></li><li class="hidden <?php echo $other; ?>"><a>其他</a></li></ul><form method="post" class="admin_exit"><input type="hidden" name="exit" value="confirm"><button type="submit" class=" icon-signout " title="安全登出"></button></form></header><nav class="menu col-xs-0 sidenav" data-ride="menu"><ul class="nav nav-primary"><li class="nav-parent show"><a><i class="icon icon-dashboard"></i>统计台</a><ul class="nav"><li class="active"><a data-target="#stats-visit" data-toggle="tab" href="javascript:;" >访问统计</a></li></ul></li></ul></nav><section class="tab-content col-xs--1"><div class="tab-pane fade active in" id="stats-visit"><div class="globle-status"><div class="block"><div class="tap"><i class="icon-list-alt" style="color:#42abfb;"></i><em>今日发布/文章总量</em><span><?php echo $info_article['today']; ?>/<?php echo $info_article['all']; ?></span></div><div class="tap"><i class="icon-line-chart" style="color: #da7741"></i><em>总浏览次数</em><span><?php echo $info_visit['article']['allsite']['all'] + $info_visit['portal']['allsite']['all']; ?></span></div><div class="tap"><i class="icon-pie-chart" style="color: #0eba71"></i><em>文章有效浏览次数</em><span><?php echo $info_article['visit']; ?></span></div><div class="tap"><i class="icon-heart" style="color: #eb5045"></i><em>被喜欢次数</em><span><?php echo $info_article['like']; ?></span></div></div><div class="block"><div class="card tap-big"><div class="chart-mid"><span class="title">管理员近期操作次数</span><?php $mod = ["管理员访问","consoleboard","allsite","day","12"]; 
    $chunk = $mod[1];
    $child = $mod[2];
    $timegap = $mod[3];
    $all = count($info_visit[$chunk][$child][$timegap]) - 1;
    $num = isset($mod[4]) ? $mod[4] : $all;
    $loop = $all - $num;
?><div class="gird-left"><canvas id="linechart_<?php echo $chunk; ?>_<?php echo $child; ?>_<?php echo $timegap; ?>"></canvas></div><script>    var data = {
        labels:[<?php for($i = $loop; $i < $all; $i++){echo $info_visit[$chunk][$child][$timegap][$i]["date"].",";} ?>],
        datasets: [{
            label: "<?php echo $mod[0]; ?>",
            color: "#ee7622",
            data: [<?php for($i = $loop; $i < $all; $i++){echo $info_visit[$chunk][$child][$timegap][$i]["value"].",";} ?>]
        }]
    };
    $("#linechart_<?php echo $chunk; ?>_<?php echo $child; ?>_<?php echo $timegap; ?>").lineChart(data);
</script></div></div></div></div><div class="card"><div class="chart-big"><span class="title">今日访问（时）</span><?php $mod = [["主页访问","文章浏览"],["portal","article"],["allsite","allsite"],"hour"]; 
    $chunk = $mod[1];
    $child = $mod[2];
    $timegap = $mod[3];
    $all = count($info_visit[$chunk[0]][$child[0]][$timegap]) - 1;
    $num = isset($mod[4]) ? $mod[4] : $all;
    $loop = $all - $num;
?><div class="gird-left"><canvas id="linechart_<?php echo $child[0]; ?>_<?php echo $child[1]; ?>_<?php echo $timegap; ?>"></canvas></div><div class="gird-right"><canvas id="piechart_<?php echo $child[0]; ?>_<?php echo $child[1]; ?>_<?php echo $timegap; ?>"></canvas></div><script>    var data = {
        labels:[<?php for($i = $loop; $i<$num; $i++){echo $info_visit[$chunk[0]][$child[0]][$timegap][$i]["date"].",";} ?>],
        datasets: [{
            label: "<?php echo $mod[0][0]; ?>",
            color: "#ff5b34",
            data: [<?php for($i = $loop; $i<$num; $i++){echo $info_visit[$chunk[0]][$child[0]][$timegap][$i]["value"].",";} ?>]
            }, {
            label: "<?php echo $mod[0][1]; ?>",
            color: "#48b35b",
            data: [<?php for($i = $loop; $i<$num; $i++){echo $info_visit[$chunk[1]][$child[1]][$timegap][$i]["value"].",";} ?>]
            }
        ]
    };
    var data2 = [{
        label:"<?php echo $mod[0][0]; ?>",
        value: <?php echo $info_visit[$chunk[0]][$child[0]][$timegap]["count"]; ?>,
        color:"#ff5b34"
    }, {
        label:"<?php echo $mod[0][1]; ?>",
        value : <?php echo $info_visit[$chunk[1]][$child[1]][$timegap]['count']; ?>,
        color : "#48b35b"
    }
    ];
    $("#linechart_<?php echo $child[0]; ?>_<?php echo $child[1]; ?>_<?php echo $timegap; ?>").lineChart(data);
    $("#piechart_<?php echo $child[0]; ?>_<?php echo $child[1]; ?>_<?php echo $timegap; ?>").doughnutChart(data2,{percentageInnerCutout:50,scaleShowLabels: true,segmentStrokeWidth : 3, scaleLabelPlacement: "inside"});
</script></div></div><div class="card"><div class="chart-big"><span class="title">最近月内访问（日）</span><?php $mod = [["主页访问","文章浏览"],["portal","article"],["allsite","allsite"],"day"]; 
    $chunk = $mod[1];
    $child = $mod[2];
    $timegap = $mod[3];
    $all = count($info_visit[$chunk[0]][$child[0]][$timegap]) - 1;
    $num = isset($mod[4]) ? $mod[4] : $all;
    $loop = $all - $num;
?><div class="gird-left"><canvas id="linechart_<?php echo $child[0]; ?>_<?php echo $child[1]; ?>_<?php echo $timegap; ?>"></canvas></div><div class="gird-right"><canvas id="piechart_<?php echo $child[0]; ?>_<?php echo $child[1]; ?>_<?php echo $timegap; ?>"></canvas></div><script>    var data = {
        labels:[<?php for($i = $loop; $i<$num; $i++){echo $info_visit[$chunk[0]][$child[0]][$timegap][$i]["date"].",";} ?>],
        datasets: [{
            label: "<?php echo $mod[0][0]; ?>",
            color: "#ff5b34",
            data: [<?php for($i = $loop; $i<$num; $i++){echo $info_visit[$chunk[0]][$child[0]][$timegap][$i]["value"].",";} ?>]
            }, {
            label: "<?php echo $mod[0][1]; ?>",
            color: "#48b35b",
            data: [<?php for($i = $loop; $i<$num; $i++){echo $info_visit[$chunk[1]][$child[1]][$timegap][$i]["value"].",";} ?>]
            }
        ]
    };
    var data2 = [{
        label:"<?php echo $mod[0][0]; ?>",
        value: <?php echo $info_visit[$chunk[0]][$child[0]][$timegap]["count"]; ?>,
        color:"#ff5b34"
    }, {
        label:"<?php echo $mod[0][1]; ?>",
        value : <?php echo $info_visit[$chunk[1]][$child[1]][$timegap]['count']; ?>,
        color : "#48b35b"
    }
    ];
    $("#linechart_<?php echo $child[0]; ?>_<?php echo $child[1]; ?>_<?php echo $timegap; ?>").lineChart(data);
    $("#piechart_<?php echo $child[0]; ?>_<?php echo $child[1]; ?>_<?php echo $timegap; ?>").doughnutChart(data2,{percentageInnerCutout:50,scaleShowLabels: true,segmentStrokeWidth : 3, scaleLabelPlacement: "inside"});
</script></div></div><div class="card"><div class="chart-big"><span class="title">一季度访问（周）</span><?php $mod = [["主页访问","文章浏览"],["portal","article"],["allsite","allsite"],"week"]; 
    $chunk = $mod[1];
    $child = $mod[2];
    $timegap = $mod[3];
    $all = count($info_visit[$chunk[0]][$child[0]][$timegap]) - 1;
    $num = isset($mod[4]) ? $mod[4] : $all;
    $loop = $all - $num;
?><div class="gird-left"><canvas id="linechart_<?php echo $child[0]; ?>_<?php echo $child[1]; ?>_<?php echo $timegap; ?>"></canvas></div><div class="gird-right"><canvas id="piechart_<?php echo $child[0]; ?>_<?php echo $child[1]; ?>_<?php echo $timegap; ?>"></canvas></div><script>    var data = {
        labels:[<?php for($i = $loop; $i<$num; $i++){echo $info_visit[$chunk[0]][$child[0]][$timegap][$i]["date"].",";} ?>],
        datasets: [{
            label: "<?php echo $mod[0][0]; ?>",
            color: "#ff5b34",
            data: [<?php for($i = $loop; $i<$num; $i++){echo $info_visit[$chunk[0]][$child[0]][$timegap][$i]["value"].",";} ?>]
            }, {
            label: "<?php echo $mod[0][1]; ?>",
            color: "#48b35b",
            data: [<?php for($i = $loop; $i<$num; $i++){echo $info_visit[$chunk[1]][$child[1]][$timegap][$i]["value"].",";} ?>]
            }
        ]
    };
    var data2 = [{
        label:"<?php echo $mod[0][0]; ?>",
        value: <?php echo $info_visit[$chunk[0]][$child[0]][$timegap]["count"]; ?>,
        color:"#ff5b34"
    }, {
        label:"<?php echo $mod[0][1]; ?>",
        value : <?php echo $info_visit[$chunk[1]][$child[1]][$timegap]['count']; ?>,
        color : "#48b35b"
    }
    ];
    $("#linechart_<?php echo $child[0]; ?>_<?php echo $child[1]; ?>_<?php echo $timegap; ?>").lineChart(data);
    $("#piechart_<?php echo $child[0]; ?>_<?php echo $child[1]; ?>_<?php echo $timegap; ?>").doughnutChart(data2,{percentageInnerCutout:50,scaleShowLabels: true,segmentStrokeWidth : 3, scaleLabelPlacement: "inside"});
</script></div></div><div class="card"><div class="chart-big"><span class="title">一年期内访问（月）</span><?php $mod = [["主页访问","文章浏览"],["portal","article"],["allsite","allsite"],"moon"]; 
    $chunk = $mod[1];
    $child = $mod[2];
    $timegap = $mod[3];
    $all = count($info_visit[$chunk[0]][$child[0]][$timegap]) - 1;
    $num = isset($mod[4]) ? $mod[4] : $all;
    $loop = $all - $num;
?><div class="gird-left"><canvas id="linechart_<?php echo $child[0]; ?>_<?php echo $child[1]; ?>_<?php echo $timegap; ?>"></canvas></div><div class="gird-right"><canvas id="piechart_<?php echo $child[0]; ?>_<?php echo $child[1]; ?>_<?php echo $timegap; ?>"></canvas></div><script>    var data = {
        labels:[<?php for($i = $loop; $i<$num; $i++){echo $info_visit[$chunk[0]][$child[0]][$timegap][$i]["date"].",";} ?>],
        datasets: [{
            label: "<?php echo $mod[0][0]; ?>",
            color: "#ff5b34",
            data: [<?php for($i = $loop; $i<$num; $i++){echo $info_visit[$chunk[0]][$child[0]][$timegap][$i]["value"].",";} ?>]
            }, {
            label: "<?php echo $mod[0][1]; ?>",
            color: "#48b35b",
            data: [<?php for($i = $loop; $i<$num; $i++){echo $info_visit[$chunk[1]][$child[1]][$timegap][$i]["value"].",";} ?>]
            }
        ]
    };
    var data2 = [{
        label:"<?php echo $mod[0][0]; ?>",
        value: <?php echo $info_visit[$chunk[0]][$child[0]][$timegap]["count"]; ?>,
        color:"#ff5b34"
    }, {
        label:"<?php echo $mod[0][1]; ?>",
        value : <?php echo $info_visit[$chunk[1]][$child[1]][$timegap]['count']; ?>,
        color : "#48b35b"
    }
    ];
    $("#linechart_<?php echo $child[0]; ?>_<?php echo $child[1]; ?>_<?php echo $timegap; ?>").lineChart(data);
    $("#piechart_<?php echo $child[0]; ?>_<?php echo $child[1]; ?>_<?php echo $timegap; ?>").doughnutChart(data2,{percentageInnerCutout:50,scaleShowLabels: true,segmentStrokeWidth : 3, scaleLabelPlacement: "inside"});
</script></div></div></div></section><footer class="copyright">Powerd By UED Team</footer></body><script>    $('.menu .nav').on('click', function() {
        var $this = $(this);
        $('.menu .nav .active').removeClass('active');
        $this.closest('li').addClass('active');
        $this.closest('.nav-parent').addClass('show ');
    });
</script></html>