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
    <div class="web_form">
        <div class="app_dist_tite">
            <div class="app_dist_tite_img">
                <img src="/Public/Mobile/img/kf06@2x.png">
            </div>
            <div class="app_dist_tite_colo">
                预定须知
            </div>
        </div>
        <div class="web_flocg">
            <div class="web_flocg_left">
                在树屋中共度浪漫纪念日
            </div>
            <div class="web_flocg_right">
                <a>￥2588</a>
            </div>
        </div>
        <div class="web_flocg">
            <div class="web_flocg_left">
                入住日期
            </div>
            <div class="web_flocg_right">
                2017年11月08日
            </div>
        </div>
        <div class="web_flocg">
            <div class="web_flocg_left">
                数量
            </div>
            <div class="web_flocg_right">
                1人
            </div>
        </div>
    </div>
    <div class="app_dist">
        <div class="app_dist_tite">
            <div class="app_dist_tite_img">
                <img src="/Public/Mobile/img/yd02@2x.png">
            </div>
            <div class="app_dist_tite_colo">
                使用电子卷
            </div>
        </div>
        <div class="web_icom">
            <div class="web_icom_left">
                <div class="fr_l1 ">
                    <input type="checkbox" />
                </div>
                <div class="fr_l2">
                    1362526358
                </div>
            </div>
            <div class="web_icom_cont">
                100元
            </div>
            <div class="web_icom_right">
                未使用
            </div>
        </div>
        <div class="web_icom">
            <div class="web_icom_left">
                <div class="fr_l1 ">
                    <input type="checkbox" />
                </div>
                <div class="fr_l2">
                    1362526358
                </div>
            </div>
            <div class="web_icom_cont">
                100元
            </div>
            <div class="web_icom_right">
                未使用
            </div>
        </div>
    </div>
    <div class="app_dist">
        <div class="app_dist_tite">
            <div class="app_dist_tite_img">
                <img src="/Public/Mobile/img/yd03@2x.png">
            </div>
            <div class="app_dist_tite_colo">
                填写信息
            </div>
        </div>
        <div class="fr_input">
            <div class="fr_input_left">
                手机号码 ：
            </div>
            <div class="fr_input_right">
                <input type="text" placeholder="请输入电话号码" class="inp_1" />
            </div>
        </div>
        <div class="fr_input">
            <div class="fr_input_left">
                称&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;谓：
            </div>
            <div class="fr_input_right">
                <div class="fr_radio">
                    <input type="radio" name="radio" />先生
                </div>
                <div class="fr_radio">
                    <input type="radio" name="radio" />女士
                </div>
            </div>
        </div>
        <div class="fr_input">
            <div class="fr_input_left">
                姓&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名 ：
            </div>
            <div class="fr_input_right">
                <input type="text" placeholder="请输入姓名" class="inp_1" />
            </div>
        </div>
        <div class="fr_input">
            <div class="fr_input_left">
                电子邮箱 ：
            </div>
            <div class="fr_input_right">
                <input type="text" placeholder="请输入电子邮箱" class="inp_1" />
            </div>
        </div>
        <div class="fr_input">
            <div class="fr_input_left">
                成&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;人 ：
            </div>
            <div class="fr_input_right">
                <select class="fr_select">
                    <option>1</option>
                    <option>2</option>
                </select>
            </div>
        </div>
        <div class="fr_input">
            <div class="fr_input_left">
                儿&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;童 ：
            </div>
            <div class="fr_input_right">
                <select class="fr_select">
                    <option>1</option>
                    <option>2</option>
                </select>
            </div>
        </div>
        <div class="fr_input">
            <div class="fr_input_left">
                备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注 ：
            </div>
            <div class="fr_input_right">
                <textarea></textarea>
            </div>
        </div>
    </div>
    <div class="dist_but2 ">
        <a class="spoert" msg-tite="您是否提交信息？”">提交信息</a>
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