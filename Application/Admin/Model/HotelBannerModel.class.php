<?php
namespace Admin\Model;
use Think\Model;
class HotelBannerModel extends Model {
	protected $_validate = [
		['imgs','get_img','请上传banner图',0,'callback',1]
	];
	protected $_auto = [
		['add_time','time',1,'function'],
		['update_time','time',2,'function'],
	];
	function get_img(){
		if(I('imgs') == null){
			return false;
		}else{
			return true;
		}
	}
}