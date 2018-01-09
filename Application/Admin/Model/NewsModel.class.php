<?php
namespace Admin\Model;
use Think\Model;
use Think\D;
class NewsModel extends Model {
	protected $_validate = [
	    ['obj','require','请选择发布范围',0],
	    ['mobile','check_mobile','输入的手机号必须是已经注册成功的用户',0,'callback'],
	    ['title','require','请填写消息标题'],
	    ['body','require','请填写消息内容'],
	];
	protected $_auto = [
		['status',1],
		['createTime','time',self::MODEL_INSERT,'function'],
		['updateTime','time',self::MODEL_BOTH,'function'],
	];
	function check_mobile($data){
		$mobile = D::field('Users.mobile',['where'=>['mobile'=>$data]]);
		if($mobile){
			return true;
		}else{
			return false;
		}
	}
}