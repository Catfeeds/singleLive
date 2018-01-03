<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;

//房间类型模块
class HouseTypeController extends CommonController {
	public $model = 'HouseCate';
	public $success = ['insert' => '新增类型成功'];
	public function _map(&$data)
	{
		$map['status'] = ['neq',3];
		$data = [
		'where' => $map,
		'order' => 'add_time DESC'
		];
	}
	public function index()
	{
		parent::index(function($data){
			$data['createTime'] = date_out($data['createTime']);
			return $data;
		});
	}
	public function _before_add()
	{
		C('LAYOUT_ON',false);
	}
	public function _before_edit()
	{
		C('LAYOUT_ON',false);
	}
	public function delete()
	{
		parent::delete(['status' => 3]);
	}
}
