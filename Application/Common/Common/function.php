<?php
use Think\D;
function msubstr($str, $length, $start=0, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}
/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 */
function tree_to_list($tree, $child = '_child', $order='id', &$list = array()){
    if(is_array($tree)) {
        $refer = array();
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if(isset($reffer[$child])){
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby='asc');
    }
    return $list;
}
/**
     * 数据导入导出
     * @param  文件名
     * @param  数据库标题字段名
     * @param  数据
     */

/*
    exexl导出方法
*/
function export_Excel($expTitle="",$expCellName,$expTableData){
    ob_end_clean();
    $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
    $fileName = $xlsTitle?$xlsTitle:$_SESSION['account'].date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
    $cellNum = count($expCellName);
    $dataNum = count($expTableData);
    vendor("PHPExcel.PHPExcel");
    $objPHPExcel = new \PHPExcel();
    $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
    $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
    $objPHPExcel->setActiveSheetIndex(0)->getstyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//标题居中
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true); //标题字体加粗 ;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1", $expTitle); //设置标题

    for($i=0;$i<$cellNum;$i++){
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
    }
    for($i=0;$i<$dataNum;$i++){
      for($j=0;$j<$cellNum;$j++){
        $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
      }
    }
    header('pragma:public');
    header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
    header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
    $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
}

/**
 * excel数据 导入
 * @param  [type] $file [description]
 * @return [type]       [description]
 */
function importExcel($file){
    // 判断文件是什么格式
    $type = pathinfo($file);
    $type = strtolower($type["extension"]);
    $type=$type==='csv' ? $type : 'Excel5';
    ini_set('max_execution_time', '0');
    Vendor('PHPExcel.PHPExcel');
    // 判断使用哪种格式
    $objReader = PHPExcel_IOFactory::createReader($type);
    $objPHPExcel = $objReader->load($file);
    $sheet = $objPHPExcel->getSheet(0);
    // 取得总行数
    $highestRow = $sheet->getHighestRow();
    // 取得总列数
    $highestColumn = $sheet->getHighestColumn();
    //循环读取excel文件,读取一条,插入一条
    $data=array();
    //从第一行开始读取数据
    for($j=1;$j<=$highestRow;$j++){
        //从A列读取数据
        for($k='A';$k<=$highestColumn;$k++){
            // 读取单元格
            $data[$j][]=$objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue();
        }
    }
    return $data;
}
/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}
/**
 * PHPMailer邮件发送函数
 * @param  [type] $to      [description]
 * @param  [type] $title   [description]
 * @param  [type] $content [description]
 * 需先按照配置要求在config内配置
 */
function sendMail($to, $title, $content) {
    Vendor('PHPMailer.PHPMailerAutoload');
    $mail = new PHPMailer(); //实例化
    $mail->IsSMTP(); // 启用SMTP
    $mail->Host=C('MAIL_HOST'); //smtp服务器的名称（这里以QQ邮箱为例）
//  $mail->Port = 465;
    $mail->SMTPAuth = C('MAIL_SMTPAUTH'); //启用smtp认证
    $mail->Username = C('MAIL_USERNAME'); //你的邮箱名
    $mail->Password = C('MAIL_PASSWORD') ; //邮箱密码
    $mail->From = C('MAIL_FROM'); //发件人地址（也就是你的邮箱地址）
//  $mail->SMTPSecure='ssl';
    $mail->FromName = C('MAIL_FROMNAME'); //发件人姓名
    $mail->AddAddress($to,"尊敬的客户");
    $mail->WordWrap = 50; //设置每行字符长度
    $mail->IsHTML(C('MAIL_ISHTML')); // 是否HTML格式邮件
    $mail->CharSet=C('MAIL_CHARSET'); //设置邮件编码
    $mail->Subject =$title; //邮件主题
    $mail->Body = $content; //邮件内容
    $mail->AltBody = "这是一个纯文本的身体在非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示
    $mail->send();
}
function WhiteFile($url,$new)
{
    $text = php_strip_whitespace($url);
    $open = fopen($new,'a');
    fwrite($open, $text);
    fclose($myfile);
}
/**
 * [date_out 格式化时间戳]
 * @Author   尹新斌
 * @DateTime 2017-07-11
 * @Function []
 * @param    [type]     $time [description]
 * @return   [type]           [description]
 */
function date_out($time,$msg ='-')
{
    if ($time >0) {
        return date('Y-m-d H:i:s',$time);
    }else{
        return $msg;
    }
}
/**
 * [uploadOne description]
 * @Author   尹新斌
 * @DateTime 2017-07-12
 * @Function []
 * @return   [type]     [description]
 */
function uploadOne($options='')
{
    $options = $options?:C('UPLOAD_CONFIG');
    $upload = new \Think\Upload($options);
    $info   =   $upload->uploadOne($_FILES['file']);

    if ($info) {
        $info['filesid'] = D::add('Files',$info);
        if (is_numeric(I('width')) && is_numeric(I('height')) && I('width')>0 && I('height')>0) {
            $image = new \Think\Image(); 
            $image->open('./Uploads'.$info['savepath'].$info['savename']);
            $image->thumb(I('width'), I('height'),\Think\Image::IMAGE_THUMB_CENTER)->save('./Uploads'.$info['savepath'].$info['savename']);
        }
        return $info;
    }else{
        return false;
    }
}
/**
 * [bannerImgs 生成banner卢索图]
 * @Author   尹新斌
 * @DateTime 2017-07-20
 * @Function []
 * @param    [type]     $file [description]
 * @return   [type]           [description]
 */
function bannerImgs($data)
{
    $image = new \Think\Image();
    $image->open('./Uploads'.$data['savepath'].$data['savename']);
    $image->thumb(750, 405,\Think\Image::IMAGE_THUMB_CENTER)->save('./Uploads'.$data['savepath'].$data['savename']);
}
/**
 * [getSrc 获取上传位置]
 * @Author   尹新斌
 * @DateTime 2017-07-12
 * @Function []
 * @param    [type]     $fileID [description]
 * @return   [type]             [description]
 */
function getSrc($fileID)
{
    $files = D::find('Files',$fileID);
    return '/Uploads'.$files['savepath'].$files['savename'];

}
/**
 * [wx_url 微信授权页面]
 * @Author   尹新斌
 * @DateTime 2017-07-13
 * @Function []
 * @param    [type]     $url   [重定向地址]
 * @param    [type]     $state [返回状态]
 * @return   [type]            [description]
 */
function wx_url($url,$state=0)
{
    $url = $url?:'http://'.$_SERVER['SERVER_NAME'].'/index.php/Mobile/Login/index.html';
    $have = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=[APPID]&redirect_uri=[REDIRECT_URI]&response_type=[CODE]&scope=[SCOPE]&state=[STATE]#wechat_redirect';
    $need = str_replace(
        ['[APPID]','[REDIRECT_URI]','[CODE]','[SCOPE]','[STATE]'],
        [C('WX_APP_ID'),urlencode($url),'code','snsapi_userinfo',$state],
        $have);
    return $need;
}
/**
 * [put_file_from_url_content description]
 * @Author   尹新斌
 * @DateTime 2017-07-13
 * @Function []
 * @param    [type]     $url      [description]
 * @param    [type]     $saveName [description]
 * @param    [type]     $path     [description]
 * @return   [type]               [description]
 */
function put_file_from_url_content($url, $saveName, $path) {
    // 设置运行时间为无限制
    set_time_limit ( 0 );
    $url = trim ( $url );
    $curl = curl_init ();
    // 设置你需要抓取的URL
    curl_setopt ( $curl, CURLOPT_URL, $url );
    // 设置header
    curl_setopt ( $curl, CURLOPT_HEADER, 0 );
    // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
    curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
    // 运行cURL，请求网页
    $file = curl_exec ( $curl );
    // 关闭URL请求
    curl_close ( $curl );
    // 将文件写入获得的数据
    $filename = $path . $saveName;
    $write = @fopen ( $filename, "w" );
    if ($write == false) {
        return false;
    }
    if (fwrite ( $write, $file ) == false) {
        return false;
    }
    if (fclose ( $write ) == false) {
        return false;
    }
}
/**
 * [http_crul 打开网页地址]
 * @Author   尹新斌
 * @DateTime 2017-07-17
 * @Function []
 * @return   [type]     [description]
 */
function http_crul($url,$type='array')
{
    set_time_limit ( 0 );
    $curl = curl_init();
    curl_setopt ( $curl, CURLOPT_URL, trim($url) );
    curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
    $json = curl_exec ( $curl );
    curl_close ( $curl );
    if ($type == 'array') {
        return json_decode($json,true);
    }else{
        return $json;
    }
}
/**
 * [get_number description]
 * @Author   尹新斌
 * @DateTime 2017-07-14
 * @Function []
 * @param    [type]     $length [获取长度]
 * @return   [type]             [description]
 */
function get_number($length)
{
    $num;
    for ($i=0; $i < $length; $i++) {
        $num .= rand(0,9);
    }
    return $num;
}

/**
 * [get_selectTime description]
 * @Author   尹新斌
 * @DateTime 2017-07-19
 * @Function []
 * @param    string     $start [description]
 * @param    string     $end   [description]
 * @return   [type]            [description]
 */
function get_selectTime($start='',$end='')
{
    $nowTime = time();
    if (!empty($start)) {
        $startTime = strtotime($start);
        if ($startTime > $nowTime) {
            $startTime = $nowTime;
        }
    }
    if (!empty($end)) {
        $endTime = strtotime($end.' +1 days');
        if ($endTime > $nowTime) {
            $endTime = $nowTime;
        }
    }
    if ($startTime) {
        if ($endTime) {
            return ['between',[$startTime,$endTime]];
        }else{
            return ['between',[$startTime,$nowTime]];
        }
    }else{
        if ($endTime) {
            return ['lt',($endTime + 1)];
        }else{
            return ['lt',($nowTime + 1)];
        }
    }
}
//标准日期格式
function get_DateTime($start='',$end='')
{
    $nowTime = date('Y-m-d');
    if (!empty($start)) {
        $startTime = $start;
        if ($startTime > $nowTime) {
            $startTime = $nowTime;
        }
    }
    if (!empty($end)) {
        $endTime = date('Y-m-d',strtotime($end.' +1 days'));
        if ($endTime > $nowTime) {
            $endTime = $nowTime;
        }
    }
    if ($startTime) {
        if ($endTime) {
            return ['between',[$startTime,$endTime]];
        }else{
            return ['between',[$startTime,$nowTime]];
        }
    }else{
        if ($endTime) {
            return ['lt',date('Y-m-d',strtotime($endTime.' +1 days'))];
        }else{
            return ['lt',date('Y-m-d',strtotime($nowTime.' +1 days'))];
        }
    }
}


/**
 * [getConfig 获取配置信息]
 * @Author   谷美东
 * @DateTime 2017-07-20
 * @Function []
 * @param  string $configName 需要获取的配置名
 * @return string $status 需要获取的权限设置 0:后台 hotel::id 酒店
 */


function getConfig($configNames="", $status='0')
{
    $configs = array();

    if(!$configNames){

        $configs = D::get('config','status='.$status);

        foreach ($configs as $config) {

            $set[$config['key']] = $config['value'];

            if($config['key']=='head'){

                $items=D::find('files','id='. $config['value'].'');
                $set['headUrl'] = '/Uploads'.$items['savepath'].$items['savename'];
            }
        }

        return $set;


    }else if(is_array($configNames)){

        foreach ($configNames as $key => $configName) {
           $row=D::find('config',[
                'status' => $status,
                'key'    => $configName
            ]);

           $configs[$configName] = $row['value'];
        }

        return $configs;

    }else{

        $row=D::find('config',[
                'status' => $status,
                'key'    => $configNames
            ]);

        return $row['value'];
    }
}
/**
 * [getTimeFormat 時間戳 格式化]
 * @Author   尹新斌
 * @DateTime 2017-07-20
 * @Function []
 * @param    string     $value [description]
 * @return   [type]            [description]
 */
function getTimeFormat($time)
{
    $h = str_pad(floor(($time / 3600)),2, "0", STR_PAD_LEFT);
    $m = str_pad(floor(($time % 3600) / 60),2, "0", STR_PAD_LEFT);
    $s = str_pad(floor(($time % 3600) % 60),2, "0", STR_PAD_LEFT);
    return $h.':'.$m.':'.$s;
}


//返回type类型名称
function getTypes($type){
    switch($type){
        case 'consume':
            $word = '消费返积分';
            break;
        case 'exchange':
            $word = '兑换电子券';
            break;
        case 'lvup':
            $word = '购买积分卡升级';
            break;
        case 'backs':
            $word = '退款减积分';
            break;
        case 'admin':
            $word = '管理员修改';
            break;
        case 'balance':
            $word = '余额支付';
            break;
        case 'wechat':
            $word = '微信支付';
            break;
        case 'outline':
            $word = '线下支付';
            break;
        case 'no':
            $word = '未支付';
            break;
        case 'pay':
            $word = '下单';
            break;
        case 'back':
            $word = '退款';
            break;
        case 'recharge':
            $word = '充值';
            break;
        case '1':
            $word = '已支付';
            break;
        case '2':
            $word = '已完成';
            break;
        case '3':
            $word = '已超时';
            break;
        case '4':
            $word = '已取消';
            break;
        case '5':
            $word = '退款审核中';
            break;
        case '6':
            $word = '已退款';
            break;
        case '7':
            $word = '已驳回';
            break;
        case '8':
            $word = '待付款';
            break;
        case '9':
            $word = '已入住';
            break;

    }
    return $word;
}
//根据订单状态  返回class名称
function showClass($status){
    switch($status){
        case '1':
            $class = 'cor_4';
            break;
        case '2':
            $class = 'cor_1';
            break;
        case '3':
            $class = 'cor_3';
            break;
        case '4':
            $class = 'cor_3';
            break;
        case '5':
            $class = 'cor_5';
            break;
        case '6':
            $class = 'cor_2';
            break;
        case '7':
            $class = 'cor_3';
            break;
        case '8':
            $class = '';
            break;
        case '9':
            $class = 'cor_9';
            break;

    }
    return $class;
}
/*
 *  封装  生成随机数函数
 *      $bit    获得几个的随机数
 * */
function get_random_number($bit){
    $arr=array();
    while(count($arr)<$bit)
    {
        //rand-随机产生一个0-9之间的数字
        $arr[]=rand(0,9);
        //去除数组元素中相同的值
        $arr=array_unique($arr);
    }
    //随机排列数组元素
    shuffle($arr);
    $str = '';
    foreach($arr as $val){
        $str .=$val;
    }
    return $str;
}
/*
* 	设置订单生成规则	字母 + 10位时间戳  + 5位随机数    共16位
* 	首字母大写  区分套餐（T）还是客房(K)订单  充值(C)
* */
function set_orderNo($type){
    return strtoupper($type).time().get_random_number(5);
}
/*
 *	$start-开始日期(标准日期格式)  $end-结束日期(标准日期格式)
 *	返回一个一维数组 从开始到结束的所有日期
 * */
function push_select_time($start,$end){
    $num = (strtotime($end,time()) - strtotime($start,time()))/86400;
    $arr = [];
    $x = 0;
    while($x<=$num){
        array_push($arr,date('Y-m-d',strtotime("$start +$x days")));
        $x++;
        if($x>$num){
            break;
        }
    }
    return $arr;
}
/*
 *  退款和后台管理员取消订单(此订单是已被驳回的订单,肯定是付过款的)操作
*       更新order表字段
 *      插入财务明细表
 *      插入退款表
 *      增加房间数量 && 客房  ？存在多天 ？则所有天数都要减1 :否则只减所选的天数
 *      判断此订单是否用了优惠券  ？ 返优惠券(更新电子券拥有表状态，删除电子券使用记录) : 不做操作
 *      判断支付方式 余额支付 ？  插入余额表记录  :  线下退款
 *      减去已返积分
 *
 *      现在需求是：
 *              如果已经被管理员驳回的退款申请订单，加上取消订单的按钮，逻辑和退款一致
 *      $id     订单ID
 * */
function do_order_back($id){
    $msg = D::find('Order',$id);
    switch ($msg['status']){
        case '5':
            $status = 6;
            break;
        case '7':
            $status = 4;
            break;
    }
    M('Order')->where("id=".$id)->setField('status',$status);
    //插入财务明细表
    $Finance = [
        'userID' => $msg['userID'],
        'orderNO' => $msg['orderNo'],
        'money' => $msg['price'],
        'type' => 'back',
        'createDate' => date('Y-m-d')
    ];
    M('Finance')->add($Finance);
    //插入退款表
    $back = [
        'orderId' => $msg['id'],
        'createTime' => time(),
        'money' => $msg['price']
    ];
    M('Drawback')->add($back);
    //判断此订单是否用了优惠券
    if($msg['coupon']){
        //更新使用记录 状态为3
        $sel = [
            'userID' => $msg['userID'],
            'cID' => $msg['coupon'],
            'type' => $msg['type'],
            'orderNO' => $msg['orderNo'],
            'roomID' => $msg['roomID'],
        ];
        M('CouponUsed')->where($sel)->setField('status',3);
        //更新电子券拥有表
        $map = [
            'userID' => $msg['userID'],
            'card' => $msg['coupon']
        ];
        D::set('CouponExchange.status',['where'=>$map],1);
    }
    if($msg['payType'] == 'balance'){
        $myBalance = [
            'userID' => $msg['userID'],
            'money' => $msg['price'],
            'orderNo' => $msg['orderNo'],
            'method' => 'back',
            'createTime' => time(),
            'updateTime' => time(),
            'status' => 1,
        ];
        M('Balance')->add($myBalance);
    }
    //增加房间剩余数量(实质是减去当天的  order_num的数量) 减积分
    if($msg['type'] == 'k'){
        $before_date = date('Y-m-d',strtotime("{$msg['outTime']} -1 day"));
        $arr = push_select_time($msg['inTime'],$before_date);
        $where['createDate'] = array('in',$arr);
        $where['roomID'] = $msg['roomID'];
        $where['type'] = $msg['type'];
        M('RoomDate')->where($where)->setDec('order_num',1);
        //减积分
        $sorce = D::field('House.sorce',$msg['roomID']);
        $sorce_data = [
            'userID' => $msg['userID'],
            'type' => 'backs',
            'sorce' => $sorce,
            'method' => 'sub',
            'createTime' => time(),
            'admin' => '0'
        ];
        M('UserSorce')->add($sorce_data);
    }else{
        $where['createDate'] = $msg['inTime'];
        $where['roomID'] = $msg['roomID'];
        $where['type'] = $msg['type'];
        M('RoomDate')->where($where)->setDec('order_num',$msg['num']);
        //减积分
        $sorce = D::field('Package.sorce',$msg['roomID']);
        $sorce_data = [
            'userID' => $msg['userID'],
            'type' => 'backs',
            'sorce' => $sorce,
            'method' => 'sub',
            'createTime' => time(),
            'admin' => '0'
        ];
        M('UserSorce')->add($sorce_data);
    }
}

/*
 * 	获得该房间的优惠券列表
 * 	$userID-用户id
 *  $house -房间信息
 *  $date-提交日期
 *  $type - coupon表的  套餐  客房字段
 * */
function get_coupon($userID,$house,$date,$type){
    $map = [
        'E.status' => 1,
        'E.userID' => $userID
    ];
    $coupon = D::get(['CouponExchange','E'],[
        'where' => $map,
        'join'	=> 'LEFT JOIN __COUPON__ C ON C.id = E.cID',
        'field'	=> 'E.*,C.money,C.exprie_start,C.exprie_end,hcate,tcate,C.notDate'
    ]);
    if($coupon){
        $arr = array_map(function($data)use($house,$date,$type){
            $data["$type"] = explode(',',$data["$type"]);
            $data['notDate'] = explode("\r\n",$data['notDate']);
            /*
             * 	首先判断该优惠券可不可以在该房间类型使用 ?  可以在查日期 : 若不可以直接就不查日期了
             * */
            if(in_array($house['category'],$data["$type"])){
                $data['allow'] = 'yes';
                //若该房间 允许使用优惠券 则判断  当前提交日期是否在  优惠券的限定时间内,且不在不可使用日期内
                if($date>=$data['exprie_start'] && $date<=$data['exprie_end'] && !in_array($date,$data['notDate'])){
                    $data['allow'] = "yes";
                }else{
                    $data['allow'] = 'no';
                }
            }else{
                $data['allow'] = 'no';
            }
            return $data;
        },$coupon);
    }else{
        $arr = [];
    }
    return $arr;
}
/*
 *  获取当天提交日期的房间数量 和 当日可用的优惠券
 *     参数： $post一维数组
 *       数组key:   date-标准日期格式
 *       数组key:   houseID-房间ID
 *       数组key:   type:k(客房) t(套餐)
 *       数组key:   userID:用户id
 *      返回一个二维数组
 * */
function get_postDate_roomNum_coupon($post){
    $dates = [
        [
            'date' => strtotime($post['date'].'-3 days'),
        ],
        [
            'date' => strtotime($post['date'].'-2 days'),
        ],
        [
            'date' => strtotime($post['date'].'-1 days'),
        ],
        [
            'date' => strtotime($post['date']),
        ],
        [
            'date' => strtotime($post['date'].'+1 days'),
        ],
        [
            'date' => strtotime($post['date'].'+2 days'),
        ],
        [
            'date' => strtotime($post['date'].'+3 days'),
        ],
    ];
    //在php中1-7的数字分别代表  周1-----周日
    $week = [
        1 => '一',
        2 => '二',
        3 => '三',
        4 => '四',
        5 => '五',
        6 => '六',
        7 => '日',
    ];
    //查询房间信息(这是h-代表客房  t-代表套餐)
    if($post['type'] == 'k'){
        $house = D::find("House",$post['houseID']);
        $parameter = 'hcate';
    }else{
        $house = D::find('Package',$post['houseID']);
        $parameter = 'tcate';
    }
    $data['db'] = array_map(function($data)use($week,$post,$house,$parameter){
        //获取当前日期
        $nowDate = strtotime(date('Y-m-d'),time());
        $date = $data['date'];
        //获取房间总数	查询提交时间的order数量
        $map['roomID'] = $post['houseID'];
        $map['createDate'] = date('Y-m-d',$date);
        $map['type'] = $post['type'];
        $num = D::find('RoomDate',['where'=>$map,'field'=>'IFNULL(order_num,0) order_num']);
        if($num['order_num'] && $num['order_num']>0){
            $houseNum = $house['total_num']-$num['order_num'];
        }else{
            $houseNum = $house['total_num'];
        }
        if($data['date']>=$nowDate){
            $str = $num['order_num'] == $house['total_num'] ? 'true' : 'false';
        }else{
            $str = 'no';
        }
        if($post['type'] == 'k'){
            //判断是否设置了价格模板
            $is = D::find('TempletePrice',[
                'where' => [
                    'roomID' => $post['houseID'],
                    'day' => date('Y-m-d',$date)
                ],
                'field' => 'price'
            ]);
            $money = $is['price'] ? $is['price'] : $house['money'];
        }else{
            $money = $house['packMoney'];
        }
        $data = [
            'month' => date('m月',$date),
            'day'   => date('d',$date),
            'week'  => $week[date('N',$date)],//N - 星期几
            'full'  => $str, //客满情况 满员写true[string] 不满则false	no-之前之间不可查询
            'date'	=> date('Y-m-d',$date),
            'num'	=> $houseNum,
            'price' => $money

        ];
        return $data;
    }, $dates);
    //查询当前用户已经拥有的且未使用的电子券
    $data['coupon'] = get_coupon($post['userID'],$house,$post['date'],$parameter);
    return $data;
}
/*
 *  可预定的最小-最大日期
 * */
function get_minDate_maxDate(){
    //设置可预订房间的最小与最大日期
    $minDate = date('Y-m-d');
    $maxDate = date('Y-m-d',strtotime("$minDate +3 month"));
    $myDate = [
        'min' => $minDate,
        'max' => $maxDate,
    ];
    return $myDate;
}

/*
 *  监控用户的级别变动情况
 *       达到规定的积分数则插入升级记录，更改用户现级别
 *       若出现积分不满足当前级别时则降级,并找到离此级别最近的级别,插入升级记录
 * */
function event_user_level($userID,$admin = ''){
    //用户积分
    $before = D::field('Users.nowLevel',$userID);
    $sorce  = D::find('UserSorce',[
        'where' => ['userID'=>$userID],
        'field' => "SUM(CASE WHEN method = 'plus' THEN sorce ELSE 0 END) up,SUM(CASE WHEN method = 'sub' THEN sorce ELSE 0 END) down"
    ]);
    //当前用户的总积分
    $all = $sorce['up'] - $sorce['down'];
    $map = [
        'sorce' => ['elt', $all],
        'status' => 1,
    ];
    //这块的逻辑就是拿用户现在总积分去查grades表,
    $grades = D::find('Grades',[
        'where'=> $map,
        'order'=>'sort desc',
    ]);
    $gid = $grades['id'] ? $grades['id'] : 0;
    if($gid != $before){
        $arr = [
            'userID' => $userID,
            'before' => $before,
            'after'  => $gid,
            'createTime' => time(),
            'admin' => $admin ? $admin : 0
        ];
        M('UserLvup')->add($arr);
        //更新现级别
        D::set('Users.nowLevel',$userID,$gid);
    }
}
/*	确认付款后	操作逻辑
	 *	1、更新订单状态	ms_order
	 * 	2、插入财务流水表 ms_finance   是否存在余额支付和微信混合支付的情况 ？ 订单金额-余额 : 订单金额
	 *	3、若存在电子券 ？ 插入电子券使用记录表(ms_coupon_used) && 更新电子卷拥有记录表(ms_coupon_exchange)  : 不做操作
	 * 	5、插入购买房间时间记录表(ms_room_date)	若是客房 && 选择多天入住 ？ 则要将所有选择的天数都插入,并order+1
	 * 	6、插入积分变更记录表 ms_user_sorce
	 *	$orderNo-订单号
	 * */
function checkTable($orderNo){
    $map['orderNo'] = $orderNo;
    $msg = D::find('Order',['where'=>$map]);
    $orderSave = [
        'status'=>1,
        'updateTime'=>NOW_TIME
    ];
    M('Order')->where($map)->setField($orderSave);
    //插入财务流水
    $Finance = [
        'userID' => $msg['userID'],
        'orderNO' => $orderNo,
        'money' => $msg['price'],
        'type' => 'pay',
        'createDate' => date('Y-m-d'),
    ];
    M('Finance')->add($Finance);
    //判断是否用了优惠券
    if($msg['coupon']){
        $coupon_used = [
            'userID' => $msg['userID'],
            'orderNO' => $orderNo,
            'roomID' => $msg['roomID'],
            'createTime' => strtotime(date('Y-m-d'),time()),
            'cID' => $msg['coupon'],
            'type' => $msg['type'],
            'status' => 1
        ];
        //插入电子券使用记录
        M('CouponUsed')->add($coupon_used);
        $save = [
            'status' => 2,
            'updateTime' => NOW_TIME,
        ];
        //更新电子券使用状态
        M('CouponExchange')->where("card=".$msg['coupon'])->setField($save);
    }
    $roomDate = search_room_date($msg['roomID'],$msg['type']);
    if($msg['type'] == 'k'){
        //客房-购买房间时间记录表 逻辑   新需求改动如果是订2-1,2-2连续的增加时应只增到退房日期的前一天
        $before_date = date('Y-m-d',strtotime("{$msg['outTime']} -1 day"));
        $arr = push_select_time($msg['inTime'],$before_date);
        foreach($arr as $key => $val){
            if(in_array($val,$roomDate)){
                $save_date[] = $val;
            }else{
                $add_date[$key]['createDate'] = $val;
            }
        }
        //已经存在日期,则更新
        if($save_date){
            $save['createDate'] = implode(',',$save_date);
            $save['type'] = 'k';
            $save['roomID'] = $msg['roomID'];
            M('RoomDate')->where($save)->setInc('order_num',1);
        }
        //不存在的日期,则新增
        if($add_date){
            $add_date = array_map(function($data)use($msg){
                $data['roomID'] = $msg['roomID'];
                $data['order_num'] = 1;
                $data['type'] = 'k';
                return $data;
            },$add_date);
            M('RoomDate')->addAll($add_date);
        }
    }else{
        //套餐-购买房间时间记录表 逻辑
        if(in_array($msg['inTime'],$roomDate)){
            $save = [
                'createDate' => $msg['inTime'],
                'type' => 't'
            ];
            M('RoomDate')->where($save)->setInc('order_num',$msg['num']);
        }else{
            $add = [
                'createDate' => $msg['inTime'],
                'order_num' => $msg['num'],
                'type' => 't',
                'roomID' => $msg['roomID']
            ];
            M('RoomDate')->add($add);
        }
    }
    //插入积分变更记录表
    if($msg['type'] == 'k'){
        $sorce = D::field('House.sorce',$msg['roomID']);
    }else{
        $sorce = D::field('Package.sorce',$msg['roomID']);
    }
    $sorce_data = [
        'userID' => $msg['userID'],
        'type' => 'consume',
        'sorce' => $sorce,
        'method' => 'plus',
        'createTime' => time(),
        'admin' => '0'
    ];
    M('UserSorce')->add($sorce_data);
}
/*
 * 	查询购买房间时间记录表  数据
 * 	$roomID 房间id
 * 	$type   h-客房  t-套餐
 * 	返回一个  一维数组
 * */
function search_room_date($roomID,$type){
    $search = [
        'type' => $type,
        'roomID' => $roomID
    ];
    $roomDate = D::lists('roomDate','createDate',['where'=>$search]);
    return $roomDate;
}
/*
 * 	判断所选日期内是否存在满房的情况
 * 	若存在 return false  否则  return true
 * 	$arr 数组 [type,roomID]
 *	$obj 一维数组 或  字符串
 * */
function is_house_all($obj,$arr){
    $bool = true;
    if(is_array($obj) === true && $arr['type'] == 'k'){
        $house_num = D::field('House.total_num',$arr['roomID']);
        $sel = [
            'roomID' => $arr['roomID'],
            'type' => $arr['type'],
            'order_num' => $house_num
        ];
        $arrs = D::lists('RoomDate','createDate',['where'=>$sel]);
        foreach ($obj as $val){
            if(in_array($val,$arrs)){
                $bool = false;
            }
        }
    }else{
        $pack_num = D::field('Package.total_num',$arr['roomID']);
        $sel = [
            'roomID' => $arr['roomID'],
            'type' => $arr['type'],
            'order_num' => $pack_num
        ];
        $arrs = D::lists('RoomDate','createDate',['where'=>$sel]);
        if(in_array($obj,$arrs)){
            $bool = false;
        }
    }
    return $bool;
}
/*
 *  获取规定开始到结束日期内的  标准日期格式数组
 *      $start  开始日期
 *      $end    结束日期
 *      返回一个二维数组    type-1: 周1--周五  type-2: 六日价格
 * */
function get_start_end_week($start,$end){
    $arr = push_select_time($start,$end);
    foreach($arr as $key => $val){
        $data[$key]['week'] = date('w',strtotime($val));
        $data[$key]['day'] = $val;
    }
    $array = [];
    foreach($data as $kk => $value){
        if($value['week'] == '0' || $value['week'] == '5' || $value['week'] == '6') {
            $array[$kk]['day'] = $value['day'];
            $array[$kk]['type'] = 2;
        }else{
            $array[$kk]['day'] = $value['day'];
            $array[$kk]['type'] = 1;
        }
    }
    return $array;
}
/*
 * 	设置插入订单价格
* 		首先判断  该订单是否存在优惠券
* 		其次判断  该订单是客房 还是 套餐的订单
* 				 客房：根据开始-结束求出天数 则价格为天数*房间单价
* 				 套餐：根据购买的份数 则价格为  份数*房间单价
* 		则该订单的总价格为：
 * 		电子券 && 客房 ？ (天数*房间单价) - 电子券金额  : (天数*房间单价)
* 		电子券 && 套餐 ？ (份数*房间单价) - 电子券金额  : (份数*房间单价)
* 	    新需求  仅用于客房 每天都可能设置房价
* */
function get_order_price($post){
    if($post['type'] == 'k'){
        $start = strtotime($post['inTime']);//入住时间
        $end = strtotime($post['outTime']);//离开时间
        $oldMoney = D::field('House.money',$post['roomID']);//房间单价
        /*
         * 	这里判断$num>=2是因为 现在需求是2017-01-01-2017-01-02这算一天的房间单价 并且算房间价格时应算入住时间的价格
         *	2017-01-01-2017-01-03这算两天的钱(2017-01-01和2017-01-02的钱)
         * */
        $num = intval(($end-$start)/86400);//入住天数
        //判断后台是否设置了价格模板 这里就拿开始入住时间查找就行
        if($num >=2){
            $search = date('Y-m-d',strtotime("{$post['outTime']} -1 day"));
            $sel = [
                'day' => array('between',[$post['inTime'],$search]),
                'roomID' => $post['roomID']
            ];
        }else{
            $sel = [
                'day' => $post['inTime'],
                'roomID' => $post['roomID']
            ];
        }
        $is = D::get('TempletePrice',[
            'where' => $sel
        ]);
        //这里先计算出所选日期的总价格
        if($is){
            $more = D::field('TempletePrice.SUM(price)',['where'=>$sel]);
            $less = D::field('TempletePrice.price',['where'=>$sel]);
            $money = $num >= 2 ? $more : $less;
        }else{
            $money = $num >= 2 ? ($oldMoney*$num) : $oldMoney;
        }
        //判断是否使用了优惠券		存在 ？ 总价格-优惠券价格 : 总价格
        if(array_key_exists('coupon',$post) === true && $post['coupon']){
            $couponID = D::field('CouponExchange.cID',['where'=>['card'=>$post['coupon']]]);
            $couponMoney = D::field('Coupon.money',$couponID);
            $price = $money-$couponMoney;
        }else{
            $price = $money;
        }
    }else{
        $money = D::field('Package.packMoney',$post['roomID']);//套餐单价
        if(array_key_exists('coupon',$post) === true && $post['coupon']){
            $couponID = D::field('CouponExchange.cID',['where'=>['card'=>$post['coupon']]]);
            $couponMoney = D::field('Coupon.money',$couponID);
            $price = $post['num'] > 1 ? ($money*$post['num'] - $couponMoney) : ($money-$couponMoney);
        }else{
            $price = $post['num'] > 1 ? $money*$post['num'] : $money;
        }
    }
    return $price;
}