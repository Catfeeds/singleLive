<?php
namespace Mobile\Controller;
use Common\Model\wx_pay;
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
	/*
	 * 	用户操作发起退款操作
	 *		首先看后台设置的参数 && 该订单是否在设置时间内 ？ 可以退款 ;否则不可以
	 * */
	public function backMoney(){
		$map['orderNo'] = I('orderNo');
		$orderMsg = D::find('Order',['where'=>$map]);
		$web = D('Config')->get_config('backTime');
		if($web && $web['backTime']>0){
			//该订单的开始时间
			$order_inTime = strtotime($orderMsg['inTime'],time());
			//设置退款时间
			$backTime = $web['backTime']*60*60;
			//当前时间戳
			$nowTime = time();
			//如果当钱时间戳 大于 $order_inTime - $backTime这个时间就不让它退款
			if( $nowTime > ($order_inTime - $backTime)){
				$this->error('您已经超过了退款时间限制,退款时间为订单所选开始日期的前'.$web['backTime'].'小时');
			}
		}
		D::save('Order',['where'=>$map],[
			'updateTime' => NOW_TIME,
			'status'	=> 5
		]);
		$this->success('申请成功,等待管理员审核',U('Orders/index'));
	}
	/*
	 * 用户发起 取消未付款的订单
	 * 	这里必须判断  使用优惠券  ?  将该优惠券变为可用状态  : 不做操作
	 * */
	public function resetOrder(){
		$map['orderNo'] = I('orderNo');
		$msg = D::find('Order',['where'=>$map]);
		D::save('Order',['where'=>$map],[
			'updateTime' => NOW_TIME,
			'status'	=> 4
		]);
		if($msg['coupon']){
			$sel = [
				'userID' => $msg['userID'],
				'card' => $msg['coupon'],
			];
			D::set('CouponExchange.status',['where'=>$sel],1);
		}
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
		$myDate = get_minDate_maxDate();
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
		$coupon = get_coupon($userID,$package,$info['createDate'],'tcate');
		$this->assign('db',$package);
		$this->assign('coupon',$coupon);
		$this->display();
	}
	//获取日期 优惠券数组
	public function getStrtotime()
	{
		$post = I('post.');
		$post['userID'] = session('user');
		//dump($post);die;
		$data = get_postDate_roomNum_coupon($post);
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
			$bool = is_house_all($parameter,$array);
			if($bool === true){
				if(array_key_exists('coupon',$data) && $data['coupon']){
					$cID = D::field('CouponExchange.cID',['where'=>['card'=>$data['coupon']]]);
					$coupon = D::find('coupon',$cID);
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
						//更新优惠券状态--为4(被占用),此状态就是为了避免下了好多单都不付款,然后挨个去付款会出现每单都会少优惠券的金额
						D::set('CouponExchange.status',['where'=>['card'=>$data['coupon']]],4);
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
		$db['no_balancePwd'] = D::field('Users.no_balancePwd',session('user'));
		//当前用户所剩余额
		$map = [
			'status' => 1,
			'userID' => $userID
		];
		$money = D::find('Balance',[
			'where' => $map,
			'field' => "SUM(CASE WHEN method='plus' THEN money ELSE 0 END) upPay,SUM(CASE WHEN method='back' THEN money ELSE 0 END) upBack,SUM(CASE WHEN method='sub' THEN money ELSE 0 END) down"
		]);
		$this->assign('money',$money['upPay']+$money['upBack']-$money['down']);
		$this->assign('db',$db);
		$this->display();
	}
	/*
	 * 	 唤起支付
	 * */
	public function paySuccess(){
		$post = I('post.');
		$order = D::find('Order',['where'=>['orderNo'=>$post['orderNo']]]);
		$payPwd = D::field('Users.balancePwd',session('user'));
		if(array_key_exists('payType',$post)){
			if($post['payType'] == 1){
				if(md5($post['no_balancePwd']) != $payPwd){
					$this->error('支付密码错误');
				}else{
					if($post['myMoney'] < $post['price']){
						$this->error('您的余额不足,请到个人中心充值或选择其他支付方式！');
					}else{
						//更新支付方式字段
						D::set('Order.payType',['where'=>['orderNo'=>$post['orderNo']]],'balance');
						$balance = [
							'userID' => $order['userID'],
							'money' =>  $post['price'],
							'orderNo' => $post['orderNo'],
							'method' => 'sub',
							'createTime' => time(),
							'status' => 1
						];
						M('Balance')->add($balance);
						checkTable($post['orderNo']);
						//$this->success('支付成功,正在跳转到我的订单',U('Orders/index'));
						$this->redirect('Orders/showSuccess',[],0,'');
					}
				}
			}else{
				$url = '/WeiXinPay/example/jsapi.php?title=房价预订&orderNo='.$post['orderNo'].'&amount='.($post['amount']*100).'&';
				redirect($url, 0, '');
				exit;	
			}
		}else{
			$this->error('请选择支付方式');
		}
	}

}