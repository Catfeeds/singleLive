<?php
namespace Mobile\Model;
use Think\Model;
use Think\D;
class CouponExchangeModel extends Model {
	protected $_auto = [
		['card','set_card',1,'callback'],
		['status',1],
		['createTime','time','1','function'],
		['updateTime','time','2','function']
	];
	function set_card(){
		return time().get_random_number(3);
	}

}