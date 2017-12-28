<?php
namespace Home\Controller;
use Think\Controller;
use Think\D;
//提现管理
class PostalController extends CommonController {
	public $model = ['Postal','P'];
	public function _map(&$data)
	{
		$map["_string"] = 'hotel='.session('hotel_user.hotel').'';

		if(I('startTime')){
            $map["_string"] .= ' And applydate >= '.strtotime(I('startTime')).'';
        }
        if(I('endTime')){
            $map["_string"] .= ' And applydate <= '.strtotime(I('endTime')).'';          
        }

        $data = [
        	'where' => $map,
        	'order' => 'applydate DESC'
        ];
	}
	//提现管理
	public function index()
	{
		$db = parent::index(function($data){
			$data['applydate'] = date('Y-m-d',$data['applydate']);
			switch ($data['status']) {
				case '0': $data['status']='正在审核'; break;			
				case '1': $data['status']='通过';break;
				default:  $data['status']='失败';break;
			}
			return $data;
		});
	}
	//新增提现请求
	public function index_add()
	{
		$date = D::get('postaldate');		
		//需求更改 新增提现待提现表 PostalWait 所有应该提现的订单都会插入PotalWait表
		//所以直接查询 PostalWait 表中 status为0 的数据即可
		$map['PW.sataus'] = 0;
		$map['PW.hotel'] = session('hotel_user.hotel');
		$map['PW.amount'] = ['gt', 0];
		$rows = D::get(['PostalWait','PW'],[
					'where' => $map,
					'join' => [
						'LEFT JOIN __ORDER_MONEY__ OM ON OM.id=PW.order',
						'LEFT JOIN __ORDER__ O ON O.id=OM.orderId',
					],
					'group' => 'OM.orderId',
					'field' => 'PW.id,OM.orderId orderId',
				]);

		//将所有需要提现的订单ID存储为session
		session('orderID',$rows);
		//已完成的订单总数
		$orderCount = count($rows);

		//循环将所有订单的价格相加
		$money = D::field("PostalWait.IFNULL(SUM(`amount`),'0')",[
					'where' => 'status=0 and hotel='.session('hotel_user.hotel').'',
				]);

		$money = sprintf("%.2f", $money);
	
		$type = "submit";
		if(!D::count('postaldate','date='.date('d').'')){
			$type = "button";
			$style = "style='background-color:#999;'";
		}

		$this->assign('type',$type);
		$this->assign('style',$style);
		$this->assign('monery',$money);
		$this->assign('orderCount',$orderCount);
		$this->assign('rows',$date);
		$this->display();		
	}
	//提现请求执行
	public function postalAddDo()
	{
		if(!I('number')||!I('monery')){
			$this->error('抱歉，您当前可提现的订单金额不足');
		}
		$row = D::find('hotels',session('hotel_user.hotel'));
		$number = "T".session('hotel_user.hotel').time();
		$Ary = [
			'hotel' => session('hotel_user.hotel'), //hotel::id
			'number' => $number,		//提现编号
			'info' => I('post.info'),   //提现说明
			'applydate' => time(), 		//申请日期
			'count' => I('number'), 	//订单总数
			'monery' => I('monery'),    //订单总金额
			'status' => '0'				//状态 ：0 正在审核
		];

		$postalID=D::add('postal',$Ary);
		//将订单状态改为提现中
		foreach (session('orderID') as $key => $value) {
			D::add('Postalrecord',['postal' => $postalID,'order' => $value['orderId']]);
			D::set('PostalWait.status',$value['id'],'2');
		}

		if($postalID>0){
			$this->success('提现申请已经提交,请等待审核',U('Postal/index'));
		}else{
			$this->error('网络错误，请刷新后重试');
		}	
	}
	//订单明细
	public function index_edit()
	{

		$map['P.postal'] = ['eq',I('id')];
		if ( I('title') ) {
			$map['CONCAT(O.no,O.realname)'] = ['like','%'.I('title').'%'];
		}
		if ( I('startTime') || I('endTime') ) {
			$map['O.createTime'] = get_selectTime( I('startTime'),I('endTime') );
		}

		$rows = D::get(['Postalrecord','P'],[
						'where' => $map,
						'join'  => [
							'LEFT JOIN __POSTAL_WAIT__ PW on P.`order`=PW.id',
							'LEFT JOIN __ORDER__ O on PW.`order`=O.id',
							'LEFT JOIN __USERS__ U on U.id=O.userId',
						],
						'field' => 'O.*,PW.amount Pamount,U.realname userName'
					]);

		foreach ($rows as $key => $row) {
			$rows[$key]['createTime'] = date('Y-m-d',$row['createTime']);
			$rows[$key]['monery'] =  round($row['used']*$row['amount']);
		}

		$this->assign('data',$rows);
		$this->display();
	}
	//提现说明
	public function index_info()
	{
		$db = D::find('postal',I('id'));

		$this->assign('db',$db);
		$this->display();
	}
	//提现退回原因
	public function index_false()
	{
		$db = D::find('postal',I('id'));

		$this->assign('db',$db);
		$this->display();
	}
	//提现记录导出
	public function excel()
	{
		$db = parent::index(true);

		foreach ($db as $key => $data) {

			$db[$key]['applydate'] = date('Y-m-d',$data['applydate']);

			switch ($data['status']) {
				case '0': $db[$key]['status']='正在审核'; break;			
				case '1': $db[$key]['status']='通过';break;
				default:  $db[$key]['status']='失败';break;
			}
		}

		$dbName = array(
			array('applydate','申请日期'),
			array('passdate','审核日期'),
			array('count','订单总数'),
			array('monery','提现金额'),
			array('info','提现说明'),
			array('return','退回原因'),
			array('status','状态')
			);
		$xlsName = session('hotel_user.hotelName').'_'.date('Ymd');

		export_Excel($xlsName,$dbName,$db);
	}
}