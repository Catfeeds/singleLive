<?php
namespace Admin\Model;
use Think\Model;
use Think\D;
class TempletePriceModel extends Model {
	protected $_validate = [
	    ['price1','require','请填写工作日价格'],
	    ['price2','require','请填写周六日价格'],
	    ['choose','check_choose','您存在没有选择的日期或没有填写的价格',1,'callback'],
	];
	protected $_auto = [
		['status','1'],
		['createTime','time',1,'function'],
	];
	function check_choose($data){
		$post = I('post.');
		$bool = true;
		if($data == 2){
			$bool = true;
		}else{
			foreach ($post['day'] as $val){
				if($val){
					$bool = true;
				}else{
					$bool = false;
				}
			}
			foreach ($post['price3'] as $value){
				if($val){
					$bool = true;
				}else{
					$bool = false;
				}
			}
		}
		return $bool;
	}

}