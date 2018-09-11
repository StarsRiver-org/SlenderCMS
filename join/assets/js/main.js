window.addEventListener('DOMContentLoaded', function () {
    let avatar = document.getElementById('avatar'),
        image = document.getElementById('image'),
        input = document.getElementById('input'),

        $alert = $('.alert'),
        $modal = $('#modal'),
        cropper,
        canvas;

    /* 初始化图片上传功能 */
    input.addEventListener('change', function (e) {
        let files = e.target.files;
        let done = function (url) {
            input.value = '';
            image.src = url;
            $alert.hide();
            $modal.modal('show');
        };
        let reader;
        let file;

        if (files && files.length > 0) {
            file = files[0];
            if (URL) {
                done(URL.createObjectURL(file));
            } else if (FileReader) {
                reader = new FileReader();
                reader.onload = function (e) {
                    done(reader.result);
                };
                reader.readAsDataURL(file);
            }
        }
    });

    /* 初始化控制弹窗 */
    $modal.on('shown.bs.modal', function () {
        cropper = new Cropper(image, {
            aspectRatio: 3 / 4,
            viewMode: 3,
        });
    }).on('hidden.bs.modal', function () {
        cropper.destroy();
        cropper = null;
    });


    /* 初始化表单中部门信息 */
    $.ajax(ApiUrl + '/api/getpartys', {
        method: 'GET',
        data: '',
        success: function (res) {
            let partys = JSON.parse(res);
            console.log(partys);
            let html = '<option disabled selected>请选择部门</option>';
            for (i in partys){
                html += '<option value="'+ i +'">'+partys[i]+'</option>';
            }
            let aim = document.querySelectorAll('[datatype="aim"]');
            for (x in aim){
                aim[x].innerHTML = html;
            }
        },
    });

    /* 裁剪头像行为 */
    document.getElementById('crop').addEventListener('click', function () {
        $modal.modal('hide');
        if (cropper) {
            canvas = cropper.getCroppedCanvas({
                width: 240,
                height: 320,
            });
            initialAvatarURL = avatar.src;
            avatar.src = canvas.toDataURL();
            $alert.removeClass('alert-success alert-warning');
        }
    });

    /* 数据提交行为 */
    document.getElementById('poster').addEventListener('click', function () {
        let Data = new FormData(document.querySelector('form'));
        if(canvas){
            canvas.toBlob(function (blob) {
                Data.append('avatar', blob, 'avatar.jpg');
                post(Data);
            });
        } else {
            post(Data);
        }
    });

    /* 数据提交方法 */
    function post(Data) {
        $.ajax(ApiUrl + '/api/usenroll', {
            method: 'POST',
            data: Data,
            processData: false,
            contentType: false,
            success: function (res) {
                let resp = JSON.parse(res);
                if(resp === 'OK'){
                    document.querySelector('form').reset();
                }
                alert(resp)
            },
            error: function (res) {
                //document.querySelector('body').innerHTML = res.responseText;  //用于调试
                let resp = {
                    Stat:'error',
                    Message:'网络错误或找不到服务器',
                };
                alert(resp)
            },
        });
    }

    /* 返回消息提示方法 */
    function alert(res) {
        document.querySelector('#alert .modal-body').innerHTML = res.Message;
        $('#alert').modal('show');
    }

});