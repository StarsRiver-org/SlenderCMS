/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2018-07-12
 *
 */
/*
 * Sms 与 Chk 的使用方法
 * 本函数仅适用与报名系统的短信发送
 *
 * 使用前首先要定义服务器接口，以及接口入口。
 *
 * 基础结构 ： （依赖于主体的 id【surface】 值进行操作，可同时加载多个操作台）
 * <select data-type="campus" name="campus"></select>
 * <div id="surface" data-url="logic">
 *     <div class="datatable">
 *          <div class="datahead"></div>
 *          <div class="datalist"></div>
 *     </div>
 *     <div class="input-group">
 *          <div class="solid-area sms-tml">
 *              <em class="title">短信动态参数</em>
 *              <input class="where" placeholder="地点">
 *              <input class="cls" placeholder="部门">
 *              <input class="time" id="timeselector1" placeholder="时间">
 *              <input class="aphone" placeholder="管理员电话">
 *          </div>
 *          <div class="solid-area btn-block">
 *              <em class="title">其他</em>
 *              <input class="msgtpl" placeholder="短信模板编号">
 *              <input class="perpage" type="number" onblur="Sms.init('#surface')" placeholder="每页数据量(0~30~99)">
 *          </div>
 *          <div class="solid-area btn-group">
 *              <button class="submit">提 交</button>
 *          </div>
 *          <div class="solid-area btn-group">
 *              <button onclick="Chk.chkall('#surface')">全选</button>
 *              <button onclick="Chk.unchkall('#surface')">全不选</button>
 *          </div>
 *     </div>
 * </div>
 *
 * 初始化
 *
 * Sms.init('#surface');
 */

partys = '';
campus = '';
$.ajaxSettings.async = false;
$.get(SiteUrl + '/api/getpartys',function(result){partys = JSON.parse(result)});
$.get(SiteUrl + '/api/getcampus',function(result){campus = JSON.parse(result)});
$.ajaxSettings.async = true;

initcampus();
function initcampus(){
    var cphtml = '<option selected value="0">全部</option>';
    for (i in campus) {
        cphtml += '<option value="' + i + '">' + campus[i] + '</option>';
    }
    var campussl = document.querySelectorAll('[data-type="campus"]');
    for (x in campussl) {
        campussl[x].innerHTML = cphtml;
    }
}

var Chk = {
    chkall: function (obj) {
        var lis = document.querySelectorAll(obj + ' .datalist li');
        for (var i = 0; i < lis.length; i++) {
            lis[i].querySelector('input').checked = true;
        }
    },

    unchkall: function (obj) {
        var lis = document.querySelectorAll(obj + ' .datalist li');
        for (var i = 0; i < lis.length; i++) {
            lis[i].querySelector('input').checked = false
        }
    },
    
    inarray: function (key, arr) {
        for (var i = 0; i < arr.length; i++){
            if(arr[i] = key){
                return 1;
            }
        }
        return 0;
    }
};

var Sms = {
    campus: 0,

    page: 1,

    vol: 0,

    list: [],

    data: [],

    token: '',

    ftcap: '',

    init: function (obj) {
        var ctn = document.querySelector(obj),
            logicURL = ctn.getAttribute('data-url'),
            loading = ctn.querySelector('.loading'),
            datahead = ctn.querySelector(".datahead"),
            datalist = ctn.querySelector(".datalist"),
            pagethumb = ctn.querySelector('.pagethumb'),
            tokenthumb = ctn.querySelector('.tokenthumb'),
            perpage = ctn.querySelector('.perpage'),
            num = perpage ? perpage.value : 30,
            submit = ctn.querySelector(".submit"),
            selecter = ctn.querySelector(".select_all"),
            unselecter = ctn.querySelector(".unselect_all"),
            campusslect = document.querySelector('[data-type="campus"]'),
            that = this;

        if(perpage){perpage.onblur= function () {Sms.init(obj);};}
        if(selecter){selecter.onclick = function(){Chk.chkall(obj)};}
        if(unselecter){unselecter.onclick = function(){Chk.unchkall(obj)};}

        /* 规定按钮类型 短信发送和筛选通过 */
        if(submit){
            switch (logicURL){
                case 'logic_3':
                    submit.innerHTML = '通 过';
                    submit.onclick = function(){Enfuc.enroll(obj)};
                    break;
                default:
                    submit.innerHTML = '提 交';
                    submit.onclick = function(){Sms.sendsms(obj)};
            }
        }

        /* 校区选择 */
        if(campusslect){
            campusslect.onchange = function () {
                that.campus = campusslect.options[campusslect.selectedIndex].value;
                Sms.init(obj)
            };
        }

        loading.style.display = "";

        if(!num){}
        else if(num > 99){ctn.querySelector('.perpage').value = 99;}
        else if(num < 10){ctn.querySelector('.perpage').value = 10;}

        $.ajax({
            url:  SiteUrl + "/enroll/enrollmag/" + logicURL,
            type: 'POST',
            data: {
                med: 'refresh',
                num: num ? (num < 10 ? 10 : (num > 99 ? 99 : num)) : 30,
                page: that.page ? that.page : 1,
                token: that.token ? that.token : '',
                ftcap: that.ftcap ? that.ftcap : '',
                campus: that.campus ? that.campus : ''
            },
            success: function (result) {
                if(JSON.parse(result).Stat === 'OK'){
                    if(pagethumb){
                        pagethumb.innerHTML = JSON.parse(result).Pages;
                        var pta = pagethumb.querySelectorAll('.pagethumb li');
                        for(p in pta){
                            pta[p].onclick = function () {
                                that.page = getEventobj().getAttribute('data-page');
                                Sms.init(obj);
                            }
                        }
                    }

                    if(tokenthumb){
                        tokenthumb.innerHTML = '';
                        tokens = JSON.parse(result).Tokens;
                        for (var t = 0; t < tokens.length; t++){
                            var temp = document.createElement('a');
                            var tk = tokens[t];
                            temp.className = 'token' + ((that.token == tk.ft && that.ftcap == tk.cpid) ? ' active' : '');
                            temp.setAttribute('data-time',tk.ft);
                            temp.setAttribute('data-camp',tk.cp);
                            temp.setAttribute('data-cpid',tk.cpid);
                            temp.innerHTML = '【' + tk.cp + '】' + tk.ft;
                            temp.onclick = function () {
                                that.token = this.getAttribute('data-time');
                                that.ftcap = this.getAttribute('data-cpid');
                                Sms.init(obj);
                            };
                            tokenthumb.appendChild(temp);
                        }
                    }

                    datahead.innerHTML = '';
                    datalist.innerHTML = '';

                    var eul = document.createElement('ul');
                    var data = JSON.parse(result).Data;
                    var elih = '<li class="item">' +
                        ((logicURL === 'logic_4' || logicURL === 'logic_6' || logicURL === 'logic_5' || logicURL === 'logic_3') ? '<i class="back">祥</i>' : '') +
                        ((logicURL === 'logic_4') ? '<i class="back">X</i>' : '') +
                        (logicURL === 'logic_2' ? '<i class="back">面</i>' : '') +
                        '<i class="chk text-success bold" style="font-weight: 700">'+ data.length +'</i>' +
                        '<i class="name ">姓名</i>' +
                        '<i class="sex ">性别</i>' +
                        ((logicURL === 'logic_3' || logicURL === 'logic_6' || logicURL === 'logic_4') ? '<i class="sex ">打分</i>' : '') +
                        '<i class="college ">校区</i>' +
                        '<i class="college ">学院</i>' +
                        '<i class="major ">专业</i>' +
                        ((logicURL !== 'logic_3' && logicURL !== 'logic_4' && logicURL !== 'logic_6') ? '<i class="class">班级</i>' : '') +
                        ((logicURL !== 'logic_2' && logicURL !== 'logic_3' && logicURL !== 'logic_4' && logicURL !== 'logic_6') ? '<i class="aim">志愿1</i>' : '') +
                        ((logicURL !== 'logic_4' && logicURL !== 'logic_6') ? '<i class="aim">志愿2</i>' : '') +
                        '<i class="phone">电话</i>' +
                        ((logicURL === 'logic_1' || logicURL === 'logic_3' || logicURL === 'logic_4' || logicURL === 'logic_6') ? ('<i class="stat">'+ (logicURL === 'logic_3' ? '处理状态' : '发送状态') + '</i>') : '') +
                        ((logicURL === 'logic_3' || logicURL === 'logic_4' || logicURL === 'logic_6') ? '<i class="sug">面试官意见</i>' : '') +
                        (logicURL === 'logic_2' ? '<i class="sug">面试时间</i>' : '') +
                        '</li>';

                    for (i in data) {
                        var eli = '<li class="item '+ (((logicURL === 'logic_1' && data[i].hascalled === 1) || (logicURL === 'logic_2' && data[i].hascalled === 2)) ? 'highlight-absence' : '') +'" id="uid_' + data[i].id + '">' +
                            ((logicURL === 'logic_4' || logicURL === 'logic_6' || logicURL === 'logic_5' || logicURL === 'logic_3') ? '<i class="back icon-folder-open-alt" onclick="Enfuc.watch()"></i>' : '' )+
                            ((logicURL === 'logic_4') ? '<i class="back icon-collapse-alt" onclick="Enfuc.unenroll()"></i>' : '' )+
                            (logicURL === 'logic_2' ? '<i class="back icon-coffee" onclick="Enfuc.f2f.open()"></i>' : '' )+
                            '<i class="chk"><input id="' + data[i].id + '" type="checkbox"></i>' +
                            '<i class="name ">' + data[i].name + '</i>' +
                            '<i class="sex">' + data[i].sex + '</i>' +
                            ((logicURL === 'logic_3' || logicURL === 'logic_6' || logicURL === 'logic_4') ? '<i class="sex ">' + data[i].score + '</i>' : '') +
                            '<i class="college">' + campus[data[i].campus] + '</i>' +
                            '<i class="college">' + data[i].college + '</i>' +
                            '<i class="major">' + data[i].major + '</i>' +
                            ((logicURL !== 'logic_3' && logicURL !== 'logic_4' && logicURL !== 'logic_6') ? '<i class="class">' + data[i].class + '</i>' : '') +
                            ((logicURL !== 'logic_2' && logicURL !== 'logic_3' && logicURL !== 'logic_4' && logicURL !== 'logic_6') ? '<i class="aim" title="' + data[i].reasion + '">' + data[i].aim + '</i>' : '') +
                            ((logicURL !== 'logic_4' && logicURL !== 'logic_6') ? '<i class="aim" title="' + data[i].reasion2 + '">' + data[i].aim2 + '</i>' : '') +
                            '<i class="phone">' + data[i].phone + '</i>' +
                            ((logicURL === 'logic_1' || logicURL === 'logic_3' || logicURL === 'logic_4' || logicURL === 'logic_6') ? '<i class="stat"></i>' : '') +
                            ((logicURL === 'logic_3' || logicURL === 'logic_4' || logicURL === 'logic_6') ? '<i class="sug">' + data[i].sug + '</i>' : '') +
                            (logicURL === 'logic_2' ? '<i class="sug">' + data[i].ftime + '</i>' : '') +
                            '</li>';

                        eul.innerHTML += eli;
                    }

                    datahead.innerHTML = elih;
                    datalist.appendChild(eul);
                    lists = datalist.querySelectorAll('li');
                    for(var i = 0;i < lists.length; i++){
                        lists[i].onclick = function () {
                            for(var i = 0; i < lists.length; i++){
                                $(lists[i]).removeClass('active');
                            }
                            $(this).addClass('active');
                        }
                    }
                    loading.style.display = 'none';

                } else {
                    new $.zui.Messager(JSON.parse(result).Message, {
                        type: 'warning',
                        placement: 'bottom-left'
                    }).show();
                }
            },
            error: function (res) {
                loading.style.display = 'none';
                dump(res)
            }
        })
    },

    sendsms: function (obj) {

        this.data = [];
        this.list = [];
        this.vol = 0;

        var k = 0;

        var ctn = document.querySelector(obj),
            logicURL = ctn.getAttribute('data-url'),
            submit = ctn.querySelector(".submit"),
            lis = ctn.querySelectorAll('.datalist li'),
            cls = ctn.querySelector('.cls')     ? ctn.querySelector('.cls').value : '',
            tsr = ctn.querySelector('.time')    ? ctn.querySelector('.time').value : '',
            whr = ctn.querySelector('.where')   ? ctn.querySelector('.where').value : '',
            ape = ctn.querySelector('.aphone')  ? ctn.querySelector('.aphone').value : '',
            qq =  ctn.querySelector('.qq')      ? ctn.querySelector('.qq').value : '',
            link = 'enroll/enroll',
            msgtpl = ctn.querySelector('.msgtpl').value;

        if(
            ((!cls || !tsr || !whr || !ape) && logicURL === 'logic_1') ||
            (!cls && logicURL === 'logic_4')
        ){
            $('#modal_smsdata_uc').modal('show');
            return 0;
        }

        for (var i = 0; i < lis.length; i++) {
            //通过for循环加if判断是否选中
            if (lis[i].querySelector('input').checked) {
                //若选中创建模拟表单

                var id = lis[i].querySelector('input').id;
                var phone = lis[i].querySelector('.phone').innerHTML;
                var msgdat = JSON.stringify({
                    "name": lis[i].querySelector('.name').innerHTML,
                    "class": cls,
                    "time": tsr,
                    "where": whr,
                    "phone": ape,
                    "qq": qq,
                    "link": link,
                });

                var form = document.createElement('form');
                var html = '' +
                    '<input name="med" value="sendsms">' +
                    '<input name="id" value="' + id + '">' +
                    '<input name="phone"  value="' + phone + '">' +
                    '<input name="msgtpl" value="' + msgtpl + '">' +
                    '<textarea name="msgdat">' + msgdat + '</textarea>' +
                    '';

                form.innerHTML = html;

                this.data[k] = new FormData(form);
                this.list[k] = lis[i].id;
                ++k;
            }
        }

        if(k !== 0 ){
            submit.innerHTML = '...';
            submit.onclick = function(){};
            this.obtainId(obj);
        }
    },

    obtainId: function (obj) {
        var that = this;
        var ctn = document.querySelector(obj),
            logicURL = ctn.getAttribute('data-url'),
            submit = ctn.querySelector(".submit");

        $.ajax({
            url:  SiteUrl + "/enroll/enrollmag/" + logicURL,
            type: 'POST',
            processData: false,
            contentType: false,
            cache: false,
            data: that.data[that.vol],
            success: function (res) {
                ctn.querySelector('#' + that.list[that.vol] + ' .stat').innerHTML = '<i class="text-' + (JSON.parse(res).Stat === 'OK' ? 'success' : 'danger') + '">' + JSON.parse(res).Message + '</i>';
                ++that.vol;
                if (that.vol < that.data.length) {
                    that.obtainId(obj);
                } else {
                    submit.innerHTML = '刷 新';
                    submit.onclick = function(){Sms.init(obj)};
                    $('#modal_next').modal('show');
                }
            },
            error: function () {
                ctn.querySelector('#' + that.list[that.vol] + ' .stat').innerHTML = '<i class="text-danger">无网络或找不到服务器</i>';
                ++that.vol;
                if (that.vol < that.data.length) {
                    that.obtainId(obj);
                } else {
                    submit.innerHTML = '刷 新';
                    submit.onclick = function(){Sms.init(obj)};
                    $('#modal_next').modal('show');
                }
            }
        })
    }
};

var Enfuc = {
    data : [],

    list : [],

    vol : 0,

    watch: function(){
        var target = getEventobj().parentNode,
            id = target.querySelector('input').id,
            ph = target.querySelector('.phone').innerHTML;

        $.ajax(SiteUrl + "/enroll/enrollmag/ei",{
            data:{
                id : id,
                phone : ph
            },
            type:'POST',
            success: function (res) {
                var data = JSON.parse(res);
                var uif = data.Data;
                if(data.Stat === 'OK'){
                    var uif = data.Data;
                    var partyselector = '';
                    for (key in partys) {
                        if(partys[key] !== uif.aim){
                            partyselector += '' +
                                '<label for="party_'+key+'" class="radio">' +
                                '    <span class="radio-bg"></span>' +
                                '    <input type="radio" name="party" id="party_'+key+'" value="'+key+'"'+ (uif.aim2 === partys[key] ? ' checked' : '') +' />' + partys[key] +
                                '    <span class="radio-on"></span>' +
                                '</label>';
                        }
                    }

                    var modal = document.querySelector('#enrollerinfo');
                    modal.querySelector('.e_pho').src = uif.photo;
                    modal.querySelector('.e_name').innerHTML = uif.name;
                    modal.querySelector('.e_sex').innerHTML = uif.sex;
                    modal.querySelector('.e_col').innerHTML = uif.college;
                    modal.querySelector('.e_spe').innerHTML = uif.major;
                    modal.querySelector('.e_cls').innerHTML = uif.class;
                    modal.querySelector('.e_reasion').innerHTML = uif.reasion;
                    modal.querySelector('.e_sug').innerHTML = uif.sug;
                    modal.querySelector('.turn_aim').innerHTML = partyselector;
                    modal.querySelector('.turn_confirm').onclick = function(){
                        var party = modal.querySelector('.turn_aim input:checked');
                        $.ajax(  SiteUrl + "/enroll/enrollmag/logic_2",{
                            type:'POST',
                            data:{
                                med : 'turn',
                                id : id,
                                phone : ph,
                                party : party.value,
                            },
                            success:function (res) {
                                var resp = JSON.parse(res);
                                if(resp.Stat === 'OK'){
                                    $('#f2f').modal('hide');
                                    var line = $('#uid_'+id);
                                    line.addClass('fadeout');
                                    line.removeClass('active');
                                    document.querySelector('#enroll_act .datahead .chk').innerHTML -= 1;
                                    setTimeout(function () {
                                        line.remove();
                                    },600);

                                }
                                new $.zui.Messager(resp.Message, {
                                    type:resp.Stat === 'OK' ? 'success' : 'warning',
                                    placement: 'bottom-left'
                                }).show();
                            },
                            error:function (res) {
                                dump(res)
                            }
                        });
                    };
                    var tabs = modal.querySelectorAll('.nav-tabs li');
                    $(tabs[0]).addClass('active');
                    $(tabs[1]).removeClass('active');

                    var tct = modal.querySelectorAll('.tab-pane');
                    $(tct[0]).addClass('active');
                    $(tct[1]).removeClass('active');

                    $('#enrollerinfo').modal({
                        show:true,
                        moveable:true
                    });
                } else {
                    new $.zui.Messager(data.Message, {
                        type:'warning',
                        placement: 'bottom-left'
                    }).show();
                }

            },
            error: function (res) {
                dump(res);
            }
        })
    },

    f2f: {

        token:'',
        ftcap:'',

        initftime : function (fm) {
            var tag = document.querySelector('#f2f .ftime_list');
            var that = this;

            $.ajax(SiteUrl + "/enroll/enrollmag/getftime/" + fm + '?JSON=1',{
                type:'GET',
                success:function (res) {
                    tag.innerHTML = '';
                    that.token = '';
                    var tokens = JSON.parse(res);
                    if(tokens.Stat !== 'error'){
                        for (t in tokens){
                            var temp = document.createElement('a');
                            var tk = tokens[t];
                            temp.className = 'token' + ((that.token == tk.ft && that.ftcap == tk.cpid) ? ' active' : '');
                            temp.setAttribute('data-time',tk.ft);
                            temp.setAttribute('data-camp',tk.cp);
                            temp.setAttribute('data-cpid',tk.cpid);
                            temp.innerHTML = '【' + tk.cp + '】' + tk.ft;
                            temp.onclick = function () {
                                if($(this).hasClass('active')){
                                    that.token = '';
                                    that.ftcap = '';
                                    $(this.parentNode.querySelectorAll('a')).removeClass('active');
                                } else {
                                    that.token = this.getAttribute('data-time');
                                    that.ftcap = this.getAttribute('data-cpid');
                                    $(this.parentNode.querySelectorAll('a')).removeClass('active');
                                    $(this).addClass('active');
                                }
                            };
                            tag.appendChild(temp);
                        }
                    }
                }
            })
        },

        open : function(){
            var target = getEventobj().parentNode,
                id = target.querySelector('input').id,
                ph = target.querySelector('.phone').innerHTML;
            var that = this;

            $.ajax(SiteUrl + "/enroll/enrollmag/ei",{
                data:{
                    id : id,
                    phone : ph
                },
                type:'POST',
                success: function (res) {
                    var data = JSON.parse(res);
                    if(data.Stat === 'OK'){
                        var uif = data.Data;
                        var partyselector = '';
                        for (key in partys) {
                            if(partys[key] !== uif.aim){
                                partyselector += '' +
                                    '<label for="party_'+key+'" class="radio">' +
                                    '    <span class="radio-bg"></span>' +
                                    '    <input type="radio" name="party" onclick="Enfuc.f2f.initftime('+key+')" id="party_'+key+'" value="'+key+'"'+ (uif.aim2 === partys[key] ? ' checked' : '') +' />' + partys[key] +
                                    '    <span class="radio-on"></span>' +
                                    '</label>';
                                if(partys[key] === uif.aim2){
                                    that.initftime(key);
                                }
                            }
                        }
                        partyselector += '<hr style="margin:10px 50px 10px 0;">' +
                            '<label for="party_0" class="radio" style="width: 100% ">' +
                            '    <span class="radio-bg"></span>' +
                            '    <input type="radio" name="party" id="party_0" value="0" ' + (uif.aim2 === '' ? ' checked' : '') +'/> 直接拒接（将从库中移除该面试者）' +
                            '    <span class="radio-on"></span>' +
                            '</label><br>';

                        var modal = document.querySelector('#f2f');
                        modal.querySelector('.e_pho').src = uif.photo;
                        modal.querySelector('.e_name').innerHTML = uif.name;
                        modal.querySelector('.e_sex').innerHTML = uif.sex;
                        modal.querySelector('.e_col').innerHTML = uif.college;
                        modal.querySelector('.e_spe').innerHTML = uif.major;
                        modal.querySelector('.e_cls').innerHTML = uif.class;
                        modal.querySelector('.e_reasion').innerHTML = uif.reasion;
                        modal.querySelector('.e_score').value = uif.score;
                        modal.querySelector('.e_sug').value = uif.sug;
                        modal.querySelector('.turn_aim').innerHTML = partyselector;
                        modal.querySelector('.turn_confirm').onclick = function(){Enfuc.f2f.turn(id,ph);};
                        modal.querySelector('.pass_confirm').onclick = function(){Enfuc.f2f.pass(id,ph);};
                        modal.querySelector('.absence_confirm').onclick = function(){Enfuc.f2f.absence(id,ph);};
                        modal.querySelector('#abs-btn-lock').checked = '';

                        var tabs = modal.querySelectorAll('.nav-tabs li');
                        $(tabs[0]).addClass('active');
                        $(tabs[1]).removeClass('active');

                        var tct = modal.querySelectorAll('.tab-pane');
                        $(tct[0]).addClass('active');
                        $(tct[1]).removeClass('active');

                        $('#f2f').modal({
                            show:true,
                            moveable:true
                        });
                    } else {
                        new $.zui.Messager(data.Message, {
                            type:'warning',
                            placement: 'bottom-left'
                        }).show();
                    }
                },
                error: function (res) {
                    dump(res);
                }
            })
        },

        pass:function(id,phone){
            var sug = document.querySelector('#f2f .e_sug').value;
            var scr = document.querySelector('#f2f .e_score').value;

            $.ajax(  SiteUrl + "/enroll/enrollmag/logic_2",{
                type:'POST',
                data:{
                    med : 'pass',
                    id : id,
                    phone : phone,
                    sug : sug,
                    score : scr,
                },
                success:function (res) {
                    var resp = JSON.parse(res);
                    if(resp.Stat === 'OK') {
                        $('#f2f').modal('hide');
                        var line = $('#uid_' + id);
                        line.addClass('fadeout');
                        line.removeClass('active');
                        document.querySelector('#enroll_mag .datahead .chk').innerHTML -= 1;
                        setTimeout(function () {
                            line.remove();
                        }, 600);
                    }
                    new $.zui.Messager(resp.Message, {
                        type:resp.Stat === 'OK' ? 'success' : 'warning',
                        placement: 'bottom-left'
                    }).show();
                },
                error:function (res) {
                    dump(res)
                }
            });

        },

        turn: function (id,phone) {
            var party = document.querySelector('#f2f .turn_aim input:checked');
            var that = this;
            $.ajax(  SiteUrl + "/enroll/enrollmag/logic_2",{
                type:'POST',
                data:{
                    med : 'turn',
                    id : id,
                    phone : phone,
                    party : party.value,
                    ftime : that.token
                },
                success:function (res) {
                    var resp = JSON.parse(res);
                    if(resp.Stat === 'OK'){
                        $('#f2f').modal('hide');
                        var line = $('#uid_'+id);
                        line.addClass('fadeout');
                        line.removeClass('active');
                        document.querySelector('#enroll_mag .datahead .chk').innerHTML -= 1;
                        setTimeout(function () {
                            line.remove();
                        },600);

                    }
                    new $.zui.Messager(resp.Message, {
                        type:resp.Stat === 'OK' ? 'success' : 'warning',
                        placement: 'bottom-left'
                    }).show();
                },
                error:function (res) {
                    dump(res)
                }
            });
        },

        absence: function (id,phone) {
            $.ajax(SiteUrl + "/enroll/enrollmag/logic_2",{
                type:'POST',
                data:{
                    med : 'absence',
                    id : id,
                    phone : phone
                },
                success:function (res) {
                    var resp = JSON.parse(res);
                    if(resp.Stat === 'OK'){
                        $('#f2f').modal('hide');
                        var line = $('#uid_'+id);
                        line.addClass('fadeout');
                        line.removeClass('active');
                        document.querySelector('#enroll_mag .datahead .chk').innerHTML -= 1;
                        setTimeout(function () {
                            line.remove();
                        },600);

                    }
                    new $.zui.Messager(resp.Message, {
                        type:resp.Stat === 'OK' ? 'success' : 'warning',
                        placement: 'bottom-left'
                    }).show();
                },
                error:function (res) {
                    dump(res)
                }
            });
        }
    },

    enroll : function (obj) {
        this.data = [];
        this.list = [];
        this.vol = 0;
        var k = 0;
        var ctn = document.querySelector(obj),
            submit = ctn.querySelector(".submit"),
            lis = ctn.querySelectorAll('.datalist li');

        for (var i = 0; i < lis.length; i++) {
            if (lis[i].querySelector('input').checked) {
                this.data[k] = {
                    'med': 'enroll',
                    'id' : lis[i].querySelector('input').id,
                    'phone' : lis[i].querySelector('.phone').innerHTML
                };
                this.list[k] = lis[i].id;
                ++k;
            }
        }
        if(k !== 0 ){
            submit.innerHTML = '...';
            submit.onclick = function(){};
            this.enrollFuc(obj);
        }
    },

    enrollFuc : function (obj) {
        var that = this;

        var ctn = document.querySelector(obj),
            logicURL = ctn.getAttribute('data-url'),
            submit = ctn.querySelector(".submit");

        $.ajax({
            url:  SiteUrl + "/enroll/enrollmag/" + logicURL,
            type: 'POST',
            data: that.data[that.vol],
            success: function (res) {
                rsp = JSON.parse(res);
                if(rsp.Stat === 'OK'){
                    ctn.querySelector('#' + that.list[that.vol] + ' .stat').innerHTML = '<i class="text-success">'+ rsp.Message +'</i>';
                } else {
                    ctn.querySelector('#' + that.list[that.vol] + ' .stat').innerHTML = '<i class="text-danger">'+ rsp.Message +'</i>';
                }
                ++that.vol;
                if (that.vol < that.data.length) {
                    that.enrollFuc(obj);
                } else {
                    submit.innerHTML = '刷 新';
                    submit.onclick = function(){Sms.init(obj)};
                }
            },
            error: function () {
                ctn.querySelector('#' + that.list[that.vol] + ' .stat').innerHTML = '<i class="text-danger">无网络或找不到服务器</i>';
                ++that.vol;
                if (that.vol < that.data.length) {
                    that.obtainId(obj);
                } else {
                    submit.innerHTML = '刷 新';
                    submit.onclick = function(){Sms.init(obj)};
                }
            }
        })
    },

    unenroll : function () {
        var target = getEventobj().parentNode;
        var id = target.querySelector('.chk input').id;
        var phone = target.querySelector('.phone').innerHTML;

        foo  = getEventobj();
        foo.className = 'back disabled';
        $.ajax(SiteUrl + "/enroll/enrollmag/logic_4",{
            type: 'POST',
            data: {
                'med': 'unenroll',
                'id': id,
                'phone': phone
            },
            success: function (res) {
                var data = JSON.parse(res);
                if(data.Stat === 'OK'){
                    target.className = 'item fadeout';
                    setTimeout(function () {
                        target.parentNode.removeChild(target);
                    },700);
                    target.parentNode.parentNode.parentNode.parentNode.querySelector('.datahead li .chk').innerHTML -= 1;
                }
                setTimeout(function () {
                    foo.className = 'back';
                },600);
                new $.zui.Messager(data.Message, {
                    type:data.Stat === 'OK' ? 'success' : 'warning',
                    placement: 'bottom-left'
                }).show();
            },
            error: function (res) {
                foo.className = 'back';
                dump(res);
            }
        })
    },
};