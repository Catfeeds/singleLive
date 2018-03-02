<?php
namespace Home\Controller;
use Think\Controller;
use Think\D;
class IndexController extends CommonController{
	public $model = 'Environment';
	public function _map(&$data)
	{
		switch (ACTION_NAME) {
			case 'index':
				$map['H.status'] = '1';
				if (I('cate')) {
					//设定房间分类进来的情况
					$map['H.category'] = I('cate');
				}
				$data = [
					'alias' => 'H',
					'table' => '__HOUSE__',
					'where' => $map,
					'field' => "H.*,'h' as type,CONCAT('/Uploads',F.savepath,F.savename) `icon`",
					'join'  => 'LEFT JOIN __FILES__ F ON F.id = H.pic',
					'order' => 'H.add_time DESC'
				];
				break;
			case 'package':
				if (I('cate')) {
					//设定房间分类进来的情况
					$map['H.category'] = I('cate');
				}
				if(I('order')){
					switch(I('order')){
						case 'timeDesc':
							$order = 'add_time desc';
							break;
						case 'timeAsc':
							$order = 'add_time asc';
							break;
						case 'priceH':
							$order = 'packMoney desc';
							break;
						case 'priceL':
							$order = 'packMoney asc';
							break;
						default:
							$order = 'add_time desc';
							break;
					}
				}else{
					$order = 'add_time desc';
				}
				$map['H.status'] = '1';
				$data = [
					'alias' => 'H',
					'table' => '__PACKAGE__',
					'where' => $map,
					'join'  => 'LEFT JOIN __FILES__ F ON F.id = H.pic',
					'field' => "H.*,'p' as type,CONCAT('/Uploads',F.savepath,F.savename) `icon`",
					'order' => $order
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
			$cates = D::get('HouseCate',[
				'status' => '1',
				'type' => 'h',
			]);
			$this->assign('cates',$cates);
			$this->assign('banners',getBanner('b'));
			$this->display();
		}
	}
	/**
	 * [problem 常见问题]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function problem()
	{
		$db = D::get('Problem',['order' => 'add_time DESC']);
		$this->assign('db',$db);
		$this->assign('list',$db);
		$this->display();
	}
	/*交通指南*/
	public function traffic()
	{
		$db = D::get('Traffic',['order' => 'add_time DESC']);
		$this->assign('db',$db);
		$this->assign('list',$db);
		$this->display();
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
		switch ($house['type']){
			case 'e':
				$house['typeName'] = '环境';
				break;
			case 'f':
				$house['typeName'] = '餐饮';
				break;
			case 'a':
				$house['typeName'] = '体验活动';
				break;
			case 'm':
				$house['typeName'] = '会员俱乐部';
				break;
		}
		$arr = array_filter(explode(',', $house['imgs']));
		$bannerMap['id'] = ['in',$arr]; //banner ids
		$Banners = D::get('Files',$bannerMap); //获取banner
		$this->assign('banners',$Banners);
		$this->assign('first',$arr[1]);
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
		if(date('Y-m-d') > $db['allowIn']){
			$date = date('Y-m-d');
		}else{
			$date = $db['allowIn'];
		}
		$myDate = [
			'min' => $date,
			'max' => $db['allowOut']
		];
		$arr = array_filter(explode(',', $db['imgs']));
		$bannerMap['id'] = ['in',$arr]; //banner ids
		$Banners = D::get('Files',$bannerMap); //获取banner
		$this->assign('banners',$Banners);
		$this->assign('first',$arr[1]);
		$this->assign('myDate',$myDate);
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
				checkTable($orderNo);
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
}