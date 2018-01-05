<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta content="initial-scale=1.0,user-scalable=no,maximum-scale=1,width=device-width" name="viewport" />
    <meta content="initial-scale=1.0,user-scalable=no,maximum-scale=1" media="(device-height: 568px)" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <title>民宿</title>
</head>
<link rel="stylesheet" href="/Public/Mobile/js/layui/css/layui.css">
<!--时间插件-->
<link rel="stylesheet" type="text/css" href="/Public/Mobile/time/css/index.css" />
<link rel="stylesheet" type="text/css" href="/Public/Mobile/time/css/LCalendar.css" />
<!--时间插件-->
<link rel="stylesheet" href="/Public/Mobile/css/css.css">
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
            <div class="fr_input">
                <div class="fr_input_left">
                    手机号码 ：
                </div>
                <div class="fr_input_right">
                    <input placeholder="请输入电话号码" class="inp_1" type="text">
                </div>
            </div>
            <div class="fr_input">
                <div class="fr_input_left">
                    验 &nbsp;证&nbsp; 码 ：
                </div>
                <div class="app_Login_wj">
                    <input placeholder="请输入验证码" class="Login_inp_1" type="text">
                    <input type="button" value="获取验证码" class="Login_inp_2" />
                </div>
            </div>
        </div>
        <div class="dist_but2 ">
            <a class="spoert" msg-tite="您是否确认？”">确认</a>
        </div>
    </div>
</body>
<script src="/Public/Mobile/js/jquery-1.10.1.min.js"></script>
<script src='/Public/Mobile/js/hhSwipe.js' type="text/javascript"></script>
<script src="/Public/Mobile/js/layui/layui.js"></script>
<script src="/Public/Mobile/js/layer_mobile/layer.js"></script>
<script src="/Public/Mobile/js/js.js"></script>

</html>