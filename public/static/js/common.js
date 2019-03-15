function dump(res) {
    /* Search-Danger : 该语句用于测试，投入使用时请将其删除！ */
    document.querySelector('body').innerHTML = res.responseText;
    /* Search-Danger*/
    new $.zui.Messager('无网络或找不到服务器！', {
        icon: 'bell',
        type: 'danger',
        placement: 'bottom-left',
    }).show();
}

function getEvent() {
    if(document.all) return window.event;
    func = getEvent.caller;
    while(func !== null) {
        var arg = func.arguments[0];
        if (arg) {
            if((arg.constructor  === Event || arg.constructor === MouseEvent) || (typeof(arg) === "object" && arg.preventDefault && arg.stopPropagation)) {
                return arg;
            }
        }
        func=func.caller;
    }
    return null;
}

function getEventobj() {
    return getEvent().srcElement || getEvent().target;
}