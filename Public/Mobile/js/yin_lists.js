var yin = {
    token: '',
    msg: function(msg) {
        layer.open({
            content: msg,
            skin: 'msg',
            time: 2 //2秒后自动关闭
        });
    },
    lists: function(json) {
        var json = json ? json : {};
        var view = json.view ? json.view : '#view',
            url = json.url ? json.url : $(view).attr('url'),
            layout = json.layout ? json.layout : '#layout'; //设置输出页面的位置
        layui.use(['laytpl', 'flow', 'util'], function() {
            var util = layui.util,
                laytpl = layui.laytpl,
                flow = layui.flow; //流加载
            if ($(view).length > 0) {
                /*流加载页面*/
                flow.load({
                    elem: view, //指定列表容器
                    isAuto: true,
                    done: function(page, next) { //到达临界点（默认滚动触发），触发下一页
                        var lis = [];
                        var getTpl = $(layout).html();
                        //以jQuery的Ajax请求为例，请求下一页数据（注意：page是从2开始返回）
                        $.get(url + '/p/' + page, function(res) {
                            if ($("input[name='_token']").length > 0) {
                                $("input[name='_token']").val(res.token);
                            }
                            $('#metaToken').attr('content', res.token);
                            yin.token = res.token;
                            //假设你的列表返回在data集合中
                            layui.each(res.db, function(index, item) {
                                laytpl(getTpl).render(item, function(html) {
                                    lis.push(html);
                                });
                            });
                            //执行下一页渲染，第二参数为：满足“加载更多”的条件，即后面仍有分页
                            //pages为Ajax返回的总页数，只有当前页小于总页数的情况下，才会继续出现加载更多
                            next('<span id="page' + page + '">' + lis.join('') + '</span>', page < res.page);
                            if ($('#page' + page).find('.timeCheck').length > 0) {
                                $('.timeCheck').each(function() {
                                    timeCheck($(this).attr('tnow'), $(this));
                                });
                            }
                        });
                    }
                });
            }
        });
    }
};
































function layerMsg(info) {
    layer.open({
        content: info,
        skin: 'msg',
        time: 2 //2秒后自动关闭
    });
}

function viewHtml(data) {
    var url = data.url,
        view = data.view,
        title = data.title;
    // alert(url);
    if (data.url) {
        $.get(url, function(data) {
            layui.use('laytpl', function() {
                var laytpl = layui.laytpl;
                var getTpl = view.html();
                laytpl(getTpl).render(data, function(html) {
                    return layer.open({
                        type: 1,
                        content: html,
                        anim: 'left',
                        style: 'position: fixed; top:0;left:0;width: 100%; height: 100%; border:none;overflow :auto;'
                    });
                });
            });
        });
    } else {
        return layer.open({
            type: 1,
            content: view.html(),
            anim: 'left',
            style: 'position: fixed; top:0;left:0;width: 100%; height: 100%; border:none;overflow :auto;'
        });
    }
}



/**
 * [TimeCheck 计时方式]
 * @Author   尹新斌
 * @DateTime 2017-07-20
 * @Function []
 * @param    {[type]}   sum [description]
 */
function timeCheck(sum, dom) {
    var min = dom.attr('tmin'),
        tplus = dom.attr('tplus');
    var interval, reg = /^\d$/,
        sleep = 1000; //间隔时间
    // var sum = 86400;
    if (!interval) {
        interval = setInterval(function() {
            sum--;
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