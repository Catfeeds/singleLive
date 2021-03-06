<?php
namespace Home\Model;
use Think\Model;
use Think\D;
class OrderModel extends Model {
	protected $_validate = [
		['inTime','require','请选择入住时间'],
		['outTime','require','请选择退房时间',0],
		['outTime','check_outTime','退房时间至少比入住时间大一天',0,'callback'],
		['mobile','require','请输入联系人手机号码'],
		['mobile','/^(((13[0-9]{1})|(15[0-35-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/','手机号码不正确',0,'regex'],
		['sex','require','请选择称谓'],
		['username','require','请填写联系人姓名'],
		['email','require','请填写联系人电子邮箱'],
		['email','/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/','邮箱格式不正确',0,'regex'],
		['mark','require','请填写备注信息'],
		['price','check_price','优惠券金额只能小于房间金额才可使用',1,'callback'],
	];
	protected $_auto = [
		['date','set_date','1','callback'],
		['createTime','time','1','function'],
		['updateTime','time','2','function'],
		['num','set_num','1','callback'],
		['userID','set_userID','1','callback'],
		['status',8],
		['price','set_price','1','callback'],
		['coupon','set_coupon','1','callback'],
		['payType','no'],
		['orderCome','user'],
		['do',0]
	];
	/*
	 * 判断 该订单提交的优惠券价格是否  高于该房价的价格
	 * 		若高于则不让它提交
	 * 		$post 下单的表单数组
	 * */
	function check_price(){
		$price = $this->set_price();
		if($price<=0){
			return false;
		}else{
			return true;
		}
	}
	/*
	 * 	设置插入订单价格
	 * 		首先判断  该订单是否存在优惠券
	 * 		其次判断  该订单是客房 还是 套餐的订单
	 * 				 客房：根据开始-结束求出天数 则价格为天数*房间单价
	 * 				 套餐：根据购买的份数 则价格为  份数*房间单价
	 * 		则该订单的总价格为：
	 * 		电子券 && 客房 ？ (天数*房间单价) - 电子券金额  : (天数*房间单价)
	 * 		电子券 && 套餐 ？ (份数*房间单价) - 电子券金额  : (份数*房间单价)
	 * 	新需求  仅用于客房 每天都可能设置房价
	 * */
	protected function set_price(){
		$post = I('post.');
		return get_order_price($post);
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