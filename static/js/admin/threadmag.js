/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2018-08-05
 *
 */

var Threadtable = {
    link: SiteUrl +'/consoleboard/threadmag/gettreaddata',

    data: [],

    Tryget : function (type) {

        var table;
        switch (type) {
            case 'thread':table = '#thread_table';break;
            case 'trash': table = '#trash_table'; break;
        }

        var tablee = $(table).data('zui.datagrid');
        tablee.renderLoading(1);

        var that = this;
        $.ajax({
            url : this.link,
            type:'POST',
            data:{'type' : type},
            success:function (res) {
                var data = JSON.parse(res);
                that.data = data.Data;
                that.renew(table);
                tablee.renderLoading(0);
                setTimeout(
                    function () {
                        new $.zui.Messager(data.Message, {
                            icon: 'bell',
                            type: data.Stat === 'OK' ? 'success' : 'danger',
                            placement: 'bottom-left',
                        }).show();
                    },300
                );
            },
            error: function () {
                tablee.renderLoading(0);
                setTimeout(
                    function () {
                        new $.zui.Messager('加载失败，请重试', {
                            icon: 'bell',
                            type: 'danger',
                            placement: 'bottom-left',
                        }).show();
                    },300
                );
            }
        })
    },

    renew : function (e) {
        var elm = $(e).data('zui.datagrid');
        elm.setDataSource(this.data);
        elm.render();
    }
};

var Threadmag = {
    link: SiteUrl +'/consoleboard/threadmag/logic.html',

    setstat : function (med,val) {
        var eventobj = getEventobj();
        $.ajax({
            url : this.link,
            type:'POST',
            data:{
                'med' : med,
                'tid' : val
            },
            success:function (res) {
                data = JSON.parse(res);
                if(data.Stat === 'OK'){
                    if(med === 'dpushthread'){
                        eventobj.className = 'threadfuncicon icon-eye-close text-danger';
                        eventobj.onclick = function () {
                            Threadmag.setstat('pushthread', val);
                        };
                    } else if(med === 'pushthread'){
                        eventobj.className = 'threadfuncicon icon-eye-open text-success';
                        eventobj.onclick = function () {
                            Threadmag.setstat('dpushthread', val);
                        };
                    }

                    if(med === 'delthread' || med === 'recoverthread' || med === 'trashthread'){

                        var cell = eventobj.parentNode.parentNode;
                        var row = cell.parentNode;
                        var id = cell.parentNode.querySelector('.datagrid-cell-index').innerHTML;
                        var table = row.parentNode.parentNode.parentNode.id;

                        /* 更新数据源，刷新表格 */
                        Threadtable.data.splice(id - 1,1);
                        Threadtable.renew('#' + table);
                    }
                }

                new $.zui.Messager(data.Message, {
                    icon: 'bell',
                    type: data.Stat === 'OK' ? 'success' : 'danger',
                    placement: 'bottom-left',
                }).show();
            },
        })
    }
};