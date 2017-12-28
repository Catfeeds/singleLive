<?php
namespace Admin\Model;
use Think\Model;
class RoomsModel extends Model {
	protected $_validate = [
	    ['roomName','require','请填写类型名称'],
	];
	protected $_auto = [
		['createTime','time',self::MODEL_INSERT,'function'],
		['updateTime','time',self::MODEL_BOTH,'function'],
	];
}