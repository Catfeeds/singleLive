<?php
namespace Admin\Model;
use Think\Model;
class HotelsModel extends Model {
	protected $_validate = [
	    ['username','require','请填写用户名'],
	    ['password','require','请设置密码'],
	    ['repassword','require','请重复设置密码'],
	    ['repassword','password','密码两次输入不一致',0,'confirm'],
	    ['hotelName','require','请填写酒店名称'],
	    ['province','require','请完善酒店所在地'],
	    ['city','require','请完善酒店所在地'],
	    ['area','require','请完善酒店所在地'],
	    ['address','require','请填写酒店地址'],
	    ['mobile','require','请填写酒店联系电话'],
	    ['head','require','请上传酒店头像'],
	];
	protected $_auto = [
		['createTime','time',self::MODEL_INSERT,'function'],
		['updateTime','time',self::MODEL_BOTH,'function'],
	];
}