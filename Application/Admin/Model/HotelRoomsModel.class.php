<?php
namespace Admin\Model;
use Think\Model;
class HotelRoomsModel extends Model {
	protected $_validate = [
	    ['room','require','请选择房间类型',1,'',1],
	    ['room','get_room','一个酒店下每种房间类型只能存在一个',1,'callback',1],
	    ['amount','require','请填写单价'],
	    ['minimum','require','请填写最低入住时长'],
	    ['minute','require','请填写计时节点'],
		['imgs_ids','get_img','请上传房间类型图片',1,'callback',1]
	];
	protected $_auto = [
		['amount','get_amount',3,'callback'],
		['createTime','time',1,'function'],
		['updateTime','time',2,'function'],
	];
	function get_img(){
		if(I('imgs_ids') == null){
			return false;
		}else{
			return true;
		}
	}
	function get_room(){
		$map['status'] = 0;
		$map['room'] = I('room');
		$map['hotel'] = I('hotel');
		$id = M('HotelRooms')->where($map)->getField('id');
		if($id){
			return false;
		}else{
			return true;
		}
	}
	//以用户的输入的24小时价格，计算1小时单价
	function get_amount(){
		$price = I('price');
		return round($price/24,4);
	}
}