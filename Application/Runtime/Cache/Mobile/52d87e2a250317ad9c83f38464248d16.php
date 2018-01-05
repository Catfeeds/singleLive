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
    <div class="integral_tite">
        <span>我的积分</span>
        <b>1000</b>
    </div>
    <div class="integral">
        <div class="integral_cont">
            <div class="integral_left">
                <img src="/Public/Mobile/img/jf01@2x.png">
            </div>
            <div class="integral_right">
                <div class="integral_right_f">
                    <span>会员晋级</span>
                    <b>500积分</b>
                </div>
                <div class="integral_right_r">
                    <a class="diopr spoert" msg-tite="您是否购买？">购买</a>
                </div>
            </div>
        </div>
        <div class="integral_cont">
            <div class="integral_left">
                <img src="/Public/Mobile/img/jf01@2x.png">
            </div>
            <div class="integral_right">
                <div class="integral_right_f">
                    <span>会员晋级</span>
                    <b>500积分</b>
                </div>
                <div class="integral_right_r">
                    <a class="diopr spoert" msg-tite="您是否购买？">购买</a>
                </div>
            </div>
        </div>
        <div class="integral_cont">
            <div class="integral_left">
                <img src="/Public/Mobile/img/jf01@2x.png">
            </div>
            <div class="integral_right">
                <div class="integral_right_f">
                    <span>会员晋级</span>
                    <b>500积分</b>
                </div>
                <div class="integral_right_r">
                    <a class="diopr spoert" msg-tite="您是否购买？">购买</a>
                </div>
            </div>
        </div>
        <div class="integral_cont">
            <div class="integral_left">
                <img src="/Public/Mobile/img/jf01@2x.png">
            </div>
            <div class="integral_right">
                <div class="integral_right_f">
                    <span>会员晋级</span>
                    <b>500积分</b>
                </div>
                <div class="integral_right_r">
                    <a class="diopr spoert" msg-tite="您是否购买？">购买</a>
                </div>
            </div>
        </div>
        <div class="integral_cont">
            <div class="integral_left">
                <img src="/Public/Mobile/img/jf01@2x.png">
            </div>
            <div class="integral_right">
                <div class="integral_right_f">
                    <span>会员晋级</span>
                    <b>500积分</b>
                </div>
                <div class="integral_right_r">
                    <a class="diopr spoert" msg-tite="您是否购买？">购买</a>
                </div>
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