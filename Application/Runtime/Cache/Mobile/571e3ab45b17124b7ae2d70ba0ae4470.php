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
    <title>登录</title>
</head>
<link rel="stylesheet" href="/Public/Mobile/js/layui/css/layui.css">
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

<body style="background: #FFFFFF;">
    <img src="/Public/Mobile/img/dl01@2x.png" width="100%">
    <div class="app_Login">
        <div class="Login_fr_1">
            <input type="text" placeholder="请输入手机号" />
        </div>
        <div class="Login_fr_2">
            <input type="text" placeholder="请输入密码" />
        </div>
        <input type="button" value="登录" class="Login_but" />
        <div class="Login_fz">
            <a href="/index.php/Mobile/Login/password.html" class="left">忘记密码</a>
            <a href="/index.php/Mobile/Login/register.html" class="right">立即注册</a>
        </div>
    </div>
</body>
<script src="/Public/Mobile/js/jquery-1.10.1.min.js"></script>
<script src='/Public/Mobile/js/hhSwipe.js' type="text/javascript"></script>
<script src="/Public/Mobile/js/layui/layui.js"></script>
<script src="/Public/Mobile/js/layer_mobile/layer.js"></script>
<script src="/Public/Mobile/js/js.js"></script>

</html>