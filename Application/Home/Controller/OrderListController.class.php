<?php
namespace Home\Controller;
use Think\Controller;
use Think\D;
//订单列表
class OrderListController extends CommonController {
	public $model = ['OrderHotel','OH'];
	public function _map(&$data)
	{
		if ( I('title') ) {
			$map['CONCAT(OH.no,U.realname,U.mobile)'] = ['like','%'.I('title').'%'];
		}
		if ( I('start') || I('end') ) {
			$map['OH.createTime'] = get_selectTime( I('start'),I('end') );
		}
		if (I('status') || I('status') == '0') {
			$map['OH.status'] = I('status');
		}
		$map['A.Hotel'] = session('hotel_user.hotel');
		$data = [
		'where' => $map,
		'field' => 'OH.*,U.realname,U.nickname,U.mobile',
		'join'  => [
			'LEFT JOIN __USERS__ U ON U.id = OH.userId',
			'LEFT JOIN __ORDER__ A ON A.id = OH.orderId'
			],
		'order' => 'OH.createTime DESC',
		];
	}
	/**
	 * [index description]
	 * @Author   尹新斌
	 * @DateTime 2017-07-19
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function index()
	{
		$info = http_build_query(I('get.'));
		$this->assign('info',$info);
		parent::index('checkData');

	}
	public function checkData( $data ){
		$order = D::find(['Order','O'],[
			'where' => ['O.id' => $data['orderId']],
			'join'  => 'LEFT JOIN __HOTEL_ROOMS__ R ON R.id = O.room',
			'field' => 'R.*,(O.duration - O.used) have',
		]);
		$data['roomType'] = D::field('Rooms.roomName',$order['room']);
		$data['have'] = $order['have'];
		$data['min'] = $order['minimum'];
		$data['minute'] = $order['minute'];
		$data['now'] = NOW_TIME - $data['startTime'];
		$h = floor(($data['endTime'] - $data['startTime']) / 3600);
		$m = floor((($data['endTime'] - $data['startTime'])% 3600) / 60);
		$s = floor((($data['endTime'] - $data['startTime'])% 3600) % 60);
		$data['old'] = ($data['status'] == 1)?str_pad($h,2, "0", STR_PAD_LEFT).':'.str_pad($m,2, "0", STR_PAD_LEFT).':'.str_pad($s,2, "0", STR_PAD_LEFT):'00:00:00';
		if ($data['status'] == 1) {
			if ($h < $order['minimum']) {
				$data['use'] = $order['minimum'];
			}else{
				$data['use'] = ($order['minute'] <= $m )?$h + 1:$h;
			}
		}else{
			$data['use'] = 0;
		}
		return $data;
	}
	/**
	 * [backOrder 取消订单]
	 * @Author   尹新斌
	 * @DateTime 2017-07-19
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function backOrder()
	{
		$id = I('id');
		$now = D::find('OrderHotel',$id);
		if ($now['status'] != '0') {
			$this->error('当前订单无法取消，请刷新页面重试');exit;
		}
		$num = D::save('OrderHotel',$id,[
			'status' => 8,
			]);
		if ($num) {
			$flag = D::save('Order',$now['orderId'],[
				'status' => '0',
				'updateTime' => NOW_TIME,
				]);
			D::add('OrderBack',['reason' => I('prompt'),'orderId' => $id]);
			$this->success('订单已取消');
		}else{
			$this->error('当前订单无法取消，请刷新页面重试');exit;
		}
	}
	/**
	 * [startTime 开始计时]
	 * @Author   尹新斌
	 * @DateTime 2017-07-20
	 * @Function []
	 * @param    string     $value [description]
	 * @return   [type]            [description]
	 */
	public function startTime()
	{
		$id = I('id');
		$now = D::find('OrderHotel',$id);
		if ($now['status'] != '0') {
			$this->error('当前订单无法操作，请刷新页面重试');exit;
		}
		$num = D::save('OrderHotel',$id,[
			'status' => 2,
			'startTime' => NOW_TIME,
			]);
		if ($num) {
			$flag = D::save('Order',$now['orderId'],[
				'status' => '2',
				'updateTime' => NOW_TIME,
				]);
			D::add('OrderBack',['reason' => I('prompt'),'orderId' => $id]);
			/*
				点击开始计时  向用户发送消息提示
			*/
			$orderId = I('orderId');
			$msg = search_order_msg($orderId);
			send_check_msg($msg['id'],$msg['hotelName'],$msg['roomName']);

			$this->success('计时开始',U('OrderList/index'));
		}else{
			$this->error('当前订单无法操作，请刷新页面重试');exit;
		}
	}
	/**
	 * [endTime 退房操作 结算]
	 * @Author   尹新斌
	 * @DateTime 2017-07-20
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function endTime()
	{
		$id = I('id');
		$now = D::find('OrderHotel',$id);
		if ($now['status'] != '2') {
			$this->error('当前订单无法操作，请刷新页面重试');exit;
		}
		$longTime = NOW_TIME - $now['startTime'];//获取共计居住多少秒
		$hours = floor($longTime / 3600);//计算出小时
		$minus = floor(($longTime%3600) / 60);//计算出分钟
		$roomId = D::find('Order',$now['orderId']);
		$config = D::find('HotelRooms',$roomId['room']);//HotelRooms房间设置
		/*计算扣除时间*/
		if ($hours < $config['minimum']) {
			$inTime = $config['minimum'];
		}else{
			if ($minus >= $config['minute']) {
				$inTime = $hours + 1;
			}else{
				$inTime = $hours;
			}
		}
		//更新酒店端订单表记录
		$nowTime = NOW_TIME;
		$num = D::save('OrderHotel',$id,[
			'status' => 1,
			'endTime' => $nowTime,
			'used' => $inTime
			]);

		//已用时长和剩余可用时长判断 订单流水表金额不能超过下单时的金额

		$uTime = $inTime;
		if($inTime>$roomId['duration']-$roomId['used']){
			$uTime = $roomId['duration']-$roomId['used'];
		}

		/*
		 * 	1、计算扣除时间
		 * 	2、为了配合手机端,用户退款，只要入住过就不能退款，所以要更新入住状态字段--checkIn
		*/
		$save_data = [
			'used' => $roomId['used'] + $inTime,
			'checkIn' => 1
		];
		if ($save_data['used'] >= $roomId['duration']) {
			$save_data['status'] = 1;
		}else{
			$save_data['status'] = 0;
		}
		/*
			为了配合新增需求：提现 酒店端订单完成后要将Order表订单的累计金额清零
			代表 此单已入住过 无需再次提现 
			同时将 PostalWait表 的数据状态变为1 代表 此单正等待提现
		 */
		
		$row = D::find('Order',$now['orderId']);
		if($row['all_amount'] > 0){

			$orderMonerId = D::lists('OrderMoney', 'id', ['orderId' => $now['orderId']]);
		    D::save('PostalWait', ['order' => ['in', $orderMonerId],'status' => '9'], ['status' => 0]);

			$save_data['all_amount'] = 0;
		}

		$flag = D::save('Order',$now['orderId'],$save_data);
		if ($flag!==false) {
			$roomName = D::find(['Order','O'],[
			'where' => ['O.id'=>$now['orderId']],
			'join' => [
				'LEFT JOIN __HOTEL_ROOMS__ HR on HR.id=O.room',
				'LEFT JOIN __ROOMS__ R on R.id=HR.room',
				'LEFT JOIN __ORDER_MONEY__ OM ON O.id=OM.orderId',
				],
			'field' => 'R.roomName'
			]);

			$useMoney = ($uTime/24*$config['price']);
			// //此次入住结算的费用
			// $lastMoney = D::field("Orderflow.IFNULL(SUM(money),'0')",['orderhotel_id'=>$now['orderId']]);
			// //此订单已经结算过的金额
			// $Money = D::field("OrderMoney.IFNULL(SUM(money),'0')",['orderId'=>$now['orderId']]);
			// //此订单的费用，一条订单可能多条记录，因为有续时操作

			// if($useMoney + $lastMoney > $Money){
			// 	$useMoney = $Money -  $lastMoney > 0 ? $Money -  $lastMoney : 0;
			// }

			//插入订单流水记录	
			$msg  = array(
				'orderhotel_id' => $now['id'],
				'hotel' => $roomId['hotel'],
				'orderhotel_time' => $roomId['createTime'],
				/*	此算法是为了 如果入住时间正好为整数的清况下,就直接吧房间单价赋值给它
					若不为整数，则拿对24去余之后的数，乘以单价
				 * */
				// 'money' => ($uTime/24*$config['price'])+($uTime%24*$roomId['amount']),
				 
				/*	需求更改，money不能超过用户花的钱
				 * */
				'money' => $useMoney,
				'orderhotel_used' => $inTime,
				'orderhotel_no' => $now['no'],
				'hotelName' => D::field('Hotels.hotelName',$roomId['hotel']),
				'roomName' => $roomName['roomName'],
				'startTime' => $now['startTime'],
				'endTime' => $nowTime,
				'userName' => D::field('Users.realname',$now['userId']),
				'status' => 0
			);

			M('Orderflow')->add($msg);
			/*
				点击退房时，给用户发送消息
			*/
			$orderId = I('orderId');
			$msg = search_order_msg($orderId);
			$plusTime = D::field('Order.duration', $orderId) - D::field('Order.used', $orderId);
			$plusTime = $plusTime < 0 ? 0 : $plusTime;
			send_out_msg($msg['id'], $msg['hotelName'], $inTime, $plusTime);
			$this->success('退房成功',U('OrderList/index'));
		}else{
			$this->error('当前订单无法操作，请刷新页面重试');exit;
		}
	}
	//导出
	public function export(){
		$db = array_map([$this,'checkData'],parent::index(true));
		foreach($db as  $key => $val){
			$db[$key]['min'] = $db[$key]['min'].'小时';
			$db[$key]['have'] = formatTime($db[$key]['have']);
			$db[$key]['createTime'] = date_out($db[$key]['createTime']);
			$db[$key]['startTime'] = date_out($db[$key]['startTime'],'未计时');
		}
		$xlsName  = date('Y-m-d_H:i:s',time()).'订单列表';
		$xlsCell  = array(
			array('no','订单编号'),
			array('realname','姓名'),
			array('mobile','电话'),
			array('roomType','房间类型'),
			array('min','最低入住时长'),
			array('have','可用时长'),
			array('createTime','订单日期'),
			array('startTime','入住时间'),
			array('old','入住时长'),
			array('use','折算时间'),
		);
		export_Excel($xlsName,$xlsCell,$db);
	}

}