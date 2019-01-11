/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2018-08-05
 *
 */
partys = '';
$.ajaxSettings.async = false;
$.get(SiteUrl + '/api/getpartys',function(result){partys = JSON.parse(result)});
$.ajaxSettings.async = true;

var Pmlmag = {
    /* 初始化用户细分权限表 */
    inituserpml: function (idbody, obj) {
        var uid = idbody.getAttribute('data-upid'),
            target = document.querySelector(obj),
            user_pml = target.querySelector('#user-pms .user-pm'),
            admin_pml = target.querySelector('#user-pms .admin-pm');

        target.setAttribute('data-upid', uid);
        user_pml.innerHTML = '';
        admin_pml.innerHTML = '';

        $.ajax({
            url: SiteUrl + '/consoleboard/usermag/logic',
            type: 'POST',
            data: {
                'getuserpml': 1,
                'uid': uid,
            },
            success: function (res) {
                var retu = JSON.parse(res);
                if (retu.Stat === 'OK') {
                    var pd = JSON.parse(retu.Data);
                    var html = '';
                    for (key in pd) {
                        if (pd[key].type === 'boolean') {
                            html = '<i class="item"><input type="checkbox" id="' + key + '" data-sort="' + pd[key].sort + '" ' + (pd[key].value ? 'checked ' : '') + (pd[key].solid ? 'disabled ' : '') + '><label for="' + key + '"><span>' + pd[key].name + '</span></label></i>'
                        } else if (pd[key].type === 'char') {
                            html = '<i class="item"><label for="' + key + '"><span>' + pd[key].name + '</span><input type="text" id="' + key + '" data-sort="' + pd[key].sort + '" value="' + pd[key].value + '"' + (pd[key].solid ? 'disabled' : '') + '></label></i>'
                        }
                        if (pd[key].sort === 'user') {
                            user_pml.innerHTML += html;
                        } else if (pd[key].sort === 'admin') {
                            admin_pml.innerHTML += html;
                        }
                    }
                    $(obj).modal('show')
                } else {
                    new $.zui.Messager(retu.Message, {
                        icon: 'bell',
                        type: 'danger',
                        placement: 'bottom-left',
                    }).show();
                }
            },
            error: function (res) {
                dump(res);
            },
        });
    },

    /* 保存用户细分权限 */
    saveuserpml: function (obj) {
        var target = target = document.querySelector(obj);
        var pml = {};
        var pmlist = document.querySelectorAll('#user-pms input');
        for (var i = 0; i < pmlist.length; i++) {
            var input_type = pmlist[i].type;
            var type = '';
            if (input_type === 'checkbox') {
                type = 'boolean'
            } else {
                type = 'char'
            }

            pml[pmlist[i].id] = {
                'value': type === 'boolean' ? pmlist[i].checked : pmlist[i].value,
            };
        }
        $.ajax({
            url: SiteUrl + '/consoleboard/usermag/logic',
            type: "POST",
            data: {
                'setuserpml': 1,
                'pml': JSON.stringify(pml),
                'uid': target.getAttribute('data-upid'),
            },
            success: function (status) {
                res = JSON.parse(status);
                if (res.Stat === 'OK') {
                    new $.zui.Messager(res.Message, {
                        icon: 'bell',
                        type: 'success',
                        placement: 'bottom-left',
                    }).show();
                    $(obj).modal('hide')
                } else {
                    new $.zui.Messager(res.Message, {
                        icon: 'bell',
                        type: 'danger',
                        placement: 'bottom-left',
                    }).show();
                }
            },
            error: function (res) {
                dump(res);
            },
        })

    },
};

var UserInfoMag = {

    /*初始化用户表单*/
    formHtml : function(ui){
        refreshtemp = ui ? (ui.promise >= 900 ? 1 : (ui.promise >= 800 ? 2 : (ui.promise >= 700 ? 3 : 4))): 4;
        var pid = ui ? ui.party : 0;
        partyselector = '<select name="party"><option value="0">请选择部门</option>';
        for (key in partys) {
            partyselector += '<option '+ (pid === eval(key) ? 'selected' : '') +' value="'+ key +'">'+ partys[key] +'</option>';
        }
        partyselector += '</select>';
        return '' +
            '<input type="hidden" name="' + (ui ? 'upuser' : 'adduser') + '" value="' + (ui ? ui.uid : '1') + '">' +
            '<input type="hidden" name="saveuserinfo" value="1">' +
            '<div><cite>用户名</cite><input type="text" name="username" value="' + (ui ? ui.username : '') + '" placeholder="用户名"></div>' +
            '<div><cite>真实姓名</cite><input type="text" name="realname" value="' + (ui ? ui.name : '') + '" placeholder="真实姓名"></div>' +
            '<div><cite>密码</cite><input type="number" onfocus="this.type=\'password\'"  name="password" placeholder="用户密码，八位以上。如无需修改密码则留空" autocomplete="off"></div>' +
            '<div><cite>邮箱</cite><input type="text" name="email" value="' + (ui ? ui.email : '') + '" placeholder="邮箱"></div>' +
            '<div><cite>手机号</cite><input type="text" name="phone" value="' + (ui ? ui.phone : '') + '" placeholder="手机号"></div>' +
            '<div>' +
            '    <cite>用户组</cite>' +
            '    <select  name="usergroup">' +
            '        <option ' + (parseInt((ui ? ui.promise : 0) / 100) === 1 ? 'selected' : '') + ' value="100">注册用户</option>' +
            '        <option ' + (parseInt((ui ? ui.promise : 0) / 100) === 7 ? 'selected' : '') + ' value="700">编辑</option>' +
            '        <option ' + (parseInt((ui ? ui.promise : 0) / 100) === 8 ? 'selected' : '') + ' value="800">管理员</option>' +
            '        <option ' + (parseInt((ui ? ui.promise : 0) / 100) === 9 ? 'selected' : '') + ' value="901">副站长</option>' +
            '        <option ' + ((ui ? ui.promise : 0) === 999 ? 'selected' : '') + ' value="999">站长</option>' +
            '    </select>' +
            '</div>' +
            '<div><cite>部门</cite>'+ partyselector +'</div>   ';

    },

    /*初始化用户信息*/
    inituserinfo: function (idbody, obj) {
        var that = this,
            uid = idbody.getAttribute('data-upid'),
            target = document.querySelector(obj),
            form = target.querySelector('.userinfoform'),
            btn_deluser = target.querySelector('.btn-deluser');
        /* The next two actions affected three element whitch set theirs 'data-upid' as the user id
            one is the modalbody, the others are buttons in .modal-footer
         */
        target.setAttribute('data-upid', uid);
        btn_deluser.setAttribute('data-upid', uid);
        form.innerHTML = '';
        $.ajax({
            url: SiteUrl + '/consoleboard/usermag/logic',
            type: "POST",
            catch:false,
            data: {
                getuserinfo: 1,
                uid: uid,
            },
            success: function (res) {
                var rep = JSON.parse(res)
                if (rep.Stat === 'OK') {
                    var data = JSON.parse(rep.Data);
                    form.innerHTML = that.formHtml(data);
                    $(obj).modal('show')
                } else {
                    new $.zui.Messager(rep.Message, {
                        icon: 'bell',
                        type: 'danger',
                        placement: 'bottom-left',
                    }).show();
                }
            },
            error: function (res) {
                dump(res);
            },
        })
    },

    /*保存用户信息表单*/
    saveuserinfo: function (obj) {
        var target = document.querySelector(obj),
            form = target.querySelector('.userinfoform');

        $.ajax({
            url: SiteUrl + '/consoleboard/usermag/logic',
            type: 'POST',
            processData: false,
            contentType: false,
            cache: false,
            data: new FormData(form),
            success: function (res) {
                rep = JSON.parse(res);
                if (rep.Stat === 'OK') {
                    new $.zui.Messager('保存成功', {
                        icon: 'bell',
                        type: 'success',
                        placement: 'bottom-left',
                    }).show();
                    PageInit.refrashnum();
                    PageInit.refreshud(refreshtemp);
                    $(obj).modal('hide');
                } else {
                    new $.zui.Messager(rep.Message, {
                        icon: 'bell',
                        type: 'danger',
                        placement: 'bottom-left',
                    }).show();
                }
            },
            error: function (res) {
                dump(res);
            },
        })
    },

    /*注册用户表单初始化*/
    reguser:function (obj) {
        var target = document.querySelector(obj),
            form = target.querySelector('.userinfoform');
        form.innerHTML = this.formHtml();
        $(obj).modal('show')
    },

    /*删除用户*/
    deluser: function (idbody) {
        var uid = idbody.getAttribute('data-upid');
        $.ajax({
            url: SiteUrl + '/consoleboard/usermag/logic',
            type: 'POST',
            data: {
                deluser: 1,
                uid: uid
            },
            success: function (res) {
                rep = JSON.parse(res);
                if (rep.Stat === 'OK') {
                    new $.zui.Messager('删除成功', {
                        icon: 'bell',
                        type: 'success',
                        placement: 'bottom-left',
                    }).show();
                    PageInit.refreshud(refreshtemp);
                    $('#user_info_setting').modal('hide')
                } else {
                    new $.zui.Messager(rep.Message, {
                        icon: 'bell',
                        type: 'danger',
                        placement: 'bottom-left',
                    }).show();
                }
            },
            error: function (res) {
                dump(res);
            },
        })
    },

    /*更改用户禁封状态*/
    userreg_switcher: function (idbody) {
        var uid = idbody.getAttribute('data-upid');
        $.ajax({
            url: SiteUrl + '/consoleboard/usermag/logic',
            type: 'POST',
            data: {
                setuserregstat: 1,
                uid: uid
            },
            success: function (res) {
                rep = JSON.parse(res);
                if(rep.Message === '用户解禁成功'){
                    idbody.className = 'btn btn-success l btn-setstat';
                    idbody.innerHTML = '禁';
                } else if(rep.Message === '用户禁用成功') {
                    idbody.className = 'btn btn-warning l btn-setstat';
                    idbody.innerHTML = '解';
                }
                new $.zui.Messager(rep.Message, {
                    icon: 'bell',
                    type: rep.Stat === 'OK' ? 'success' : 'danger',
                    placement: 'bottom-left',
                }).show();
            },
            error: function (res) {
                dump(res);
            },
        })
    }
};

var PageInit = {
    refrashnum : function () {
        document.querySelector('.loading').style.display = '';
        $.ajaxSettings.async = false;
        $.get(SiteUrl + '/consoleboard/usermag/get_usernum',function(result){
            var data = JSON.parse(result);
            document.querySelector('#usernum_reginday').innerHTML =  data.reg_inday;
            document.querySelector('#usernum_reginmon').innerHTML = data.reg_inmonth;
            document.querySelector('#usernum_creator').innerHTML =  data.num_creator;
            document.querySelector('#usernum_admin').innerHTML =  data.num_admin;
            document.querySelector('#usernum_editor').innerHTML =  data.num_editor;
            document.querySelector('#usernum_cuser').innerHTML =  data.num_cuser;
        });
        $.ajaxSettings.async = true;
        document.querySelector('.loading').style.display = 'none';
    },

    refreshud : function (type) {
        var html = '';
        document.querySelector('.loading').style.display = '';
        $.ajaxSettings.async = false;
        $.get(SiteUrl + '/consoleboard/usermag/renwud/' + type,function(result){
            var data = JSON.parse(result);
            html = '' +
                '<table class="table table-fixed table-bordered">' +
                '   <thead>' +
                '       <tr>' +
                '           <th align="center" width="37px">#</th>' +
                '           <th align="center" width="72px">信息</th>' +
                '           <th width="56px" align="center">ID</th>' +
                '           <th width="96px">用户名</th>' +
                '           <th width="64px">姓名</th>' +
                '           <th>所在部门</th>' +
                '           <th>电话</th>' +
                '           <th>邮箱</th>' +
                '           <th>最后登录</th>' +
                '       </tr>' +
                '   </thead>' +
                '   <tbody>';
            for(var i = 0; i < data.length; i++){
                var btn1 = '<button class="text ud icon-wrench" type="button" data-upid="'+ data[i].uid +'" onclick="UserInfoMag.inituserinfo(this,\'#user_info_setting\')" title="更新"></button>';
                var btn2 = '<button class="text ud icon-cogs" type="button" data-upid="'+ data[i].uid +'" onclick="Pmlmag.inituserpml(this,\'#user_pml_setting\');" title="权限"></button>';
                var btn3 = data[i].unreg === 1 ? '<button class="btn btn-warning l btn-setstat" type="button" data-upid="'+ data[i].uid +'" onclick="UserInfoMag.userreg_switcher(this)">解</button>' : '<button class="btn btn-success l btn-setstat" type="button" data-upid="'+ data[i].uid +'" onclick="UserInfoMag.userreg_switcher(this)">禁</button>';
                html += '' +
                    '<tr class="'+  (data[i].is ? 'highlight' : '') +'">' +
                    '   <td align="center">' + (parseInt(appm/10) > parseInt(data[i].promise/10) ? btn3 : '#') +'</td>' +
                    '   <td align="center">' + (parseInt(appm/10) > parseInt(data[i].promise/10) || data[i].is ? btn1 : '')  + (parseInt(appm/10) > parseInt(data[i].promise/10) ? btn2 : '') +'</td>' +
                    '   <td align="center">'+ data[i].uid +'</td>' +
                    '   <td>'+ data[i].username +'</td>' +
                    '   <td>'+ data[i].name +'</td>' +
                    '   <td>'+ partys[data[i].party] +'</td>' +
                    '   <td>'+ data[i].phone +'</td>' +
                    '   <td>'+ data[i].email +'</td>' +
                    '   <td>'+ data[i].lastlogin +'</td>' +
                    '</tr>';
            }
        });
        $.ajaxSettings.async = true;
        html += '</tbody></table>';
        document.querySelector('#ud_list_' + type).innerHTML = html;
        document.querySelector('.loading').style.display = 'none';
    }
}