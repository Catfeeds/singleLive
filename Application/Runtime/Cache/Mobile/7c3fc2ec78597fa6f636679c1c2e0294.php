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
                <div><a href="javascript:;"><img class="img-responsive" src="/Public/Mobile/img/kf01@2x.png"/></a></div>
                <div><a href="javascript:;"><img class="img-responsive" src="/Public/Mobile/img/kf01@2x.png" /></a></div>
                <div><a href="javascript:;"><img class="img-responsive" src="/Public/Mobile/img/kf01@2x.png"/></a></div>
                <div><a href="javascript:;"><img class="img-responsive" src="/Public/Mobile/img/kf01@2x.png" /></a></div>
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
                <img src="/Public/Mobile/img/myct02@2x.png">
            </div>
            <div class="app_dist_tite_colo">
                房间设备
            </div>
        </div>
        <div class="app_dist_cont">
            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;以健康蔬食为主的茗园餐厅，使用当地新鲜安全的食材，古法自榨食用葵花籽油，云峰山天然麦饭石矿泉水水质甘甜，无论饭菜、羹汤、咖啡和茶都更美味。</p>
        </div>
    </div>
    <div class="app_dist">
        <div class="app_dist_tite">
            <div class="app_dist_tite_img">
                <img src="/Public/Mobile/img/myct03@2x.png">
            </div>
            <div class="app_dist_tite_colo">
                早餐 <a>自助式  供应时间： 7:15-9:00</a>
            </div>
        </div>
    </div>
    <div class="app_dist">
        <div class="app_dist_tite">
            <div class="app_dist_tite_img">
                <img src="/Public/Mobile/img/myct04@2x.png">
            </div>
            <div class="app_dist_tite_colo">
                午餐 <a>供应时间： 11:30-14:30</a>
            </div>
        </div>
    </div>
    <div class="app_dist">
        <div class="app_dist_tite">
            <div class="app_dist_tite_img">
                <img src="/Public/Mobile/img/myct05@2x.png">
            </div>
            <div class="app_dist_tite_colo">
                晚餐 <a>供应时间： 17:15-20:00 </a>
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