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
    
/*生成7位随机数*/
/*function random_num(){
    return str_pad(mt_rand(0, 999999999), 9, "0", STR_PAD_BOTH);
}*/

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

