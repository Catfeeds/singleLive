<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\D;
class RoomsController extends CommonController{
	public $model = 'House';
	public function _map(&$data)
	{
		if (I('cate')) {
			$map['H.category'] = I('cate');
			//设定房间分类进来的情况
		}
		$map['H.status'] = '1';
		$data = [
			'alias' => 'H',
			'where' => $map,
			'join'  => 'LEFT JOIN __FILES__ F ON F.id = H.pic',
			'field' => "H.id,H.name,H.word,CONCAT('/Uploads',F.savepath,F.savename) `icon`"
		];
	}
	/**
	 * [index 客房]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function index()
	{
		
		if (IS_AJAX) {
			parent::index();
		}else{
			//获取分类信息
			$cates = D::get('HouseCate',[
				'status' => '1',
				'type' => 'h',
			]);
			$this->assign('cates',$cates);
			$this->display();
		}
	}
	/**
	 * [edit 客房详情页]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function edit($id)
	{
		$house = D::find('House',$id); //获取房屋信息
		$bannerMap['id'] = ['in',array_filter(explode(',', $house['imgs']))]; //banner ids
		$Banners = D::get('Files',$bannerMap); //获取banner
		$this->assign('banners',$Banners);
		$this->assign('db',$house);
		$this->display();
	}
}