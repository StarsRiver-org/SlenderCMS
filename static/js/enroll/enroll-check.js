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
