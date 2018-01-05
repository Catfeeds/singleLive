$(function() {
    /**
     * [订单提交方法]
     * @Author   尹新斌
     * @DateTime 2017-08-01
     * @Function []
     * @param    {[type]}   ) {                       $(this).attr('disabled', 'disabled');            var self [description]
     * @return   {[type]}     [description]
     */


    $('body').on('touchend', '.ajax-get', function() {
        $(this).attr('disabled', 'true');
        var self = $(this);
        var target;
        if ($(this).hasClass('confirm')) { //判断是否需要确认
            var nead_confirm = true;
        } else {
            var nead_confirm = false;
        }
        var msg = '确定执行该操作吗？';
        msg = $(this).attr('data-msg') ? ($(this).attr('data-msg')) : msg;
        if ((target = $(this).attr('href')) || (target = $(this).attr('url'))) {
            if (nead_confirm) {
                layer.open({
                    content: msg,
                    btn: ['确定', '取消'],
                    skin: 'footer',
                    yes: function(index) {
                        $.get(target, success, "json");
                    }
                });
            } else {
                $.get(target, success, "json");
            }
        }
        return false;
    });
    $('body').on('touchend', '.ajax-post', function() {
        $(this).attr('disabled', 'disabled');
        var self = $(this);
        var data; //提交数据
        var target_form = $(this).attr('target-form');
        var file_url = $(this).attr('file-url');
        if (file_url) {
            doUpload(file_url, target_form); //上传组件执行操作
        }
        var msg = '确定执行该操作吗？';
        msg = $(this).attr('data-msg') ? ($(this).attr('data-msg')) : msg;
        if ($(this).hasClass('confirm')) { //判断是否需要确认
            var nead_confirm = true;
        } else {
            var nead_confirm = false;
        }
        if ($(this).hasClass('validate')) { //判断是否需要执行验证
            var nead_validate = true;
        } else {
            var nead_validate = false;
        }
        var flag = true;
        var target = ($(this).attr('href')) || ($(this).attr('url'));
        if (($(this).attr('type') == 'submit') || target) {
            var form = $(this).parents('.' + target_form);

            if (form.get(0) == undefined) {
                return false;
            } else if (form.get(0).nodeName == 'FORM') {
                if ($(this).attr('url') !== undefined) {
                    target = $(this).attr('url');
                } else {
                    target = form.get(0).action;
                }
                data = form.serialize();
            } else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {
                data = form.serialize();
            } else {
                data = form.find('input,select,textarea').serialize();
            }
            if (nead_validate) {
                var validate = form.validate().form();
            }
            if (validate !== false) {
                if (nead_confirm) {
                    layer.open({
                        content: msg,
                        btn: ['确定', '取消'],
                        skin: 'footer',
                        yes: function(index) {
                            $.post(target, data, success, "json");
                        }
                    });
                    self.attr('disabled', false);
                } else {
                    $.post(target, data, success, "json");
                    self.attr('disabled', false);
                }
            }
        }
        return false;
    });

    function success(data) {
        if (data.status) {
            layer.open({
                content: data.info,
                skin: 'msg',
                time: data.time //2秒后自动关闭
            });
            setTimeout(function() {
                window.location.href = data.url
            }, data.time * 1000);
            return false;
        } else {
            layer.open({
                content: data.info,
                skin: 'msg',
                time: data.time //2秒后自动关闭
            });
            return false;
        }
    }




    /*渲染页面*/
    // function viewHtml() {
    //  alert(1);
    // }











});


function timeCheck(sum, dom) {
    // var min = dom.attr('tmin'),tplus = dom.attr('tplus');
    var interval, reg = /^\d$/,
        sleep = 1000; //间隔时间
    if (!interval) {
        interval = setInterval(function() {
            sum++;
            var h = parseInt(sum / 3600);
            var m = parseInt((sum % 3600) / 60);
            var s = parseInt((sum % 3600) % 60);
            if (h < 10) {
                h = '0' + h;
            }
            if (m < 10) {
                m = '0' + m;
            }
            if (s < 10) {
                s = '0' + s;
            }
            dom.text(h + ':' + m + ':' + s);
        }, sleep);
    } else {
        clearInterval(interval);
        interval = null;
    }
}

function layerMsg(info) {
    layer.open({
        content: info,
        skin: 'msg',
        time: 2 //2秒后自动关闭
    });
}