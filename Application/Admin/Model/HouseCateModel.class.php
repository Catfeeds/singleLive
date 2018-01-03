<?php
namespace Admin\Model;
use Think\Model;
class HouseCateModel extends Model {
	protected $_validate = [
	    ['title','require','请填写类型名称'],
	    ['mark','require','请填写备注信息'],
	];
	protected $_auto = [
		['status',1],
		['add_time','time',self::MODEL_INSERT,'function'],
		['update_time','time',self::MODEL_BOTH,'function'],
	];
}