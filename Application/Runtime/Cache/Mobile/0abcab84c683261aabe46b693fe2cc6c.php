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
    <div class="app_Personal">
        <div class="Personal">
            <div class="app_name">
                <img src="/Public/Mobile/img/my02@2x.png" />
                <input type="file" />
            </div>
            <div class="app_name_1">
                丫头
            </div>
            <div class="app_name_1">
                会员等级：<a>V1</a>
            </div>
        </div>
    </div>
    <div class="pers_cont">
        <a href="/index.php/Mobile/Self/information" class="biten">
            <div class="biten_img"><img src="/Public/Mobile/img/my06@2x.png"></div>
            <span>基本信息</span>
            <b><img src="/Public/Mobile/img/my05@2x.png"></b>
        </a>
        <a href="/index.php/Mobile/Self/balance" class="biten">
            <div class="biten_img"><img src="/Public/Mobile/img/my07@2x.png"></div>
            <span>账户余额</span>
            <b><img src="/Public/Mobile/img/my05@2x.png"></b>
        </a>
        <a href="<?php echo U('Orders/index');?>" class="biten">
            <div class="biten_img"><img src="/Public/Mobile/img/my09@2x.png"></div>
            <span>我的订单</span>
            <b><img src="/Public/Mobile/img/my05@2x.png"></b>
        </a>
        <a href="/index.php/Mobile/Self/coupon" class="biten">
            <div class="biten_img"><img src="/Public/Mobile/img/my10@2x.png"></div>
            <span>电子卷</span>
            <b><img src="/Public/Mobile/img/my05@2x.png"></b>
        </a>
        <a href="/index.php/Mobile/Self/upgrade" class="biten">
            <div class="biten_img"><img src="/Public/Mobile/img/my11@2x.png"></div>
            <span>积分升级</span>
            <b><img src="/Public/Mobile/img/my05@2x.png"></b>
        </a>
        <a href="/index.php/Mobile/Self/message" class="biten">
            <div class="biten_img"><img src="/Public/Mobile/img/xx01@2x.png">
                <div class="biten_span">99</div>
            </div>
            <span>系统消息</span>
            <b><img src="/Public/Mobile/img/my05@2x.png"></b>
        </a>
        <a href="/index.php/Mobile/Self/activity" class="biten">
            <div class="biten_img"><img src="/Public/Mobile/img/xx02@2x.png">
                <div class="biten_span">99</div>
            </div>
            <span>活动消息</span>
            <b><img src="/Public/Mobile/img/my05@2x.png"></b>
        </a>
    </div>
    <div class="pers_cont1">
        <a href="/index.php/Mobile/Self/password" class="biten">
            <div class="biten_img"><img src="/Public/Mobile/img/mm@2x.png"></div>
            <span>修改密码</span>
            <b><img src="/Public/Mobile/img/my05@2x.png"></b>
        </a>
        <a href="/index.php/Mobile/Self/problem" class="biten">
            <div class="biten_img"><img src="/Public/Mobile/img/my12@2x.png"></div>
            <span>常见问题</span>
            <b><img src="/Public/Mobile/img/my05@2x.png"></b>
        </a>
    </div>
    <div class="dist_but3 ">
        <a href="Login.html" class="spoert" msg-tite="您是否退出登录？”">退出登录</a>
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