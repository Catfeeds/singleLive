<?php
namespace Admin\Model;
use Think\Model;
use Think\D;
class GradesModel extends Model {
	protected $_validate = [
	    ['title','require','请填写级别名称'],
	    ['sort','check_sorts','级别等级顺序已存在或格式不正确,请先此顺序的级别',1,'callback',1],
	    ['sorce','require','请填写该级别所需的积分'],
	    ['content','require','请填写级别描述'],
	];
	protected $_auto = [
		['status','1']
	];
	//设置等级顺序
	function check_sorts($data){
		$gradeSort = D::lists('Grades','sort',['where'=>['status'=>1]]);
		if(preg_match("/^[1-9][0-9]*$/",$data) == 1 && !in_array($data,$gradeSort)){
			return true;
		}else{
			return false;
		}
	}

}