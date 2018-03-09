<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\D;
class WxPayController extends CommonController{
	public static $login = false;
	//调起微信支付
	public function jsapi()
	{
		
		$this->assign('parameter', I('parameter'));
		$this->assign('editAddress', I('editAddress'));
		$this->assign('orderNo', I('orderNo'));
		$this->assign('totalFee', I('totalFee'));
		$this->display();
	}
}