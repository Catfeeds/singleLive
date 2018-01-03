<?php
namespace Admin\Model;
use Think\Model;
class EnvironmentModel extends Model {
	protected $_validate = [
	    ['title','require','请填写标题名称'],
	    ['mark','require','请填写描述'],
	];
	protected $_auto = [
		['add_time','time',self::MODEL_INSERT,'function'],
		['update_time','time',self::MODEL_BOTH,'function'],
	];
}