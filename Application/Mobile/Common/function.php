<?php
use Think\D;
/**
 * [sendSMS description]
 * @Author   ヽ(•ω•。)ノ   Mr.Solo
 * @DateTime 2018-01-05
 * @Function []
 * @param    [type]     $mobile [手机号]
 * @param    [type]     $code   [验证码]
 * @return   [type]             [description]
 */
function sendSMS($mobile,$code)
{
	$url = 'http://smssh1.253.com/msg/variable/json';
	$param = '{"account":"N7720646","password":"nglIWbkKZ3a90c","msg":"【山野运动基地】您的验证码是：{$var}","params":"'.$mobile.','.$code.'","sendtime":"201801010101","report":"true","extend":"555","uid":"123456"}';
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS,$param);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    'Content-Type: application/json',
	    'Content-Length: ' . strlen($param)
	));
	$result = json_decode(curl_exec($ch),'array');
	if ($result['code'] == '0') {
		return [
			'status' => true,
		];
	}else{
		return [
			'status' => false,
			'errorMsg' => $result['errorMsg']
		];
	}

}
function getBanner($type)
{
	$banner = D::get('Banner',[
		'alias' => 'B',
		'where' => ['B.type' => $type],
		'join'  => 'LEFT JOIN __FILES__ F ON F.id = B.imgs',
		'field' => "B.*,CONCAT('/Uploads',F.savepath,F.savename) `icon`",
	]);
	return $banner;
}