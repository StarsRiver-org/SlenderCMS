window.addEventListener('DOMContentLoaded', function () {
    quseList = document.querySelector('#qlist');
    form = document.querySelector('form');
    end = document.querySelector('#end');
    nothing = document.querySelector('#nothing');
    more = document.querySelector('#more');
    sK = document.querySelector('[name="key"]');
    sC = document.querySelector('[name="course"]');
    sY = document.querySelector('[name="year"]');
    sP = document.querySelector('[name="chapter"]');
    sT = document.querySelector('[name="type"]');
    page = 0;

    datalength = 15;

    /* 按钮切换 */
    function state(st) {
        switch (st) {
            case 'more' :
                more.className = '';
                end.className = 'hide';
                nothing.className = 'hide';

                $(document).off('scroll');
                setTimeout(function () {
                    $(document).scroll(function() {
                        if($(document).height() ===  $(document).scrollTop() + window.innerHeight) {
                            post();
                        }
                    });
                },datalength);
                break;
            case 'end' :
                more.className = 'hide';
                end.className = '';
                nothing.className = 'hide';
                $(document).off('scroll');
                break;
            case 'nothing' :
                more.className = 'hide';
                end.className = 'hide';
                nothing.className = '';
                $(document).off('scroll');
                break;
            case 'error' :
                more.className = 'hide';
                end.className = 'hide';
                nothing.className = 'hide';
                $(document).off('scroll');
                break;
        }
    }

    /* 简单的内容包含检查 */
    function in_array(v,arr) {for (let i = 0 ; i < arr.length; i++){if(arr[i] === v){return 1;}}}

    /* 改变选框 */
    function makeSelect(e,data,df,all){
        e.last = in_array($(e).val(), data) ? $(e).val() : 0;
        let html = '<option '+( all ? '' : 'disabled')+' value="0" '+ (e.last === 0 ? 'selected' : '')  +'>'+ df +'</option>';
        for (i in data){html += '<option value="'+ data[i] +'" '+ (e.last === data[i] ? 'selected' : '')  +'>'+data[i]+'</option>';}
        e.innerHTML = html;
    }

    /* 生成题目 */
    function makeQust(data){
        if (data.ques) {
            console.log(sK.value);
            sK.value ? data.ques = data.ques.replace(sK.value, '<i>' + sK.value + '</i>') : '';
            let ques = '<p class="ques">' + data.ques + '</p>';
            let note = data.note ? '<p class="note">' + data.note + '</p>' : '';
            let choss = '<div class="ans">';
            switch (data.type) {
                case '多选题':
                case '单选题':
                    for(i in data.chos){
                        if(data.chos[i]){choss += '<div class="select '+ (in_array(i,data.ans) ? 'active' : '') +'"><em>'+i+' . </em><span>'+data.chos[i]+'</span></div><br />';}
                    }
                    break;
                case '判断题':
                    for(i in data.chos){
                        if(data.chos[i]){choss += '<div class="judge '+ (in_array(i,data.ans) ? 'active' : '') +'"><em>'+i+' . </em><span>'+data.chos[i]+'</span></div>';}
                    }
                    break;
                case '填空题':
                    for(i in data.chos){
                        if(data.chos[i]){choss += '<span class="fill">'+data.chos[i]+'</span>';}
                    }
                    break;
                case '简答题':
                    for(i in data.chos){
                        if(data.chos[i]){choss += '<p class="ask">'+data.chos[i]+'</p>';}
                    }
                    break;

            }
            choss += '</div>';
            return '<div class="qi">' + ques + note + choss + '</div>';
        }
        return null;
    }

    /* 初始化信息 */
    ot = 0; //刷新超时次数
    function init(){
        loading();
        $.ajax(ApiUrl + '/api/quesbank', {
            method: 'post',
            data: new FormData(form),
            processData: false,
            contentType: false,
            success: function (res) {
                let data = JSON.parse(res).Data;
                makeSelect(sC,data.courses,'请选择课程');
                makeSelect(sY,data.years,'请选择年度');
                makeSelect(sP,data.chapters,'全部章节',1);
                makeSelect(sT,data.type,'全部题型',1);
                sY.addEventListener('change',post);
                sC.addEventListener('change',post);
                sP.addEventListener('change',post);
                sT.addEventListener('change',post);
                sK.addEventListener('change',post);
                more.addEventListener('click',post);
                loading('stop');
            },
            error: function () {
                if (ot < 5) {
                    ot++;
                    setTimeout(init, 1000);
                } else {
                    loading('fail');
                }
            }
        });
    }

    /* 保存上一次请求参数*/
    sKlv = ''; //key
    sClv = ''; //course
    sYlv = ''; //year
    sPlv = ''; //chapter
    sTlv = ''; //question type

    /* 请求数据 */
    function post() {
        if(!(sKlv === sK.value && sClv === $(sC).val() && sYlv === $(sY).val() && sPlv === $(sP).val() && sTlv === $(sT).val())) {
            page = 0;
            sKlv = sK.value;
            sClv = $(sC).val();
            sYlv = $(sY).val();
            sPlv = $(sP).val();
            sTlv = $(sT).val();
        }
        loading();
        PData = new FormData(form);
        PData.append('page', page);
        $.ajax(ApiUrl + '/api/quesbank', {
            method: 'POST',
            data: PData,
            processData: false,
            contentType: false,
            success: function (res) {
                let data = JSON.parse(res).Data;
                makeSelect(sC,data.courses,'请选择课程');
                makeSelect(sY,data.years,'请选择年度');
                makeSelect(sP,data.chapters,'全部章节',1);
                makeSelect(sT,data.type,'全部题型',1);
                if(data.ques.length > 0){
                    if(page === 0){quseList.innerHTML = '';}
                    for(item in data.ques){quseList.innerHTML += makeQust(data.ques[item])}
                    page++;
                    if(data.ques.length < datalength){state('end');} else {state('more');}
                } else {
                    if(page === 0 ){state('nothing');} else {state('end');}
                }
                loading('stop');
            },
            error: function () {
                loading('fail');
            },
        });
    }

    /* 返回提示 */
    function loading(res) {
        let loader = $('.loading');
        let fail =  $('#fail');
        if(!res){
            loader.addClass('on');
            fail.addClass('hide');
            fail.click();
        } else {
            loader.removeClass('on');
            if(res === 'fail'){
                fail.removeClass('hide');
                fail.click(function(){init();});
            }
        }
    }
    init();
});