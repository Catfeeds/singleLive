<?php
namespace Admin\Model;
use Think\Model;
use Think\D;
class UserSorceModel extends Model {
	protected $_validate = [
	    ['method','require','请选择修改方式'],
	    ['sorce','/^[+]{0,1}(\d+)$/','积分值必须为正整数',1,'regex']
	];
	protected $_auto = [
		['createTime','time','1','function']
	];

}