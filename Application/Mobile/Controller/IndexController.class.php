<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\D;
class IndexController extends CommonController{
	public $model = 'Environment';
	public function _map(&$data)
	{
		switch (ACTION_NAME) {
			case 'index':
				$map['H.status'] = '1';
				$map['H.push'] = '1';
				$sql = [
					D::get('Package',[
						'alias' => 'H',
						'where' => $map,
						'field' => "H.id,'p' as type,H.title name,H.word,H.pic,H.add_time createTime"
					],false),
					D::get('House',[
						'alias' => 'H',
						'where' => $map,
						'field' => "H.id,'h' as type,H.name,H.word,H.pic,H.add_time createTime"
					],false),
				];
				$data = [
					'alias' => 'D',
					'table' => '('.implode(' UNION ALL ', $sql).') ',
					'join'  => 'LEFT JOIN __FILES__ F ON F.id = D.pic',
					'field' => "D.*,CONCAT('/Uploads',F.savepath,F.savename) `icon`",
					'order' => 'D.createTime DESC'
				];
				break;
			case 'package':
				if (I('cate')) {
					$map['H.category'] = I('cate');
					//设定房间分类进来的情况
				}
				$map['H.status'] = '1';
				$data = [
					'alias' => 'H',
					'table' => '__PACKAGE__',
					'where' => $map,
					'join'  => 'LEFT JOIN __FILES__ F ON F.id = H.pic',
					'field' => "H.id,H.title name,H.word,CONCAT('/Uploads',F.savepath,F.savename) `icon`"
				];
				break;
			default:
				if (I('type')) {
					$map['H.type'] = I('type');
					//设定房间分类进来的情况
				}
				$data = [
					'alias' => 'H',
					'where' => $map,
					'join'  => 'LEFT JOIN __FILES__ F ON F.id = H.pic',
					'field' => "H.id,H.name,H.word,CONCAT('/Uploads',F.savepath,F.savename) `icon`"
				];
				break;
		}

	}
	/**
	 * [index 首页]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function index()
	{
		if (IS_AJAX) {
			parent::index(function($data){
				switch ($data['type']) {
					case 'h':
						$data['url'] = U('Rooms/edit',['id' => $data['id']]);
						break;
					case 'p':
						$data['url'] = U('Index/packageEdit',['id' => $data['id']]);
						break;
				}
				return $data;
			});
		}else{
			$this->assign('banners',getBanner('b'));
			$this->display();
		}
	}
	/**
	 * [restaurant 餐饮]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function restaurant()
	{
		if (IS_AJAX) {
			parent::index();
		}else{
			$this->assign('banners',getBanner('f'));
			$this->display();
		}
	}
	/**
	 * [restaurant 餐饮详情]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function restaurantEdit()
	{
		$this->display();
	}
	/**
	 * [environment 环境]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function environment()
	{
		if (IS_AJAX) {
			parent::index();
		}else{
			$this->assign('banners',getBanner('e'));
			$this->display();
		}
	}
	/**
	 * [environmentEdit 环境详情]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function environmentEdit($id)
	{
		$house = D::find('Environment',$id);
		$bannerMap['id'] = ['in',array_filter(explode(',', $house['imgs']))]; //banner ids
		$Banners = D::get('Files',$bannerMap); //获取banner
		$this->assign('banners',$Banners);
		$this->assign('db',$house);
		$this->display();
	}
	/**
	 * [campaign 体验活动]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function campaign()
	{
		if (IS_AJAX) {
			parent::index();
		}else{
			$this->assign('banners',getBanner('a'));
			$this->display();
		}
	}
	public function campaignEdit()
	{
		$this->display();
	}
	/**
	 * [package 套餐]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function package()
	{
		if (IS_AJAX) {
			parent::index();
		}else{
			//获取分类信息
			$cates = D::get('HouseCate',[
				'status' => '1',
				'type' => 't',
			]);
			$this->assign('cates',$cates);
			$this->assign('banners',getBanner('t'));
			$this->display();
		}
	}
	/**
	 * [packageEdit 套餐详情页]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-08
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function packageEdit($id)
	{
		$db = D::find('Package',$id);
		$db['set'] = array_map(function($data){
			$data['amount'] = sprintf('%.2f', $data['attr'] * $data['money']);
			return $data;
		},  D::get('PackageSet',['pid' => $id]));
		$this->assign('db',$db);
		$this->display();
	}
	//查找当天房间剩余余量
	public function search_num(){
		$post = I('post.');
		$post['type'] = 't';
		$msg = D::find('RoomDate',['where'=>$post,'field'=>'order_num']);
		$package = D::find('Package',[
			'where'=>"id=".$post['roomID'],
			'field'=>'total_num'
		]);
		if($msg['order_num'] && $msg['order_num']>=0 && $msg['order_num']<$package['total_num']){
			$shu = $package['total_num']-$msg['order_num'];
			$num = $shu.'间';
		}elseif($msg['order_num'] && $msg['order_num'] == $msg['order_num']){
			$num = '当前选择日期已经满房';
		}else{
			$num = $package['total_num'].'间';
		}
		$this->ajaxReturn($num);
	}
	//缓存 套餐信息
	public function jumps(){
		S('info',null);
		$post  = I('post.');
		$msg['status'] = 'yes';
		if(!$post['createDate']){
			$msg['msg'] = '请选择日期';
			$msg['status'] = 'no';
		}
		$map = [
			'roomID' => $post['$post'],
			'createDate' => $post['createDate'],
			'type'	=> 't'
		];
		$room = D::find('RoomDate',['where'=>$map,'field'=>'order_num']);
		$package = D::find('Package',[
			'where'=>"id=".$post['roomID'],
			'field'=>'total_num,limit'
		]);
		//满房情况
		if($room['order_num'] == $package['total_num']){
			$msg['msg'] = '您选择的日期已经满房,不可预定';
			$msg['status'] = 'no';
		}
		//套餐限购
		$sel = [
			'userID' => session('user'),
			'type' => 't',
			'roomID' => $post['roomID'],
			'status' => array('in','1,2,9')
		];
		$limit = D::find('Order',[
			'where' => $sel,
			'field' => 'SUM(num) limitNum'
		]);
		if($limit['limitNum'] == $package['limit']){
			$msg['msg'] = '您之前购买的该套餐已经到达了限购份数,不能下单';
			$msg['status'] = 'no';
		}
		$number = $limit['limitNum'] + $post['limit_num'];
		if($number > $package['limit']){
			$msg['msg'] = "您之前已经购买了该套餐".$limit['limitNum']."份,请减少选购数量";
			$msg['status'] = 'no';
		}
		if($msg['status'] == 'yes'){
			S('info',$post);
			$msg['msg'] = '正在跳转到填写订单页面...';
			$msg['status'] = 'yes';
		}
		$this->ajaxReturn($msg);
	}
	/*
	 * 	微信支付回调 不能有登陆验证
	 * 		本地测试时将$orderNo当做形参
	 * 		微信支付时删除,解开下方注释
	 * */
	public function wechatPay(){
		$orderNo = I('orderNo');
		$str = substr($orderNo,0,1);
		if($str === 'K' || $str === 'T'){
			//套餐  和  客房走一个回调逻辑
			$map['orderNo'] = $orderNo;
			$msg = D::find('Order',['where'=>$map]);
			if ($msg['status'] == '8') {
				D::set('Order.payType',['where'=>['orderNo'=>$orderNo]],'wechat');
				$this->checkTable($orderNo);
				//$this->success('支付成功,正在跳转到首页',U('Index/index'));
				$this->redirect('Self/index',[],0,'');
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
				//插入积分记录
				$sorce = [
					'userID' => $info['userID'],
					'type' => 'recharge',
					'sorce' => $info['sorce'],
					'method' => 'plus',
					'createTime' => time(),
					'admin' => '0'
				];
				M('UserSorce')->add($sorce);
				//$this->success('充值成功',U('Self/index'));
				$this->redirect('Self/index',[],0,'');
			}
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
	public function checkTable($orderNo){
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
	public function search_room_date($roomID,$type){
		$search = [
			'type' => $type,
			'roomID' => $roomID
		];
		$roomDate = D::lists('roomDate','createDate',['where'=>$search]);
		return $roomDate;
	}

}