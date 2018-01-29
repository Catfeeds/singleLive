<?php
namespace Admin\Model;
use Think\Model;
use Think\D;
class TempleteModel extends Model {
	protected $_validate = [
	    ['start','require','请选择要设置的开始时间'],
		['end','require','请选择要设置的结束时间'],
	];
	protected $_auto = [
		['status','1'],
		['createTime','time',1,'function'],
	];

}