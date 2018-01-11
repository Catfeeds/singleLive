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
		//设置可预订房间的最小与最大日期
		$mixDate = date('Y-m-d');
		$maxDate = date('Y-m-d',strtotime("$mixDate +6 month"));
		$myDate = [
			'mix' => $mixDate,
			'max' => $maxDate,
		];
		$this->assign('myDate',$myDate);
		$this->assign('house',$house);
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
	/**
	 * [getStrtotime 获取时间日期格式信息]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-11
	 * @Function []
	 * @param    [type]     $time   [选中日期（时间戳）]
	 * @return   [type]             [description]
	 */
	public function getStrtotime($date)
	{
		// dump($date);
		$dates = [
			[
				'date' => strtotime($date.'-3 days'),
			],
			[
				'date' => strtotime($date.'-2 days'),
			],
			[
				'date' => strtotime($date.'-1 days'),
			],
			[
				'date' => strtotime($date),
			],
			[
				'date' => strtotime($date.'+1 days'),
			],
			[
				'date' => strtotime($date.'+2 days'),
			],
			[
				'date' => strtotime($date.'+3 days'),
			],
		];
		$week = [
			1 => '一',
			2 => '二',
			3 => '三',
			4 => '四',
			5 => '五',
			6 => '六',
			7 => '日',
		];
		$data['db'] = array_map(function($data)use($week){
			$date = $data['date'];
			$data = [
				'month' => date('m月',$date),
				'day'   => date('d',$date),
				'week'  => $week[date('N',$date)],
				'full'  => 'false', //客满情况 满员写true[string]
			];
			return $data;
		}, $dates);
		$data['coupon'] = [];//优惠券信息
		$this->ajaxReturn($data);
	}
}