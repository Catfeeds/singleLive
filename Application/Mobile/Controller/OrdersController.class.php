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
		//查询当前用户可以使用的电子券
		$map = [
			'status' => 1,
			'userID' => $userID
		];
		$coupon = D::get(['CouponExchange','E'],[
			'where' => $map,
			'join'	=> 'LEFT JOIN __COUPON__ C ON C.id = E.cID',
			'field'	=> 'E.*,C.money,C.exprie_start,C.exprie_end,hcate'
		]);

		//$have = D::lists('Coupon','id,hcate',$map);
		//查询当前房间信息
		$house = D::find('House',$houseID);


		//获取当前日期
		$nowDate = date('Y-m-d');
		$this->assign('house',$house);
		$this->assign('nowDate',$nowDate);
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