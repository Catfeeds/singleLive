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
		$userID = session('user');
		//查询当前用户已拥有的电子券
		$map = [
			"E.status" => 1,
			"E.userID" => $userID
		];
		$coupon = D::get(['CouponExchange','E'],[
			'where' => $map,
			'join'	=> 'LEFT JOIN __COUPON__ C ON C.id = E.cID',
			'field'	=> 'E.*,C.money,C.exprie_start,C.exprie_end,hcate'
		]);
		//查询当前房间信息
		$house = D::find('House',$houseID);
		array_map(function($data)use($house){
			
		},$coupon);
		//$have = D::lists('Coupon','id,hcate',$map);


		//设置可预订房间的最小与最大日期
		$mixDate = date('Y-m-d');
		$maxDate = date('Y-m-d',strtotime("$mixDate +6 month"));
		$myDate = [
			'mix' => $mixDate,
			'max' => $maxDate,
		];
		$this->assign('house',$house);
		$this->assign('myDate',$myDate);
		$this->assign('coupon',$coupon);
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
}