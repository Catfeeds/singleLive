<?php
namespace Admin\Model;
use Think\Model;
use Think\D;
class RechargeModel extends Model {
	protected $_validate = [
	    ['money','/^(?!0$|0\.00|0\.0|0\d+$)([1-9]?\d+(\.\d*)|(\\s&&[^\\f\\n\\r\\t\\v])|([1-9]*[1-9][0-9]*)?)$/','充值金额必须为正数',1,'regex'],
	    ['sorce','/^(?!0$|0\.00|0\.0|0\d+$)([1-9]?\d+(\.\d*)|(\\s&&[^\\f\\n\\r\\t\\v])|([1-9]*[1-9][0-9]*)?)$/','赠送积分必须为正数',1,'regex'],
	];
	protected $_auto = [
		['status',1],
		['createTime','time',1,'function'],
		['updateTime','time',2,'function']
	];
}