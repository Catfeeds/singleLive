<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta content="initial-scale=1.0,user-scalable=no,maximum-scale=1,width=device-width" name="viewport" />
    <meta content="initial-scale=1.0,user-scalable=no,maximum-scale=1" media="(device-height: 568px)" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <title></title>
</head>
<link rel="stylesheet" href="/Public/Mobile/js/layui/css/layui.css">
<link rel="stylesheet" href="/Public/Mobile/css/css.css">
<link rel="stylesheet" type="text/css" href="/Public/Mobile/time/css/index.css" />
<link rel="stylesheet" type="text/css" href="/Public/Mobile/time/css/LCalendar.css" />
<script src="/Public/Mobile/js/jquery-1.10.1.min.js"></script>
<script src='/Public/Mobile/js/hhSwipe.js' type="text/javascript"></script>
<script src="/Public/Mobile/js/layui/layui.js"></script>
<script src="/Public/Mobile/js/layer_mobile/layer.js"></script>
<script src="/Public/Mobile/js/js.js"></script>
<script>
(function(global) {
    function remChange() {
        document.documentElement.style.fontSize = 20 * document.documentElement.clientWidth / 1024 + 'px';
    }
    remChange();
    global.addEventListener('resize', remChange, false);
})(window);
</script>

<body>
    <div class="app_center">
    <div class="app_dister">
        <div class="app_system_dister">
            <a>双12大优惠双12大优惠双12大优惠</a>
            <p> 还有半天就要休园了，实不相瞒这是小编提前写的推送，现在本该写年终汇报的时候（我是拖延狂，庄主说上飞机前要报告完，才能出去玩），此时，小编抑制不住激动的心情，要把休园公告提前写了，我有充分的理由跟关注了我们一年的粉丝好好说再见！（多一半是关注好几年的唻） 谢谢大家这一年的关注，认真看小编的推送，忍受编的碎碎念，同时感谢大家喜欢云峰山的美景，来住童话树屋和东禅院，谢谢您给我们服务的机会，有大家的支持我们才会走的更稳更远，你们的满意是我们努力的方向！ 不知道上面说的是不是太“狗腿”了，但全是真心话，我的心里话也可以代表大部分云峰山小伙伴的意思，真心希望大家生活美满，精神富足!!! </p>
            <span class="right">2017-11-20</span>
        </div>
    </div>
</div>
    <div class="forder">
        <ul>
            <li><a class="ror_1 acver" href="<?php echo U('Index/index');?>">首页</a></li>
            <li><a class="ror_2" href="<?php echo U('Rooms/index');?>">客房</a></li>
            <li><a class="ror_3" href="<?php echo U('Orders/index');?>">订单</a></li>
            <li><a class="ror_4" href="<?php echo U('Self/index');?>">我的</a></li>
        </ul>
    </div>
</body>
<script>
//轮播图
if ($('#position').length > 0) {
    var bullets = document.getElementById('position').getElementsByTagName('li');
    var banner = Swipe(document.getElementById('mySwipe'), {
        auto: 4000,
        continuous: true,
        disableScroll: false,
        callback: function(pos) {
            var i = bullets.length;
            while (i--) {
                bullets[i].className = ' ';
            }
            bullets[pos].className = 'cur';
        }
    })
}
</script>
</html>