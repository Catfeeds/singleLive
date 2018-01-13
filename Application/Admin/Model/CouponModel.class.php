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
		['notDate','check_notDate','日期格式设置出现错误,月份在(1-12)之间,日期在(1-31)之间',0,'callback'],
	    ['sorce','require','请设置兑换所需积分'],
	    ['sorce','check_sorce','兑换所需积分必须为正整数',0,'callback'],
	];
	protected $_auto = [
		['status',1],
		['year','set_year',self::MODEL_INSERT,'callback'],
		['notDate','format_date',self::MODEL_BOTH,'callback'],
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
	//检查设置的日期格式
	function check_notDate($data){
		$data = $this->format_date($data);
		$arr = explode("\r\n",$data);
		foreach($arr as $row){
			if(!strtotime($row)){
				return false;
			}
		}
		return true;
	}
	//设置日期格式
	public function format_date($data){
		$date_ary = explode("\r\n",$data);
		$date_data = [];
		foreach($date_ary as $key => &$data){
			if(!empty($data)){
				$arr = explode('-',$data);
				$date = [];
				foreach($arr as $k=> &$v){
					if(is_numeric($v) && !empty($v) ){
						$date[] = sprintf("%02d", $v);//格式化字符串
					}
				}
				count($date) > 1 && count($date) < 3 ? array_unshift($date, date('Y',time())) : '';
				count($date) > 1 ? $date_data[] = implode('-', $date) : '';
			}
		}
		return implode("\r\n", $date_data);
	}
}