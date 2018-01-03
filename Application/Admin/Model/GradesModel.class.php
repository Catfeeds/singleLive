<?php
namespace Admin\Model;
use Think\Model;
use Think\D;
class GradesModel extends Model {
	protected $_validate = [
	    ['title','require','请填写级别名称'],
	    ['sorce','require','请填写该级别所需的积分'],
	    ['content','require','请填写级别描述'],
	];
	protected $_auto = [
		['sort','set_sort',self::MODEL_INSERT,'callback'],
	];
	//设置排序
	function set_sort(){
		$sort = D::field('Grades.Max(sort)');
		if($sort){
			return $sort+1;
		}else{
			return 1;
		}
	}
}