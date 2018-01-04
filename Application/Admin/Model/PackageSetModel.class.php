<?php
namespace Admin\Model;
use Think\Model;
class PackageSetModel extends Model {
	protected $_validate = [
	    ['title','require','请填写套餐内容名称'],
		['money','require','请填写套餐内容单价'],
	    ['attr','require','请填写规格/数量'],
	    ['attr','check_attr','规格/数量必须为正整数',0,'callback'],
	];
	function check_attr($data){
		if(preg_match("/^[+]{0,1}(\d+)$/",$data) ==1){
			return true;
		}else{
			return false;
		}
	}
}