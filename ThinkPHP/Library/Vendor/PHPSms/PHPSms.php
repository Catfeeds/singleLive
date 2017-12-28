<?php
/**
 * 腾讯云 短信发送类
 */
require_once "SmsSender.php";
require_once "SmsVoiceSender.php";
use Qcloud\Sms\SmsSingleSender;
use Qcloud\Sms\SmsMultiSender;
use Qcloud\Sms\SmsVoicePromtSender;
use Qcloud\Sms\SmsVoiceVeriryCodeSender;
class PHPSms{
	private $appid;
	private $appkey;
	private $obj;
	public function __construct($appid,$appkey)
	{
		$this->appid = $appid;
		$this->appkey = $appkey;
		$this->obj = new SmsSingleSender($appid, $appkey);
	}
	/**
	 * [sendSms 发送模板短信]
	 * @Author   尹新斌
	 * @DateTime 2017-07-21
	 * @Function []
	 * @param    [type]     $phone_number [收件人手机号码]
	 * @param    [type]     $templId      [模板编号]
	 * @param    array      $params       [正文内容]
	 * @return   [type]                   [返回json]
	 */
	public function sendSms($phone_number,$templId,$params = array())
	{
		try {
			$singleSender = $this->obj;
		    $result = $singleSender->sendWithParam("86", $phone_number, $templId, $params, '手机绑定', "", "");
		    $rsp = json_decode($result);
		    return $result;
		} catch (\Exception $e) {
			echo var_dump($e);
		}
	}
}