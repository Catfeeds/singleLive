<?php
namespace Home\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;
//酒店信息
class HotelInfoController extends CommonController {
	public function index()
	{
		$hotels = D('Hotels');
		$hotel = session('hotel_user.hotel');
		$data = D::find('Hotels',$hotel);
		if(IS_POST){
			$data = $hotels->create();
			$hotels->save($data);
			$this->success('修改成功');
		}
		$this->assign('data',$data);
		$this->display();
	}
}