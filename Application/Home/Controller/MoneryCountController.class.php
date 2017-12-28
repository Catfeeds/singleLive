<?php
namespace Home\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;

/*
 * 	财务统计
 	这次修改,只要是用户花钱了,就会进入order_money 
 	不管这个用户入住或没入住,都算做财务
 	并且期间如果出现,高价钱——>换-->低价钱,不用花钱的情况，这里不做显示
 * 	FROM_UNIXTIME mysql函数将unix时间戳转换为标准时间格式
 * */
class MoneryCountController extends CommonController {
	public function _map(&$data){
		if(I('start')){
			$map['B.dateTime'] = I('start');
		}
		if(I('end')){
			$map['B.dateTime'] = I('end');
		}
		if(I('start') && I('end')){
			$map['B.dateTime'] = array('between',array(I('start'),I('e nd')));
		}
		$sql = D::get(['OrderMoney','a'],[
			'where' => ['b.hotel' => session('hotel_user.hotel')],
			'field'	=> 'a.add_time time,a.money total',
			'join'	=> [
				'LEFT JOIN __ORDER__ b ON b.id = a.orderId',
			],
		],false);
		$sql2 = D::get(['Order', 'O'],[
				'where' => ['O.hotel' => session('hotel_user.hotel'), 'O.status'=>'3'],
				'join' => [
					'LEFT JOIN __ORDER_MONEY__ OM ON OM.orderId=O.id'
				],
				'field' => 'O.updateTime time, -(OM.money) total',
			],false);
		$lastSQL = '( '.$sql.' ) UNION ALL ('.$sql2.' )';
		$sql_time = D::get('',[
			'table' => '('.$lastSQL.') A',
			'field' => "FROM_UNIXTIME( A.time, '%Y-%m-%d' ) dateTime,SUM(total) total",
			'group' => 'dateTime'
		],false);
		$data = [
			'table' => '('.$sql_time.') B',
			'where' => $map,
			'order' => 'B.dateTime DESC'
		];
	}
	public function index()
	{
		$info = http_build_query(I('get.'));
		$this->assign('info',$info);
		parent::index();
	}
	//财务导出
	public function index_export(){
		$db = parent::index(true);
		$xlsName  = date('Y-m-d_H:i:s',time()).'财务统计';
		$xlsCell  = array(
			array('dateTime','日期'),
			array('total','金额收入')
		);
		export_Excel($xlsName,$xlsCell,$db);
	}
	//查看明细
	public function look(){
		if(I('title')){
			$map['e.userName'] = array('like','%'.I('title').'%');
		}
		if(I('status') == '10'){
			$map['_string'] = "ISNULL(f.`status`)";
		}elseif(I('status')){
			$map['f.status'] = I('status');
		}
		$start = strtotime(I('date').' 00:00:00');
		$end = strtotime(I('date').' 23:59:59');
		$map['A.add_time'] = array('between',array($start,$end));
		$map['b.hotel'] = session('hotel_user.hotel');
		$sql1 = D::get(['OrderMoney','A'],[
			'where' => $map,
			'field'	=> 'A.type, A.money,b.hotel,b.postal,d.roomName,e.realname,f.status, A.add_time updateTime,b.status state',
			'join'	=> [
				'LEFT JOIN __POSTAL_WAIT__ f ON f.`order` = A.id',
				'LEFT JOIN __ORDER__ b ON b.id = A.orderId',
				'LEFT JOIN __USERS__ e ON e.id = b.userId',
				'LEFT JOIN __HOTEL_ROOMS__ c ON c.id = b.room',
				'LEFT JOIN __ROOMS__ d ON d.id = c.room'
			],
			'order' => 'A.add_time DESC'
		],false);
		$where['b.updateTime'] = array('between',array($start,$end));
		$where['b.hotel'] = session('hotel_user.hotel');
		$where['b.status'] = 3;
		$sql2 = D::get(['Order','b'],[
			'where' => $where,
			'field'	=> "'退款' type, -(OM.money),b.hotel,b.postal,d.roomName,e.realname,f.status,b.updateTime,b.status state",
			'join'	=> [
				'LEFT JOIN __ORDER_MONEY__ OM ON OM.orderId=b.id',
				'LEFT JOIN __POSTAL_WAIT__ f ON f.`order` = b.id',
				'LEFT JOIN __USERS__ e ON e.id = b.userId',
				'LEFT JOIN __HOTEL_ROOMS__ c ON c.id = b.room',
				'LEFT JOIN __ROOMS__ d ON d.id = c.room'
			],
			'order' => 'b.updateTime DESC'
		],false);
		$db = D::get('', [
				'table' => '( ( '.$sql1.' ) UNION ALL ('.$sql2.' ) ) A',
				'field' => 'A.*',
				'order' => 'A.updateTime DESC'
			]);
		foreach($db as $key => &$val){
			switch ($db[$key]['type']) {
				case 'buy':
						$db[$key]['type_name'] = '直接购买';
					break;
				case 'change':
						$db[$key]['type_name'] = '换房';
					break;
				case 'continue':
						$db[$key]['type_name'] = '续时';
					break;	
				default:
						$db[$key]['type_name'] = '退款';
						$db[$key]['status'] = '8';
					break;
			}
		}
		$info = http_build_query(I('get.'));
		$this->assign('info',$info);
		$this->assign('db',$db);
		$this->display();
	}
	//明细导出
	public function look_export(){
		if(I('title')){
			$map['e.userName'] = array('like','%'.I('title').'%');
		}
		if(I('status') == '10'){
			$map['_string'] = "ISNULL(f.`status`)";
		}elseif(I('status')){
			$map['f.status'] = I('status');
		}
		$start = strtotime(I('date').' 00:00:00');
		$end = strtotime(I('date').' 23:59:59');
		$map['A.add_time'] = array('between',array($start,$end));
		$map['b.hotel'] = session('hotel_user.hotel');


		$sql1 = D::get(['OrderMoney','A'],[
			'where' => $map,
			'field'	=> 'A.type, A.money,b.hotel,b.postal,d.roomName,e.realname,f.status, A.add_time updateTime,b.status state',
			'join'	=> [
				'LEFT JOIN __POSTAL_WAIT__ f ON f.`order` = A.id',
				'LEFT JOIN __ORDER__ b ON b.id = A.orderId',
				'LEFT JOIN __USERS__ e ON e.id = b.userId',
				'LEFT JOIN __HOTEL_ROOMS__ c ON c.id = b.room',
				'LEFT JOIN __ROOMS__ d ON d.id = c.room'
			],
			'order' => 'A.add_time DESC'
		],false);
		$where['b.updateTime'] = array('between',array($start,$end));
		$where['b.hotel'] = session('hotel_user.hotel');
		$where['b.status'] = 3;
		$sql2 = D::get(['Order','b'],[
			'where' => $where,
			'field'	=> "'退款' type, -(OM.money),b.hotel,b.postal,d.roomName,e.realname,f.status,b.updateTime,b.status state",
			'join'	=> [
				'LEFT JOIN __ORDER_MONEY__ OM ON OM.orderId=b.id',
				'LEFT JOIN __POSTAL_WAIT__ f ON f.`order` = b.id',
				'LEFT JOIN __USERS__ e ON e.id = b.userId',
				'LEFT JOIN __HOTEL_ROOMS__ c ON c.id = b.room',
				'LEFT JOIN __ROOMS__ d ON d.id = c.room'
			],
			'order' => 'b.updateTime DESC'
		],false);
		$db = D::get('', [
				'table' => '( ( '.$sql1.' ) UNION ALL ('.$sql2.' ) ) A',
				'field' => 'A.*',
				'order' => 'A.updateTime DESC'
			]);
		foreach($db as $key => &$val){

			switch ($val['type']) {
				case 'buy':
						$val['type_name'] = '直接购买';
					break;
				case 'change':
						$val['type_name'] = '换房';
					break;
				case 'continue':
						$val['type_name'] = '续时';
					break;	
				default:
						$val['type_name'] = '退款';
						$val['status'] = '8';
					break;
			}

			if($val['state'] == 3 && $val['status'] != 8){
				$val['status'] = '已退款';
			}else if($val['status'] == 0){
				$val['status'] = '未提现';
			}else if($val['status'] == 8){
				$val['status'] = '--';
			}else if($val['status'] == 1){
				$val['status'] = '已提现';
			}else if($val['status'] == 9){
				$val['status'] = '未入住';
			}else{
				$val['status'] = '提现申请中';
			}
			
			$val['updateTime'] = date('Y-m-d_H:i:s', $val['updateTime']);
		}

		$xlsName  = I('date').'财务明细';
		$xlsCell  = array(
			array('updateTime','日期'),
			array('roomName','房间类型'),
			array('realname','用户姓名'),
			array('money','订单金额'),
			array('type_name','交易类型'),
			array('status','状态')
		);
		export_Excel($xlsName,$xlsCell,$db);
	}
}