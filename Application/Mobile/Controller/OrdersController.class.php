<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\D;
class OrdersController extends CommonController{
	public static $login = true;
	public $model = 'Order';
	/**
	 * zhenhong~
	 */
	public function _map(&$data){
		$map['userID'] = session('user');
		$data = [
			'where' => $map,
			'order'	=> 'createTime desc'
		];
	}
	/**
	 *  订单显示页面  zhenhong~
	 */
	public function index()
	{
		if(IS_AJAX){
			parent::index(function($data){
				switch ($data['status']) {
					case '1':
						$data['url'] = U('Orders/backMoney?orderNo='.$data['orderNo']);
						break;
					case '8':
						$data['url'] = U('Orders/pay?orderNo='.$data['orderNo']);
						$data['reset_url'] = U('Orders/resetOrder?orderNo='.$data['orderNo']);
						break;
				}
				if($data['type'] == 'k'){
					$data['type_name'] = '客房';
					$data['date_show'] = $data['inTime'].'-'.$data['outTime'];
				}else{
					$data['type_name'] = '套餐';
					$data['date_show'] = $data['inTime'];
				}
				$data['link'] = U('Orders/edit?orderNo='.$data['orderNo']);
				$data['status_type'] = getTypes($data['status']);
				$data['class'] = showClass($data['status']);
				return $data;
			});
		}
		$this->display();
	}
	//订单查看
	public function edit(){
		$map['orderNo'] = I('orderNo');
		$db = D::find('Order',[
			'where'	=> $map
		]);
		if($db['type'] == 'k'){
			$arr = D::find('House',$db['roomID']);
			$db['roomPic'] = getSrc($arr['pic']);
			$db['word'] = $arr['word'];
			$db['cateName'] = D::field('HouseCate.title',$arr['category']);
			$db['show_date'] = $db['inTime'].','.$db['outTime'];
		}else{
			$arr = D::find('Package',$db['roomID']);
			$db['roomPic'] = getSrc($arr['pic']);
			$db['word'] = $arr['word'];
			$db['cateName'] = D::field('HouseCate.title',$arr['category']);
			$db['show_date'] = $db['inTime'];
		}
		$db['status_type'] = getTypes($db['status']);
		$this->assign('db',$db);
		$this->display();
	}
	//用户操作发起退款操作
	public function backMoney(){
		$map['orderNo'] = I('orderNo');
		D::save('Order',['where'=>$map],[
			'updateTime' => NOW_TIME,
			'status'	=> 5
		]);
		$this->success('申请成功,等待管理员审核',U('Orders/index'));
	}
	//用户发起 取消未付款的订单
	public function resetOrder(){
		$map['orderNo'] = I('orderNo');
		D::save('Order',['where'=>$map],[
			'updateTime' => NOW_TIME,
			'status'	=> 4
		]);
		$this->success('取消成功',U('Orders/index'));
	}
	/**
	 * 下单逻辑  zhenhong~
	 */
	public function prepareOrder()
	{
		$houseID = I('id');
		//查询当前房间信息
		$house = D::find('House',$houseID);
		//设置可预订房间的最小与最大日期
		$minDate = date('Y-m-d');
		$maxDate = date('Y-m-d',strtotime("$minDate +6 month"));
		$myDate = [
			'min' => $minDate,
			'max' => $maxDate,
		];
		$this->assign('house',$house);
		$this->assign('myDate',$myDate);
		$this->display();
	}
	/**
	 * 下单逻辑  zhenhong~
	 */
	public function prepareOrderPackage()
	{
		$info = S('info');
		$userID = session('user');
		$package = D::find('Package',I('id'));
		$package['inTime'] = $info['createDate'];
		$package['num'] = $info['limit_num'];
		$coupon = $this->get_coupon($userID,$package,$info['createDate'],'tcate');
		$this->assign('db',$package);
		$this->assign('coupon',$coupon);
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
			$map['type'] = 'h';
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
			$data = [
				'month' => date('m月',$date),
				'day'   => date('d',$date),
				'week'  => $week[date('N',$date)],//N - 星期几
				'full'  => $str, //客满情况 满员写true[string] 不满则false	no-之前之间不可查询
				'date'	=> date('Y-m-d',$date),
				'num'	=> $houseNum
			];
			return $data;
		}, $dates);
		//用户id
		$userID = session('user');
		//查询当前用户已经拥有的且未使用的电子券
		$data['coupon'] = $this->get_coupon($userID,$house,$post['date'],'hcate');
		$this->ajaxReturn($data);
	}
	//订单处理
	public function OrderCheck(){
		$order = D('Order');
		if($data = $order->create()){
			$data['orderNo'] = set_orderNo($data['type']);
			//判断 所选日期中是否存在满房的情况
			if($data['type'] == 'k'){
				$parameter = push_select_time($data['inTime'],$data['outTime']);
			}else{
				$parameter = $data['inTime'];
			}
			$array = ['roomID'=>$data['roomID'],'type'=>$data['type']];
			$bool = $this->is_house_all($parameter,$array);
			if($bool === true){
				if(array_key_exists('coupon',$data) && $data['coupon']){
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
					$this->success('下单成功,正在跳转到支付页面...',U('Orders/pay?orderNo='.$data['orderNo']));
				}
			}else{
				$this->error('您所选日期中存在已经满房的房间!');
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
		$order = D::find('Order',['where'=>['orderNo'=>$post['orderNo']]]);
		if(array_key_exists('payType',$post)){
			if($post['payType'] == 1){
				if($post['payType'] == 1 && $post['myMoney'] < $post['price']){
					$this->error('您的余额不足,请到个人中心充值或选择其他支付方式！');
				}else{
					$balance = [
						'userID' => $order['userID'],
						'money' => $post['myMoney'] - $post['price'],
						'orderNo' => $post['orderNo'],
						'method' => 'sub',
						'createTime' => strtotime(date('Y-m-d'),time()),
						'status' => 1
					];
					M('Balance')->add($balance);
					$this->checkTable($post['orderNo']);
					$this->success('支付成功,正在跳转到首页',U('Index/index'));
				}
			}else{
				//跳转微信支付
				$this->wechatPay($post['orderNo']);
			}
		}else{
			$this->error('请选择支付方式');
		}

	}
	/*
	 * 	微信支付回调
	 * */
	public function wechatPay($orderNo){
		//$orderNo = I('orderNo');
		$str = substr($orderNo,0,1);
		if($str === 'K' || $str === 'T'){
			//套餐  和  客房走一个回调逻辑
			$map['orderNo'] = $orderNo;
			$msg = D::find('Order',['where'=>$map]);
			if ($msg['status'] == '8') {
				$this->checkTable($orderNo);
				$this->success('支付成功,正在跳转到首页',U('Index/index'));
			}
		}else{
			//充值回调
			$sel = [
				'orderNo' => $orderNo,
				'method' => 'plus'
			];
			$info = D::find('Balance',['where'=>$sel]);
			if($info['status'] == '2'){
				$save = [
					'orderNo' => $orderNo,
					'method' => 'plus'
				];
				D::set('Balance.status',['where'=>$save],1);
				//插入财务流水
				$Finance = [
					'userID' => $info['userID'],
					'orderNO' => $orderNo,
					'money' => $info['money'],
					'type' => 'recharge',
					'createDate' => date('Y-m-d'),
				];
				M('Finance')->add($Finance);
				$this->success('充值成功',U('Self/index'));
			}
		}
	}

	/*	确认付款后	操作逻辑
	 *	1、更新订单状态	ms_order
	 * 	2、插入财务流水表 ms_finance   是否存在余额支付和微信混合支付的情况 ？ 订单金额-余额 : 订单金额
	 *	3、若存在电子券 ？ 插入电子券使用记录表(ms_coupon_used) && 更新电子卷拥有记录表(ms_coupon_exchange) && 减库存  : 不做操作
	 * 	5、插入购买房间时间记录表(ms_room_date)	若是客房 && 选择多天入住 ？ 则要将所有选择的天数都插入,并order+1
	 * 	6、插入积分变更记录表 ms_user_sorce
	 *	$orderNo-订单号
	 * */
	public function checkTable($orderNo){
		$map['orderNo'] = $orderNo;
		$msg = D::find('Order',['where'=>$map]);
		M('Order')->where($map)->setField(['status'=>1,'updateTime'=>NOW_TIME]);
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
			];
			//插入电子券使用记录
			M('CouponUsed')->add($coupon_used);
			$save = [
				'status' => 2,
				'updateTime' => NOW_TIME,
			];
			//更新电子券使用状态
			M('CouponExchange')->where("card=".$msg['coupon'])->setField($save);
			$cID = D::field('CouponExchange.cID',[
				'where' => [
					'userID' => $msg['userID'],
					'card' => $msg['coupon']
				]
			]);
			//减库存
			M('Coupon')->where("id=".$cID)->setDec('num',1);
		}
		$roomDate = $this->search_room_date($msg['roomID'],$msg['type']);
		if($msg['type'] == 'k'){
			//客房-购买房间时间记录表 逻辑
			$arr = push_select_time($msg['inTime'],$msg['outTime']);
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
				$save['type'] = 'h';
				M('RoomDate')->where($save)->setInc('order_num',1);
			}
			//不存在的日期,则新增
			if($add_date){
				$add_date = array_map(function($data)use($msg){
					$data['roomID'] = $msg['roomID'];
					$data['order_num'] = 1;
					$data['type'] = 'h';
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
			'createTime' => strtotime(date('Y-m-d'))
		];
		M('UserSorce')->add($sorce_data);
	}

	/*
	 * 	查询购买房间时间记录表  数据
	 * 	$roomID 房间id
	 * 	$type   h-客房  t-套餐
	 * 	返回一个  一维数组
	 * */
	public function search_room_date($roomID,$type){
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
	public function is_house_all($obj,$arr){
		$bool = true;
		if(is_array($obj) === true && $arr['type'] == 'k'){
			$house_num = D::field('House.total_num',$arr['roomID']);
			$sel = [
				'roomID' => $arr['roomID'],
				'type' => $arr['type'],
				'order_num' => $house_num
			];
			$arr = D::lists('RoomDate','createDate',['where'=>$sel]);
			foreach ($obj as $val){
				if(in_array($val,$arr)){
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
			$arr = D::lists('RoomDate','createDate',['where'=>$sel]);
			if(in_array($obj,$arr)){
				$bool = false;
			}
		}
		return $bool;
	}

	/*
	 * 	获得该房间的优惠券列表
	 * 	$userID-用户id
	 *  $house -房间信息
	 *  $date-提交日期
	 *  $type - coupon表的  套餐  客房字段
	 * */
	public function get_coupon($userID,$house,$date,$type){
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
                 * 	首先判断该优惠券可不可以在该房间类型使用 ?  可以在查日期 : 若不可以直接就查日期了
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
}