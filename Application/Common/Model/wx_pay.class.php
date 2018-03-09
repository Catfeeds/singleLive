<?php
namespace Common\Model;

class wx_pay {

	//扫码支付
	public static function native($data){
		Vendor('WeiXinPay.Wxpay');
		$notify = new \NativeNotifyCallBack();
		// $notify->Handle(false);
		return call_user_func_array([$notify, 'unifiedorder'], $data);
	}
	//支付回调 验证订单
	public static function vrify_order($data, &$msg){
		Vendor('WeiXinPay.PayNotify');
		$pay_notify = new \PayNotifyCallBack();
		$pay_notify->Handle(false);
		return $pay_notify->NotifyProcess($data, $msg);
	}
}