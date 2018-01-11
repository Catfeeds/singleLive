<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;

class RechargeController extends CommonController {
	public $model = 'Recharge';
	public function _map(&$data){
		$data = [
			'where' => "`status`=1",
			'order' => 'createTime desc'
		];
	}
	public function del(){
		M($this->model)->where("id=".I('id'))->setField('status',3);
		$this->success('删除成功',U('Recharge/index'));
	}

}