<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;
//房间类型模块
class RoomTypeController extends CommonController {
	public $model = 'Rooms';
	public $success = ['insert' => '新增类型成功'];
	public function _map(&$data)
	{
		$map['status'] = ['neq',9];
		$data = [
		'where' => $map,
		'order' => 'createTime DESC'
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
	/**
	 * [delete 逻辑删除]暂定
	 * @Author   尹新斌
	 * @DateTime 2017-07-11
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function delete()
	{
		parent::delete(['status' => 9]);
	}
}
