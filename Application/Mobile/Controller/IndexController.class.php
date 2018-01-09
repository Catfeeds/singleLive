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
		// echo parent::index('sql');die;
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
		// dump($db['set']);die;
		$this->assign('db',$db);
		$this->display();
	}

}