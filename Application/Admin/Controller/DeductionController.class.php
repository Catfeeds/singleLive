<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;
//扣除管理模块
class DeductionController extends CommonController {
	//会员扣除
    public function index()
    {
    	$rows=D::get('hotels','status!=9 and status!=1');

    	$this->assign('rows',$rows);
        $this->display();
    }

    public function Select()
    {
    	$userRow = D::find('users','mobile='.I('mobile').'');

    	$orderRows = D::get('order','userId='.$userRow['id'].' and hotel='.I('hotel').' and room='.I('room').' and status=0');

    	echo json_encode($orderRows);
    }

    //会员扣除执行
    public function deductionDo()
    {
    	$Time = "";
    	foreach ($_POST['order'] as $value) {
    		$row = D::find('order',$value);

    		$Time += $row['duration'] - $row['used'];
    	}

	 	if(!I('realname')){
    		$this->error('未获取到会员信息，请填写正确的会员手机号！');
    	}else if(!I('rooms')||!I('hotels')){
    		$this->error('请选择酒店和房间类型！');
    	}else if($Time<I('duration')*24){
    		$this->error('对不起，您的时间余额不足');
    	}

    	$hotelInfo = D::find('hotels',I('hotels'));
        $roomInfo =  D::find(['hotel_rooms','HR'],[
                'where' =>'HR.id='. I('rooms').'',
                'join' => '__ROOMS__ R on HR.room=R.id',
                'field' => 'HR.id,R.roomName,HR.price'
            ]);
    	$userInfo =  D::find('users','mobile='.I('mobile').'');


    	$number = 'H'.$userInfo['id'].time();
    	$recordAry = [
    		'no' => $number, 						//记录编号
    		'hotelName' => $hotelInfo['hotelName'],	//酒店名称
    		'roomName' => $roomInfo['roomName'],	//房间类型
    		'realname' => $userInfo['realname'],	//会员名称
    		'mobile' => I('mobile'),				//手机号
    		'deduct' => I('duration')*24,			//扣除时间数/小时
    		'amount' => I('amount'),				//扣除金额
    		'createTime' => time()    		    	//扣除时间
    	];

		//向扣除记录表中添加数据
    	D::add('deduct_record',$recordAry);

    	//比较需要扣除的时间和订单中所剩余的时间
    	$duration = I('duration')*24;
    	foreach ($_POST['order'] as $value) {

    		$row = D::find('order',$value);

    		if($row['duration']-$row['used']>$duration&&$duration){

	    		$Ary = [
	    			'used' => $duration+$row['used']
	    		];

    			D::save('order',$value,$Ary);

    		}else if($duration){

    			$Ary = [
    				'used' => $row['duration'],//将所有时间变为已用
    				'status' => 1        	   //并将状态变为过期
    			];

    			D::save('order',$value,$Ary);

    			$duration -= $row['duration']-$row['used'];
    		}
    	}

    	$this->success('扣除成功！',U('Deduction/index'));
    }
}
