/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2018-09-12
 *
 */
//面试查询
jQuery(document).ready(function() {
	$('.contact-form form').submit(function(e) {
		e.preventDefault();
	    $('.contact-form form input[type="text"], .contact-form form textarea').removeClass('input-error');
        $.ajax({
            url: SiteUrl + "/enroll/enroll/check",
            type: 'POST',
            processData: false,
            contentType: false,
            cache: false,
            data: new FormData(document.querySelector('.contact-form form')),
            success: function (res) {
                var ress = JSON.parse(res);
                showdilog(ress.Message);
            },
            error: function () {
                showdilog('网络错误，请刷新后再试');
            },
        })
	});

    function showdilog(str) {
        document.querySelector('#ctr .modal-body').innerHTML = str;
        $('#ctr').modal('show');
    }

});
