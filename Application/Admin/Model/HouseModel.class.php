<?php
namespace Admin\Model;
use Think\Model;
class HouseModel extends Model {
	protected $_validate = [
	    ['category','require','请选择客房分类'],
		['name','require','请设置客房名称'],
	    ['money','require','请设置客房金额'],
	    ['sorce','require','请设置反还积分'],
	    ['equipment','require','请输入房间设备'],
	    ['mark','require','请输入房间描述'],
	    ['back','require','请输入订房须知'],
	    ['change','require','请输入更改订单须知'],
	];
	protected $_auto = [
		['status',1],
		['add_time','time',self::MODEL_INSERT,'function'],
		['update_time','time',self::MODEL_BOTH,'function'],
	];
}