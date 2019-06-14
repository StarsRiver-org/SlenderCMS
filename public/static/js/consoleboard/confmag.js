/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2018-03-21
 *
 */

var Confio = {

    creatlist: function(arr){
        var html = '<ul class="conf-list">';
        for (x in arr){

            arr[x].data = arr[x].data ? arr[x].data : '';

            var datalen = arr[x].data ? arr[x].data.replace(/[^\u0000-\u00ff]/g,"aa").length : 0;
            var dataser = '';
            if(arr[x].data === 'on' || arr[x].data === 'off' ){
                dataser = '<p><input id="conf_'+ arr[x].name +'" data-type="" type="checkbox" class="hidden" '+ (arr[x].data === 'on' ? 'checked ' : '') + (arr[x].issolid ? 'disabled' : '') +'><label for="conf_'+ arr[x].name + '">OFF</label></p>';
            } else if(datalen <= 36){
                dataser = arr[x].issolid ? '<p class="input-modal">'+ arr[x].data + '</p>' : '<p><input value="'+ arr[x].data + '"/></p>';
            } else {
                dataser = arr[x].issolid ? '<p class="input-modal">' + arr[x].data + '</p>' : '<p><textarea>' + arr[x].data + '</textarea></p>';
            }

            var li = '<li class="' +  (arr[x].issolid ? 'solid' : '') + '">' +
                '<i class="name">'+ arr[x].name +'</i>' +
                '<i class="discrib">'+ arr[x].descrip +'</i>' +
                '<i class="data">' + dataser +'</i>' +
                (!arr[x].issolid ? '<i class="reset icon-undo"></i>' : '') +
                '</li>';

            html += li;
        }
        return html + '</ul>';
    },

    initdata : function (obj) {
        var target = document.querySelector(obj);
        var that = this;
        $.ajax({
            url: SiteUrl + '/consoleboard/configmag/logic.html',
            type:'POST',
            data:{
                'getconf': target.getAttribute('data-type'),
            },
            success: function (res) {
                var data = JSON.parse(res);
                if(data.Stat === 'OK'){
                    target.innerHTML = that.creatlist(data.Data);
                    var resb = target.querySelectorAll('.reset');
                    for(i in resb){resb[i].onclick = function () {that.renewconf();}}

                    var tar = target.querySelectorAll('textarea');
                    for(i in tar){tar[i].onblur = function () {that.saveconf();}}

                    var ins = target.querySelectorAll('input');
                    for(i in ins){
                        if(ins[i].type === 'checkbox'){
                            ins[i].onclick = function () {that.saveconf();}
                        } else {
                            ins[i].onblur = function () {that.saveconf();}
                        }
                    }

                } else {
                    new $.zui.Messager(data.Message, {
                        icon: 'bell',
                        type: 'danger',
                        placement: 'bottom-left',
                    }).show();
                }
            },
            error :function (res) {
                dump(res);
            }
        });
    },

    renewconf : function () {
        var target = getEventobj().parentNode;
        $.ajax({
            url: SiteUrl + '/consoleboard/configmag/logic.html',
            type:'POST',
            data:{
                'resetconf' : 1,
                'data': JSON.stringify({'name': target.querySelector('.name').innerHTML})
            },
            success: function (res) {
                var data = JSON.parse(res);
                if(data.Stat === 'OK'){
                    /*处理返回值，更新被影响的视图*/
                    var opt = target.querySelector('.data input');
                    if(opt){
                        if(opt.type === 'checkbox'){
                            opt.checked = data.Data === 'on' ? 'checked' : '';
                        } else {
                            opt.value = data.Data;
                        }
                    } else {
                        opt = target.querySelector('.data textarea');
                        opt.value = data.Data;
                    }
                }
                new $.zui.Messager(data.Message, {
                    icon: 'bell',
                    type: data.Stat === 'OK' ? 'success' : 'danger',
                    placement: 'bottom-left',
                }).show();
            },
            error :function (res) {
                dump(res);
            }
        });
    },

    saveconf : function () {
        var opt = getEventobj();
        var target = opt.parentNode.parentNode.parentNode;
        var value = '';

        if(opt.type === 'checkbox'){
            value = opt.checked ? 'on' : 'off';
        } else {
            value = opt.value;
        }

        $.ajax({
            url: SiteUrl + '/consoleboard/configmag/logic.html',
            type:'POST',
            data:{
                'saveconf' : 1,
                'data': JSON.stringify({
                    'name': target.querySelector('.name').innerHTML,
                    'data': value
                })
            },
            success: function (res) {
                if(res){
                    var data = JSON.parse(res);
                    new $.zui.Messager(data.Message, {
                        icon: 'bell',
                        type: data.Stat === 'OK' ? 'success' : 'danger',
                        placement: 'bottom-left',
                    }).show();
                }
            },
            error :function (res) {
                dump(res);
            }
        });
    }
};