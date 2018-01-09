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
		['users','set_users',self::MODEL_INSERT,'callback'],
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
	//群发 指定用户
	function set_users(){
		$obj = I('obj');
		$mobile = I('mobile');
		if($obj == 'single'){
			$uid = D::field('Users.id',['where'=>['mobile'=>$mobile]]);
			return $uid;
		}else{
			return 0;
		}

	}
}