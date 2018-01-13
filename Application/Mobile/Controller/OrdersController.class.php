<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\D;
class OrdersController extends CommonController{
	public static $login = true;
	/**
	 * [index 订单]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function index()
	{
		$this->display();
	}
	/**
	 * [prepareOrder 房间预订]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 * 下单逻辑  zhenhong~
	 */
	public function prepareOrder()
	{
		$houseID = I('id');
		//查询当前房间信息
		$house = D::find('House',$houseID);
		//设置可预订房间的最小与最大日期 及查看时间的最小最大日期
		$minDate = date('Y-m-d');// 查看最小
		$maxDate = date('Y-m-d',strtotime("$minDate +6 month"));// 查看最大
		$start = date('Y-m-d',strtotime("$minDate +1 days"));
		$end = date('Y-m-d',strtotime("$start +6 month"));
		$myDate = [
			'min' => $minDate,
			'max' => $maxDate,
			'start' => $start,
			'end' => $end
		];
		$this->assign('myDate',$myDate);
		$this->assign('house',$house);
		$this->display();
	}
	/**
	 * [prepareOrder 餐饮下单]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function prepareOrderPackage()
	{
		$this->display();
	}
	/**
	 * [getStrtotime 获取时间日期格式信息]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-11
	 * @Function []
	 * @param    [type]     $time   [选中日期（时间戳）]
	 * @return   [type]             [description]
	 */
	public function getStrtotime()
	{
		$post = I('post.');
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
		//查询房间信息
		$house = D::find("House",$post['houseID']);
		$data['db'] = array_map(function($data)use($week,$post,$house){
			//获取当前日期
			$nowDate = strtotime(date('Y-m-d'),time());
			$date = $data['date'];
			//获取房间总数	查询提交时间的order数量
			$map['roomID'] = $post['houseID'];
			$map['createDate'] = date('Y-m-d',$date);
			$num = D::find('RoomDate',['where'=>$map,'field'=>'IFNULL(order_num,0) order_num']);
			if($data['date']>=$nowDate){
				$str = $num['order_num'] == $house['total_num'] ? 'true' : 'false';
			}else{
				$str = 'no';
			}
			$data = [
				'month' => date('m月',$date),
				'day'   => date('d',$date),
				'week'  => $week[date('N',$date)],//N - 星期几
				'full'  => $str, //客满情况 满员写true[string] 不满则false	no-之前之间不可查询
				'date'	=> date('Y-m-d',$date),
				'num'	=> $num['order_num'] === null ? $house['total_num'] : $num['order_num']
			];
			return $data;
		}, $dates);
		//用户id
		$userID = session('user');
		//查询当前用户已经拥有的且未使用的电子券
		$map = [
			'E.status' => 1,
			'E.userID' => $userID
		];
		$coupon = D::get(['CouponExchange','E'],[
			'where' => $map,
			'join'	=> 'LEFT JOIN __COUPON__ C ON C.id = E.cID',
			'field'	=> 'E.*,C.money,C.exprie_start,C.exprie_end,hcate,tcate,C.notDate'
		]);
		$data['coupon'] = array_map(function($data)use($house,$post){
			$data['hcate'] = explode(',',$data['hcate']);
			$data['notDate'] = explode("\r\n",$data['notDate']);
			/*
			 * 	首先判断该优惠券可不可以在该房间类型使用 ?  可以在查日期 : 若不可以直接就查日期了
			 * */
			if(in_array($house['category'],$data['hcate'])){
				$data['allow'] = 'yes';
				//若该房间 允许使用优惠券 则判断  当前提交日期是否在  优惠券的限定时间内,且不在不可使用日期内
				if($post['date']>=$data['exprie_start'] && $post['date']<=$data['exprie_end'] && !in_array($post['date'],$data['notDate'])){
					$data['allow'] = "yes";
				}else{
					$data['allow'] = 'no';
				}
			}else{
				$data['allow'] = 'no';
			}
			return $data;
		},$coupon);//优惠券信息
		$this->ajaxReturn($data);
	}
	//订单处理
	public function OrderCheck(){
		$order = D('Order');
		if($data = $order->create()){
			$data['orderNo'] = set_orderNo($data['type']);
			if(array_key_exists('coupon',$data)){
				$coupon = D::find('coupon',$data['coupon']);
				$arr = explode("\r\n",$coupon['notDate']);
				//这里必须要判断   优惠券设置的特定不可用日期
				$bool = true;
				foreach ($arr as $val){
					if($val>=$data['inTime'] && $val<=$data['outTime']){
						$bool = false;
					}
				}
				if($data['inTime']>=$coupon['exprie_start'] && $data['outTime']<=$coupon['exprie_end'] && $bool===true){
					$order->add($data);
					$this->success('下单成功,正在跳转到支付页面...',U('Orders/pay?orderNo='.$data['orderNo']));
				}else{
					$this->error('您选择的日期内,存在优惠券的不可用日期');
				}
			}else{
				$order->add($data);
				$this->success('下单成功,正在跳转到支付页面...');
			}
		}else{
			$this->error($order->getError());
		}
	}
	//假支付页面
	public function pay(){
		$userID = session('user');
		$orderNo = I('orderNo');
		$db = D::find('Order',['where'=>['orderNo'=>$orderNo]]);
		//当前用户所剩余额
		$map = [
			'status' => 1,
			'userID' => $userID
		];
		$money = D::find('Balance',[
			'where' => $map,
			'field' => "SUM(CASE WHEN method='plus' THEN money ELSE 0 END) up,SUM(CASE WHEN method='sub' THEN money ELSE 0 END) down"
		]);
		$this->assign('money',$money['up']-$money['down']);
		$this->assign('db',$db);
		$this->display();
	}
	/*
	 * 	 唤起支付
	 * */
	public function paySuccess(){
		$post = I('post.');
		if($post['payType'] == 1 || $post['payType'] ==3){
			//如果余额多余  要支付的金额  则直接用余额支付
			if($post['myMoney'] >= $post['price']){

			}
		}else{

		}
	}
	/*
	 * 	微信支付回调
	 * */
	public function wechatPay(){
		$orderNo = I('orderNo');
		$str = substr($orderNo,0,1);
		if($str === 'K' || $str === 'T'){
			//套餐  和  客房走一个回调逻辑
			$map['no'] = I('orderNo');
			$status = D::field('Order.status',$map);

		}else{
			//充值回调
		}
	}

	/*	确认付款后	操作逻辑
	 *	1、更新订单状态	ms_order
	 * 	2、插入财务流水表 ms_finance
	 *	3、若存在电子券 ？ 插入电子券使用记录表(ms_coupon_used) 且更新电子卷拥有记录表(ms_coupon_exchange)  : 不做操作
	 *  4、是否存在  余额支付	？ 插入余额记录表ms_balance	:不做操作
	 * 	5、插入购买房间时间记录表(ms_room_date)	若是客房 && 选择多天入住 ？ 则要将所有选择的天数都插入,并order+1
	 * 	6、插入积分变更记录表 ms_user_sorce
	 *
	 * */
	public function checkTable($orderNo,$type){

	}
}