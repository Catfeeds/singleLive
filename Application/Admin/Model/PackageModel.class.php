<?php
namespace Admin\Model;
use Think\Model;
class PackageModel extends Model {
	protected $_validate = [
	    ['category','require','请选择套餐分类'],
	    ['total_num','require','请填写房间总数'],
	    ['total_num','check_total','请房间总数必须为正整数',0,'callback'],
		['title','require','请设置套餐名称'],
		['limit','require','请设置每人限购份数'],
		['limit','check_limit','限购份数必须为正整数',0,'callback'],
	    ['packMoney','require','请设置套餐金额'],
	    ['sorce','require','请设置反还积分'],
	    ['sorce','check_sorce','反还积分必须为正整数',0,'callback'],
	];
	protected $_auto = [
		['status',1],
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
	function check_limit($data){
		if(preg_match("/^[+]{0,1}(\d+)$/",$data) ==1){
			return true;
		}else{
			return false;
		}
	}
	function check_total($data){
		if(preg_match("/^[+]{0,1}(\d+)$/",$data) ==1){
			return true;
		}else{
			return false;
		}
	}
}