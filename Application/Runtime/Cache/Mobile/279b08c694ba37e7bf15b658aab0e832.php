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
    <div class="problem_tite">
        常见问题
    </div>
    <div class="problem">
        <div class="biem_cont biatacve">
            <div class="biem_tite">
                <span>花季时间预告</span>
            </div>
            <div class="biem_ecor">
                薰衣草园介绍？
                <br /> 1.景区薰衣草种在山谷里，主要品种是英国狭叶薰衣草，这种薰衣草原产地英国，在法国普罗旺斯广泛种植，可以用来提炼最顶级的薰衣草精油。
                <br /> 2.景区目前一共有20亩地左右，每年六月初开始开花，一直持续到七月中下旬，花期约一个半月，与普罗旺斯的花期完全同步。
                <br /> 3.六月中下旬是薰衣草花开最漂亮的时候。
            </div>
        </div>
        <div class="biem_cont">
            <div class="biem_tite">
                <span>客户须知</span>
            </div>
            <div class="biem_ecor">
                薰衣草园介绍？
                <br /> 1.景区薰衣草种在山谷里，主要品种是英国狭叶薰衣草，这种薰衣草原产地英国，在法国普罗旺斯广泛种植，可以用来提炼最顶级的薰衣草精油。
                <br /> 2.景区目前一共有20亩地左右，每年六月初开始开花，一直持续到七月中下旬，花期约一个半月，与普罗旺斯的花期完全同步。
                <br /> 3.六月中下旬是薰衣草花开最漂亮的时候。
            </div>
        </div>
        <div class="biem_cont">
            <div class="biem_tite">
                <span>团队客户</span>
            </div>
            <div class="biem_ecor">
                薰衣草园介绍？
                <br /> 1.景区薰衣草种在山谷里，主要品种是英国狭叶薰衣草，这种薰衣草原产地英国，在法国普罗旺斯广泛种植，可以用来提炼最顶级的薰衣草精油。
                <br /> 2.景区目前一共有20亩地左右，每年六月初开始开花，一直持续到七月中下旬，花期约一个半月，与普罗旺斯的花期完全同步。
                <br /> 3.六月中下旬是薰衣草花开最漂亮的时候。
            </div>
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