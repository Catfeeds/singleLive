<?php
namespace Admin\Model;
use Think\Model;
class CouponModel extends Model {
	protected $_validate = [
	    ['title','require','请填写电子券名称'],
	    ['pic','require','请上传电子券图片'],
	    ['money','require','请填写电子券金额'],
	    ['money','check_money','电子券金额必须为正整数',0,'callback'],
		['exprie_start','require','请选择开始日期'],
		['exprie_start','check_exprie_start','开始日期必须小于结束日期',0,'callback'],
		['exprie_end','require','请选择结束日期'],
		['exprie_end','check_exprie_end','结束日期必须大于开始日期',0,'callback'],
		['num','require','请设置电子券库存数量'],
		['notDate','require','请设置电子券不可使用日期'],
	    ['sorce','require','请设置兑换所需积分'],
	    ['sorce','check_sorce','兑换所需积分必须为正整数',0,'callback'],
	];
	protected $_auto = [
		['status',1],
		['year','set_year',self::MODEL_INSERT,'callback'],
		['notDate','set_notDate',self::MODEL_BOTH,'callback'],
		['hcate','set_hcate',self::MODEL_BOTH,'callback'],
		['tcate','set_tcate',self::MODEL_BOTH,'callback'],
		['add_time','time',self::MODEL_INSERT,'function'],
		['update_time','time',self::MODEL_BOTH,'function'],
	];
	function check_sorce($data){
		if(preg_match("/^[+]{0,1}(\d+)$/",$data) ==1){
			return true;
		}else{
			return false;
		}
	}
	function check_money($data){
		if(preg_match("/^[+]{0,1}(\d+)$/",$data) ==1){
			return true;
		}else{
			return false;
		}
	}
	//设置开始日期检验
	function check_exprie_start($data){
		$start = strtotime($data,time());
		$end = strtotime(I('exprie_end'),time());
		if($start<$end){
			return true;
		}else{
			return false;
		}
	}
	//设置结束日期检验
	function check_exprie_end($data){
		$end = strtotime($data,time());
		$start = strtotime(I('exprie_start'),time());
		if($end>$start){
			return true;
		}else{
			return false;
		}
	}
	//设置当前年份
	function set_year(){
		$year = date('Y');
		return $year;
	}
	//设置可以使用电子券的客房分类
	function set_hcate(){
		$hcate = I('hcate');
		return implode(',',$hcate);
	}
	//设置可以使用电子券的套餐分类
	function set_tcate(){
		$tcate = I('tcate');
		return implode(',',$tcate);
	}
	//设置不可使用优惠券的时间
	function set_notDate(){
		$date = I('notDate');
		$arr = explode("\r\n",$date);
		foreach($arr as $k=>$v){
			if($arr[$k] == ''){
				unset($arr[$k]);
			}
		}
		return implode("\r\n",$arr);
	}
}