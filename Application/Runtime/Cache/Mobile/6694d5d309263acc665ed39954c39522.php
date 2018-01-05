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
    <div class="barto">
        <div class="hort_cont">
            <a href="/index.php/Mobile/Orders/edit" class="ritu_1">
                       <span class="left">订单号：12345678945612</span>
                       <span class="right">房型房型</span>
                  </a>
            <div class="ritu_2">
                <span class="left">2017年11月8日-2017年11月10日</span>
                <span class="right">待支付</span>
            </div>
            <div class="ritu_1">
                <a class="web_qzf left">去支付</a>
                <b class="right">￥2588</b>
            </div>
        </div>
        <div class="hort_cont">
            <a href="/index.php/Mobile/Orders/edit" class="ritu_1">
                       <span class="left">订单号：12345678945612</span>
                       <span class="right">房型房型</span>
                  </a>
            <div class="ritu_2">
                <span class="left">2017年11月8日-2017年11月10日</span>
                <span class="right cor_4">已支付</span>
            </div>
            <div class="ritu_1">
                <a class="web_tk left ">退款</a>
                <b class="right">￥2588</b>
            </div>
        </div>
        <div class="hort_cont">
            <a href="/index.php/Mobile/Orders/edit" class="ritu_1">
                       <span class="left">订单号：12345678945612</span>
                       <span class="right">房型房型</span>
                  </a>
            <div class="ritu_2">
                <span class="left">2017年11月8日-2017年11月10日</span>
                <span class="right cor_1">已完成</span>
            </div>
            <div class="ritu_1">
                <b class="right">￥2588</b>
            </div>
        </div>
        <div class="hort_cont">
            <a href="/index.php/Mobile/Orders/edit" class="ritu_1">
                       <span class="left">订单号：12345678945612</span>
                       <span class="right">房型房型</span>
                  </a>
            <div class="ritu_2">
                <span class="left">2017年11月8日-2017年11月10日</span>
                <span class="right cor_2">已退款</span>
            </div>
            <div class="ritu_1">
                <b class="right">￥2588</b>
            </div>
        </div>
        <div class="hort_cont">
            <a href="/index.php/Mobile/Orders/edit" class="ritu_1">
                       <span class="left">订单号：12345678945612</span>
                       <span class="right">房型房型</span>
                  </a>
            <div class="ritu_2">
                <span class="left">2017年11月8日-2017年11月10日</span>
                <span class="right cor_3">已取消</span>
            </div>
            <div class="ritu_1">
                <b class="right">￥2588</b>
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