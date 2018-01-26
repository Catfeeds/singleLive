<?php
namespace Admin\Model;
use Think\Model;
use Think\D;
class OrderModel extends Model {
	protected $_validate = [
		['phone','check_phone','会员不存在或被删除',1,'callback'],
		['price','require','请输入订单金额'],
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
	];
	protected $_auto = [
		['date','set_date',1,'callback'],
		['createTime','time',1,'function'],
		['updateTime','time',2,'function'],
		['num','set_num',1,'callback'],
		['status',1],
		['payType','outline'],
		['orderCome','admin'],
		['do','set_do',1,'callback'],
		['userID','set_userID',1,'callback'],
		['coupon',0]
	];
	//验证手机号
	function check_phone(){
		$user = D::find('Users',['where'=>['mobile'=>I('phone')]]);
		if($user && $user['status'] != 3){
			return true;
		}else{
			return false;
		}
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
	//设置操作人
	function set_do(){
		$admin = session('root_user');
		return $admin['id'];
	}
	//设置会员id
	function set_userID(){
		return D::field('Users.id',['where'=>['mobile'=>I('phone')]]);
	}
}