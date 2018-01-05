<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\D;
class OrdersController extends CommonController{
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
	 */
	public function prepareOrder()
	{
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