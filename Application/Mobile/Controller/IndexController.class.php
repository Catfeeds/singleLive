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
						$data['url'] = U('Rooms/Edit',['id' => $data['id']]);
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
		$minDate = date('Y-m-d');
		$maxDate = date('Y-m-d',strtotime("$minDate +6 month"));
		$myDate = [
			'min' => $minDate,
			'max' => $maxDate,
		];
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
		if(!$msg['order_num']){
			$num = '当前剩余房间数:'.$package['total_num'];
		}elseif($msg['order_num'] == $package['total_num']){
			$num = '当前选择日期已经满房';
		}else{
			$num = '当前剩余房间数:'.$msg['order_num'];
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

}