<?php
namespace Home\Controller;
use Think\Controller;
use Think\D;
//订单统计
class OrderCountController extends CommonController {
	public $model = ['Orderflow','A'];
	public function _map(&$data)
	{
		if ( I('start') || I('end') ) {
			$map['A.orderhotel_time'] = get_selectTime( I('start'),I('end') );
		}
		if(I('select')){
			$map['A.roomName'] = I('select');
		}
		if(I('title')){
			$map['A.orderhotel_no'] = I('select');
		}
		$map['A.hotel'] = session('hotel_user.hotel');
		$data = [
			'where' => $map,
			'join'	=> [
				'LEFT JOIN __ORDER_HOTEL__ OH ON OH.id = A.orderhotel_id',
				'LEFT JOIN __ORDER__ O ON O.id = OH.orderId',
				'LEFT JOIN __HOTEL_ROOMS__ C ON C.id = O.room'
			],
			'field'	=> 'A.*,C.price as buy_money',
			'order' => 'orderhotel_time DESC'
		];
	}
	public function _before_index(){
		$roomType = D::get(['HotelRooms','A'],[
			'where' => ['hotel'=>session('hotel_user.hotel')],
			'field' => 'A.room,B.roomName',
			'join'  => 'LEFT JOIN __ROOMS__ B ON B.id = A.room'
		]);
		$this->assign('type',$roomType);
	}
	public function index()
	{
		$info = http_build_query(I('get.'));
		$this->assign('info',$info);
		parent::index();

	}
	public function export(){
		$db = parent::index(true);
		foreach($db as $key=>$val){
			$db[$key]['orderhotel_time'] = date_out($db[$key]['orderhotel_time']);
		}
		$xlsName  = date('Y-m-d_H:i:s',time()).'订单统计';
		$xlsCell  = array(
			array('orderhotel_no','订单编号'),
			array('orderhotel_time','日期'),
			array('roomName','房间类型'),
			array('buy_money','交易金额')
		);
		export_Excel($xlsName,$xlsCell,$db);
	}
}