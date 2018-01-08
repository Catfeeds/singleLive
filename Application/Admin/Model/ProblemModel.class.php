<?php
namespace Admin\Model;
use Think\Model;
class ProblemModel extends Model {
	protected $_validate = [
		['title','require','请填写问题名称'],
		['content','require','请填写问题回答']
	];
	protected $_auto = [
		['add_time','time',self::MODEL_INSERT,'function'],
		['update_time','time',self::MODEL_BOTH,'function'],
	];
}