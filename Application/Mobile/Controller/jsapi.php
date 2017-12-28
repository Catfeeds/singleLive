<?php
header("Content-Type: text/html; charset=UTF-8");
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
require_once "../lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';

// echo '<pre>';
// var_dump($_GET);die;
// echo '</pre>';



$title = $_GET['title'];
$orderNo = $_GET['orderNo'];
$amount = $_GET['amount'];
$str_attach = $_GET['str_attach'];

session_start();
$_SESSION["PayOrder"]=$orderNo;

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

//打印输出数组信息
function printf_info($data)
{
    foreach($data as $key=>$value){
        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
}

//①、获取用户openid
$tools = new JsApiPay();
$openId = $tools->GetOpenid();

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody($title);
$input->SetAttach($str_attach);
$input->SetOut_trade_no($orderNo);
$input->SetTotal_fee(1);//($amount);//
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("test");
$input->SetNotify_url("http://wwww.jszsxt.com/WeiXinPay/example/notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);
// echo '<font color="#f00"><b>订单支付</b></font><br/>';

// echo '<pre>';
// var_dump($order);exit;
// echo '</pre>';

// printf_info($order);
$jsApiParameters = $tools->GetJsApiParameters($order);

//获取共享收货地址js函数参数
$editAddress = $tools->GetEditAddressParameters();

//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */
?>

<!doctype html>
<html class="no-js">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="viewport"
        content="width=device-width, initial-scale=1">
  <title>微信支付</title>

  <!-- Set render engine for 360 browser -->
  <meta name="renderer" content="webkit">

  <!-- No Baidu Siteapp-->
  <meta http-equiv="Cache-Control" content="no-siteapp"/>

  <!--<link rel="icon" type="image/png" href="/Public/Mobile/assets/i/favicon.png">-->

  <!-- Add to homescreen for Chrome on Android -->
  <meta name="mobile-web-app-capable" content="yes">
 <!-- <link rel="icon" sizes="192x192" href="/Public/Mobile/assets/i/app-icon72x72@2x.png">-->

  <!-- Add to homescreen for Safari on iOS -->
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-title" content="Amaze UI"/>
  <link rel="apple-touch-icon-precomposed" href="/Public/Mobile/assets/i/app-icon72x72@2x.png">

  <!-- Tile icon for Win8 (144x144 + tile color) -->
  <meta name="msapplication-TileImage" content="assets/i/app-icon72x72@2x.png">
  <meta name="msapplication-TileColor" content="#0e90d2">
<link rel="stylesheet" type="text/css" href="css/wxzf.css">

    <script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				// alert(res.err_code+res.err_desc+res.err_msg);
				if (res.err_msg == "get_brand_wcpay_request:ok") {
				// message: "微信支付成功!",
					window.location.replace('http://www.jszsxt.com/Index/pay/orderNo/<?php echo $orderNo;?>');
				}else if (res.err_msg == "get_brand_wcpay_request:cancel") {
				// message: "已取消微信支付!"
				}
			}
		);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall);
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
	</script>
	<script type="text/javascript">
	//获取共享地址
	function editAddress()
	{
		WeixinJSBridge.invoke(
			'editAddress',
			<?php echo $editAddress; ?>,
			function(res){
				var value1 = res.proviceFirstStageName;
				var value2 = res.addressCitySecondStageName;
				var value3 = res.addressCountiesThirdStageName;
				var value4 = res.addressDetailInfo;
				var tel = res.telNumber;

				// alert(value1 + value2 + value3 + value4 + ":" + tel);
			}
		);
	}

	window.onload = function(){
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', editAddress, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', editAddress);
		        document.attachEvent('onWeixinJSBridgeReady', editAddress);
		    }
		}else{
			editAddress();
		}
	};

	</script>
</head>
<body>
<!--     <br/>
    <font color="#9ACD32"><b>支付金额<span style="color:#f00;font-size:50px">元</span></b></font><br/><br/>
 -->
<center>



  <!--   <div class="header">
        <div class="all_w ">
            <div class="gofh"> <a href="#"><img src="images/jt_03.jpg" ></a> </div>
            <div class="ttwenz">
                <h4>确认交易</h4>
                <h5>微信安全支付</h5>
            </div>
        </div>
    </div> -->
    <div class="wenx_xx">
        <div class="mz">微信安全支付</div>
        <div class="wxzf_price">￥<?php echo $amount * 0.01; ?></div>
    </div>
    <div class="skf_xinf">
        <div class="all_w"> <span class="bt">商家订单号 ：</span> <span class="fr"><?php echo $orderNo; ?></span> </div>
    </div>
    <a href="javascript:void(0);" class="ljzf_but all_w" onclick="callpay()">立即支付</a>














<!-- 
    <div class="aenthend">
      <div class="fendch">
               <div class="fendch_left">金额 ：
               </div>
               <div class="fendch_right">￥<?php echo $amount * 0.01; ?>
               </div>
         </div>
      <div class="fendch">
               <div class="fendch_left">商家订单号 ：
               </div>
               <div class="fendch_right"><?php echo $orderNo; ?>
               </div>
         </div>
         <div class="bn_oot" align="center">
           <button class="sumet" type="button" onclick="callpay()" >立即支付</button>
         </div>
    </div> -->

</center>
</body>
</html>