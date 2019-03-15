window.addEventListener('DOMContentLoaded', function () {
    let avatar = document.getElementById('avatar'),
        image = document.getElementById('image'),
        input = document.getElementById('input'),

        $alert = $('.alert'),
        $modal = $('#modal'),
        cropper,
        canvas;

    /* ImageUpload init */
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

    /* Image crop init */
    $modal.on('shown.bs.modal', function () {
        cropper = new Cropper(image, {
            aspectRatio: 3 / 4,
            viewMode: 3,
        });
    }).on('hidden.bs.modal', function () {
        cropper.destroy();
        cropper = null;
    });


    /* Party List init */
    loadparty();
    function loadparty(){
        loading();
        $.ajax(ApiUrl + '/api/getpartys', {
            method: 'GET',
            data: '',
            success: function (res) {
                loading('stop');
                loadcampus();
                let partys = JSON.parse(res);
                let html = '<option disabled selected value="0">请选择部门</option>';
                for (i in partys){
                    html += '<option value="'+ i +'">'+partys[i]+'</option>';
                }
                let aim = document.querySelectorAll('[datatype="aim"]');
                for (x in aim){
                    aim[x].innerHTML = html;
                }
            },
            error: function () {
                var res = {};
                res.Message = '页面加载失败,即将重新加载';
                alert(res);
                setTimeout(function () {
                    $('#alert').modal('hide');
                    $('.modal-backdrop').remove();
                },3000);
                setTimeout(loadparty,4000);
            }
        });
    }

    /* Campus List init */
    function loadcampus(){
        loading();
        $.ajax(ApiUrl + '/api/getcampus', {
            method: 'GET',
            data: '',
            success: function (res) {
                loading('stop');
                let partys = JSON.parse(res);
                let html = '<option disabled selected value="0">请选择校区</option>';
                for (i in partys){
                    html += '<option value="'+ i +'">'+partys[i]+'</option>';
                }
                let aim = document.querySelectorAll('[datatype="campus"]');
                for (x in aim){
                    aim[x].innerHTML = html;
                }
            },
            error: function () {
                var res = {};
                res.Message = '页面加载失败,即将重新加载';
                alert(res);
                setTimeout(function () {
                    $('#alert').modal('hide');
                    $('.modal-backdrop').remove();
                },3000);
                setTimeout(loadcampus,4000);
            }
        });
    }


    /* Croper Worker */
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
    document.getElementById('poster').addEventListener('click',poston);
    function poston() {
        let Data = new FormData(document.querySelector('form'));
        if (canvas) {
            canvas.toBlob(function (blob) {
                Data.append('avatar', blob, 'avatar.jpg');
                post(Data);
            });
        } else {
            post(Data);
        }
    }

    /* 数据提交方法 */
    function post(Data) {
        $('#poster').attr("disabled",true);
        document.getElementById('poster').removeEventListener('click',poston);
        loading();
        $.ajax(ApiUrl + '/api/usenroll', {
            method: 'POST',
            data: Data,
            processData: false,
            contentType: false,
            success: function (res) {
                document.getElementById('poster').addEventListener('click',poston);
                $('#poster').attr("disabled",false);
                loading('stop');
                let resp = JSON.parse(res);
                if(resp === 'OK'){
                    document.querySelector('form').reset();
                }
                alert(resp)
            },
            error: function (res) {
                //document.querySelector('body').innerHTML = res.responseText;  //用于调试
                document.getElementById('poster').addEventListener('click',poston);
                $('#poster').attr("disabled",false);
                loading('stop');
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
    /* 返回消息提示方法 */
    function loading(res) {
        var loader = $('.loading');
        if(!res){
            loader.addClass('on')
        } else {
            loader.removeClass('on')
        }
    }

});