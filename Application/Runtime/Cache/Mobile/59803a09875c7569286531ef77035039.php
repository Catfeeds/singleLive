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
    <div class="addWrap">
        <div class="swipe" id="mySwipe">
            <div class="swipe-wrap">
                <div><a href="javascript:;"><img class="img-responsive" src="/Public/Mobile/img/sy01@2x.png"/></a></div>
                <div><a href="javascript:;"><img class="img-responsive" src="/Public/Mobile/img/sy01@2x.png" /></a></div>
                <div><a href="javascript:;"><img class="img-responsive" src="/Public/Mobile/img/sy01@2x.png"/></a></div>
                <div><a href="javascript:;"><img class="img-responsive" src="/Public/Mobile/img/sy01@2x.png" /></a></div>
            </div>
        </div>
        <ul id="position">
            <li class="cur"></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
    </div>
    <div class="app_Shortcut">
        <div class="Shortcut">
            <p><a href="<?php echo U('Rooms/index');?>"><img src="/Public/Mobile/img/sy02@2x.png"></a></p>
            <p>客房</p>
        </div>
        <div class="Shortcut">
            <p><a href="<?php echo U('Index/restaurant');?>"><img src="/Public/Mobile/img/sy03@2x.png"></a></p>
            <p>餐饮</p>
        </div>
        <div class="Shortcut">
            <p><a href="<?php echo U('Index/environment');?>"><img src="/Public/Mobile/img/sy04@2x.png"></a></p>
            <p>环境</p>
        </div>
        <div class="Shortcut">
            <p><a href="<?php echo U('Index/campaign');?>"><img src="/Public/Mobile/img/sy05@2x.png"></a></p>
            <p>体验活动</p>
        </div>
        <div class="Shortcut">
            <p><a href="<?php echo U('Index/package');?>"><img src="/Public/Mobile/img/sy06@2x.png"></a></p>
            <p>套餐</p>
        </div>
        <div class="Shortcut">
            <p><a href="<?php echo U('Index/club');?>"><img src="/Public/Mobile/img/sy07@2x.png"></a></p>
            <p>会员俱乐部</p>
        </div>
        <div class="Shortcut">
            <p><a href="<?php echo U('Self/index');?>"><img src="/Public/Mobile/img/sy08@2x.png"></a></p>
            <p>个人中心</p>
        </div>
        <div class="Shortcut">
            <p><a href="<?php echo U('Orders/index');?>"><img src="/Public/Mobile/img/sy09@2x.png"></a></p>
            <p>我的订单</p>
        </div>
    </div>
    <div class="app_lint">
        <div class="app_lint_tite">
            <img src="/Public/Mobile/img/sy10@2x.png" />
        </div>
        <div class="app_cont flow-default" id="LAY_demo1">
            <div class="app_lint_cont">
                <span><img src="/Public/Mobile/img/sy11@2x.png"></span>
                <h3>仙都啦城堡</h3>
                <p>尊贵城堡树屋</p>
            </div>
            <div class="app_lint_cont">
                <span><img src="/Public/Mobile/img/sy11@2x.png"></span>
                <h3>仙都啦城堡</h3>
                <p>尊贵城堡树屋</p>
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