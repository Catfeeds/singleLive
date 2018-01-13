<?php
namespace Mobile\Model;
use Think\Model;
use Think\D;
class OrderModel extends Model {
	protected $_validate = [
		['inTime','require','请选择开始入住日期'],
		['outTime','require','请选择离开日期',0],
		['outTime','check_outTime','离开日期至少比入住日期大一天',0,'callback'],
		['mobile','require','请输入联系人手机号码'],
		['mobile','/^(((13[0-9]{1})|(15[0-35-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/','手机号码不正确',0,'regex'],
		['sex','require','请选择称谓'],
		['username','require','请填写联系人姓名'],
		['email','require','请填写联系人电子邮箱'],
		['email','/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/','邮箱格式不正确',0,'regex'],
		['mark','require','请填写备注信息'],
	];
	protected $_auto = [
		['date','set_date','1','callback'],
		['createTime','time','1','function'],
		['updateTime','time','2','function'],
		['num','set_num','1','callback'],
		['userID','set_userID','1','callback'],
		['status',8],
		['price','set_price','1','callback'],
		['coupon','set_coupon','1','callback']
	];
	/*
	 * 	设置插入订单价格
	 * 		首先判断  该订单是否存在优惠券
	 * 		其次判断  该订单是客房 还是 套餐的订单
	 * 				 客房：根据开始-结束求出天数 则价格为天数*房间单价
	 * 				 套餐：根据购买的份数 则价格为  份数*房间单价
	 * 		则该订单的总价格为：
	 * 		电子券 && 客房 ？ (天数*房间单价) - 电子券金额  : (天数*房间单价)
	 * 		电子券 && 套餐 ？ (份数*房间单价) - 电子券金额  : (份数*房间单价)
	 * */
	function set_price(){
		$post = I('post.');
		if($post['type'] == 'k'){
			$money = D::field('House.money',$post['roomID']);//房间单价
			$start = strtotime($post['inTime']);//入住时间
			$end = strtotime($post['outTime']);//离开时间
			//这里判断$num>=2是因为 现在需求是2017-01-01-2017-01-02这算一天的房间单价
			$num = intval(($end-$start)/86400);//入住天数
			if(array_key_exists('coupon',$post) === true){
				$couponMoney = D::field('Coupon.money',$post['coupon']);
				$price = $num >= 2 ? ($money*$num-$couponMoney) : ($money-$couponMoney);
			}else{
				$price = $num >= 2 ? ($money*$num) : $money;
			}
		}else{
			$money = D::field('Package.packMoney',$post['roomID']);//套餐单价
			if(array_key_exists('coupon',$post) === true){
				$couponMoney = D::field('Coupon.money',$post['coupon']);
				$price = $post['num'] > 1 ? ($money*$post['num'] - $couponMoney) : ($money-$couponMoney);
			}else{
				$price = $post['num'] > 1 ? $money*$post['num'] : $money;
			}
		}
		return $price;
	}

	//设置下单标准日期
	function set_date(){
		return date('Y-m-d');
	}
	//检查离开日期
	function check_outTime($data){
		if($data>I('inTime')){
			return true;
		}else{
			return false;
		}
	}
	//设置购买数量
	function set_num(){
		$type = I('type');
		if($type == 'k'){
			return 1;
		}else{
			return I('num');
		}
	}
	//设置插入优惠券 0-表示没有用  优惠券
	function set_coupon(){
		$post = I('post.');
		if(array_key_exists('coupon',$post) === true){
			return $post['coupon'];
		}else{
			return 0;
		}
	}
	//用户id
	function set_userID(){
		return session('user');
	}
}