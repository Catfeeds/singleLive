<?php
namespace Home\Controller;
use Think\Controller;
use Think\D;
use Common\Model\wx_pay;
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
	/*
	 * 	查询房间
	 * */
	public function search(){
		$this->display();
	}
	public function getStrtotime(){
//		if(is_numeric(I('houseID'))){
//			$post['houseID'] = I('houseID');
//		}
//		if(I('date')){
//			$post['date'] = I('date');
//		}else{
//			$post['date'] = date('Y-m-d');
//		}
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
		$data['db'] = array_map(function($data)use($week,$post){
			$date = $data['date'];
			$data = [
				'month' => date('m月',$date),
				'day'   => date('d',$date),
				'week'  => $week[date('N',$date)],//N - 星期几
			];
			return $data;
		}, $dates);
		//查询房间信息(这是h-代表客房  t-代表套餐)
		if($post['type'] == 'k'){
			if(I('houseID')){
				$map['id'] = I('houseID');
			}
			$map['status'] = 1;
			$house = D::get('House',['where'=>$map]);
		}else{
			$house = D::find('Package',$post['houseID']);
		}
		$data['room'] = array_map(function($data)use($post,$dates){
			foreach($dates as $val){
				$data['room'][] = $this->get_nowHouse_data($data['id'],date('Y-m-d',$val['date']),'k');
			}
			$data = [
				'roomName' => $data['name'],
				'date' => $data['room']
			];
			return $data;
		},$house);
		$this->ajaxReturn($data);
	}
	/*
	 * 	获取当前房间的数据
	 * 		$houseID-房间id
	 * 		$date-日期
	 * 		$type-类型(t-套餐|h-客房)
	 * */
	public function get_nowHouse_data($houseID,$date,$type){
		//获取当前日期
		$nowDate = date('Y-m-d');
		//获取房间总数	查询提交时间的order数量
		$house = D::find('House',$houseID);
		$map['roomID'] = $houseID;
		$map['createDate'] = $date;
		$map['type'] = $type;
		$num = D::find('RoomDate',['where'=>$map,'field'=>'IFNULL(order_num,0) order_num']);
		if($num['order_num'] && $num['order_num']>0){
			$houseNum = $house['total_num']-$num['order_num'];
		}else{
			$houseNum = $house['total_num'];
		}
		/*
		 * 	判断该时间是否可以预定 ? （该房间的订单数量==房间总数 ？ 满员 : 剩余数量） : 不可预定
		 * */
		if($date>=$nowDate){
			$str = $num['order_num'] == $house['total_num'] ? 'true' : 'false';
		}else{
			$str = 'no';
		}
		if($type == 'k'){
			//判断是否设置了价格模板
			$is = D::find('TempletePrice',[
				'where' => [
					'roomID' => $houseID,
					'day' => $date
				],
				'field' => 'price'
			]);
			$money = $is['price'] ? $is['price'] : $house['money'];
		}else{
			$money = $house['packMoney'];
		}
		$arr = [
			'full' => $str,
			'num' => $houseNum,
			'money' => $money
		];
		return $arr;
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
	 * 	微信扫码回调
	 * */
	public function notifyQrcodeCallback(){
		 $xml = $GLOBALS['HTTP_RAW_POST_DATA']; //返回的xml
		 file_put_contents(dirname(__FILE__).'/xml.txt',$xml);//将返回的xml数据存在当前文件夹中
		 //记录日志 支付成功后查看xml.txt文件是否有内容如果有xml格式文件说明回调成功
		 $xmlObj = simplexml_load_string($xml, 'SimplexmlElement', LIBXML_NOCDATA);
		 $data = json_decode(json_encode($xmlObj),true);
		 $msg = '';
		 $vrify = wx_pay::vrify_order($data, $msg);
		 if($vrify){
			$attch = json_decode($data['attach'], true);
			$this->wechatPay($attch['orderNo']);
			echo 'SUCCESS'; //返回成功给微信端 一定要带上不然微信会一直回调8次
			exit;
	 	}
	}
	/*
	 * 	微信支付回调 不能有登陆验证
	 * 		微信支付时删除,解开下方注释
	 * */
	public function wechatPay($orderNo){
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