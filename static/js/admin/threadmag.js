/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2018-08-05
 *
 */
var Threadmag = {
    setstat : function (med,val) {
        var eventobj = getEventobj();
        $.ajax({
            url : SiteUrl +'/consoleboard/threadmag/logic.html',
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
                        eventobj.parentNode.style.opacity = .3;
                        eventobj.parentNode.style.pointerEvents = 'none';
                    }
                }

                new $.zui.Messager(data.Message, {
                    icon: 'bell',
                    type: data.Stat === 'OK' ? 'success' : 'danger',
                    placement: 'bottom-left',
                }).show();
            },
            error:function (res) {
                dump(res)
            }
        })
    }
};