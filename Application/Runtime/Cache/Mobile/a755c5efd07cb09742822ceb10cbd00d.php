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
                <div><a href="javascript:;"><img class="img-responsive" src="/Public/Mobile/img/hy03@2x.png"/></a></div>
                <div><a href="javascript:;"><img class="img-responsive" src="/Public/Mobile/img/hy03@2x.png" /></a></div>
                <div><a href="javascript:;"><img class="img-responsive" src="/Public/Mobile/img/hy03@2x.png"/></a></div>
                <div><a href="javascript:;"><img class="img-responsive" src="/Public/Mobile/img/hy03@2x.png" /></a></div>
            </div>
        </div>
        <ul id="position">
            <li class="cur"></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
    </div>
    <div class="app_dist">
        <div class="app_dist_tite">
            <div class="app_dist_tite_img">
                <img src="/Public/Mobile/img/kf08@2x.png">
            </div>
            <div class="app_dist_tite_colo">
                介绍
            </div>
        </div>
        <div class="app_dist_cont">
            <p>现在就加入云峰山森林俱乐部，享受优先订房权，更有用餐、购物多种优惠畅享！入住及消费即获积分返金！送给家人一个浪漫假期也犒赏下自己！成为官网会员注册并登录官网用户，获得住客级别，就可以在开放预订期间预订客房了。 </p>
            <p>• 预订累积积分晋级</p>
            <p>当您官网预订并支付，完成入住之后会获得对应积分，通过累积积分可以兑换会员晋级（一年会员权益）。查看积分在会员中心-基本信息页查看《森林卡会员手册》>></p>
            <p>• 储值会员卡晋级</p>
            <p>或者直接通过官网储值，直接获得对应会员等级，优先享受预订，立即获得多重优惠及会员礼遇。现在去充值>></p>
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