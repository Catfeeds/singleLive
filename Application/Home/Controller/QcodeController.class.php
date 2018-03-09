<?php
namespace Home\Controller;
use Think\Controller;
use Think\D;
class QcodeController extends CommonController
{
    public static $login = false;
    public function get_qrcode()
    {
        $url = base64_decode(I('url'));
        ob_end_clean();
        vendor("phpqrcode.phpqrcode");
        $QRcode = new \QRcode();
        $QRcode->png($url, false, 'H', 6);
    }
}