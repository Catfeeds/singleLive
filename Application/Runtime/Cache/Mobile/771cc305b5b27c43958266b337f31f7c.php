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
                <div><a href="javascript:;"><img class="img-responsive" src="/Public/Mobile/img/hj01@2x.png"/></a></div>
                <div><a href="javascript:;"><img class="img-responsive" src="/Public/Mobile/img/hj01@2x.png" /></a></div>
                <div><a href="javascript:;"><img class="img-responsive" src="/Public/Mobile/img/hj01@2x.png"/></a></div>
                <div><a href="javascript:;"><img class="img-responsive" src="/Public/Mobile/img/hj01@2x.png" /></a></div>
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
            <p> 超胜庵始建于唐代,至今已有一千多年历史,金、明、清各代皆曾进行重修,为京东著名古刹之一超胜庵是云峰山景区的奇观，简称为"千年古刹";吸引众多游者来观赏!两岸有心人共同修复被战火毁坏的超胜庵，怀念共同抗日的年代!
                <br /> 于日本帝国主义侵略中国时遭到焚毁，直至1996年由云山恒泰旅游开发公司出资重建。超胜庵有十景:"石桥双柏"、"艮岩"、"妙云亭"、"普门桥"、"朝阳洞"、"苍玉屏"、"无尽意台"、"法幢"、"积翠崖"、"南天门"。超胜庵分前后两进院落，前院有仪门3间，中间为山门过厅，门内两侧有泥塑彩绘的站马、马童和坐像。 山门外东西各有一侧门，3门均为拱形券门。正门前有一座小石桥和18级条石雕刻的台阶。石桥旁原有两颗柏树，称为"石桥双柏"。殿外两侧各有一座螭首龟跃石碑，后院正殿3间是大佛殿，内有泥塑如来和弟子像，殿前月台两侧各有3间配殿，均有塑像。
            </p>
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