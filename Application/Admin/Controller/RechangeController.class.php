<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;
//充值管理模块
class RechangeController extends CommonController {
	//会员充值
    public function index()
    {
    	$rows=D::get('hotels','status!=9 and status!=1');

    	$this->assign('rows',$rows);
        $this->display();
    }

    public function Phone()
    {

    	$row = D::find('users','mobile='.I('mobile').'');

    	echo $row['realname'];
    }

    public function hotel_room()
    {

    	$rows = D::get(['hotel_rooms','HR'],[
    			'join' => '__ROOMS__ R on HR.room=R.id',
    			'where' => 'HR.hotel='.I('hotel').' and HR.status=0',
    			'field' => 'HR.id,R.roomName,HR.price'
    		]);
    	foreach ($rows as $key => $row) {

    		$rows[$key]['roomName'] = urlencode($row['roomName']);	
    	}
    	
    	echo urldecode(json_encode($rows));
    }

    //会员充值执行
    public function rechargeDo()
    {
    	if(!I('realname')){
    		$this->error('未获取到会员信息，请填写正确的会员手机号！');
    	}else if(!I('rooms')||!I('hotels')){
    		$this->error('请选择酒店和房间类型！');
    	}else if(I('amount')=='NaN'){
    		$this->error('请输入正确的购买天数！');
    	}

    	$hotelInfo = D::find('hotels',I('hotels'));
    	$roomInfo =  D::find(['hotel_rooms','HR'],[
                'where' =>'HR.id='. I('rooms').'',
                'join' => '__ROOMS__ R on HR.room=R.id',
                'field' => 'HR.id,R.roomName,HR.price'  
            ]);
    	$userInfo =  D::find('users','mobile='.I('mobile').'');

    	$number = 'H'.$userInfo['id'].time();
    	$Ary = [
    		'no' => $number,
    		'userId' => $userInfo['id'],
    		'hotel' => $hotelInfo['id'],
    		'room' => $roomInfo['id'],
    		'duration' => I('duration')*24,
    		'amount' => I('amount')/(I('duration')*24),
    		'status' => 0,
    		'createTime' => time()
    	];

    	$recordAry = [
    		'no' => $number,
    		'hotelName' => $hotelInfo['hotelName'],
    		'roomName' => $roomInfo['roomName'],
    		'realname' => $userInfo['realname'],
    		'mobile' => I('mobile'),
    		'deduct' => I('duration')*24,
    		'amount' => I('amount'),
    		'createTime' => time()    		    		
    	];

    	if(D::add('order',$Ary)&&D::add('user_orders',$recordAry)){
    		$this->success('充值成功！',U('Rechange/index'));
    	}else{
    		$this->error('充值失败，请重试！');
    	}

    }

}
