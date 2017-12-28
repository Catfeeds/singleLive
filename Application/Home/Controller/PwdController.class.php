<?php
namespace Home\Controller;
use Think\Controller;
use Think\Faster;
//密码修改
class PwdController extends CommonController {
	public function index()
	{
		$sid = session('hotel_user.id');
		$hoteladmin = D('HotelAdmins');
		if(IS_POST){
			if($hoteladmin->create()){
				$pwd = md5(I('password'));
				$hoteladmin->where('id='.I('id'))->setField('password',$pwd);
				$this->success('修改成功，请重新登录',U('Index/login'));
			}else{
				$this->error($hoteladmin->getError());
			}
		}
		$this->assign('sid',$sid);
		$this->display();
	}
}