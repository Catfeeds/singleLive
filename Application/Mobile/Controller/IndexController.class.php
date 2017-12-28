<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\D;
class IndexController extends MobileCommonController {
	/**
	 * [index description]
	 * @Author   尹新斌
	 * @DateTime 2017-07-14
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function index()
	{
		//S('info',null);//清空订单缓存
		$id         = base64_decode(I('hotel'));//获取酒店ID
		if (!$id) {
//测试用
			if ($_SERVER['SERVER_NAME'] == '127.0.0.6') {
				session('user',D::find('Users',7));
			}
//测试用
			$this->redirect('Rooms/index','',0,'');die;
		}

		$images = array_map(function($data){
			return '/Uploads'.$data['savepath'].$data['savename'];
		},D::get('Files',['id' => ['in',D::field('HotelBanner.imgs',['hotel' => $id])]]));
		$hotelData  = D::find('Hotels',$id);
		if ($hotelData['status'] != '0') {
			header("Content-type: text/html; charset=utf-8");
			echo '<script type="text/javascript"> alert("此酒店已停用或被删除！"); </script>';die;
		}
		$hotelRooms = D::get(['HotelRooms','H'],[
			'where' => ['H.hotel' => $id,'H.status' => '0'],
			'join'  => 'LEFT JOIN __ROOMS__ R ON H.room = R.id',
			'order' => 'H.updateTime DESC',
			'field' => 'H.*,R.roomName'
			]);
		if (session('user')) {
			$count = D::count('Order',['userId' => session('user.id'),'hotel' => $id,'status' => 0]);
		}else{
			$count = 0;
		}
		$this->assign('orderNum',$count);
		$this->assign('id',base64_encode($id));
		$this->assign('banner',$images);
		$this->assign('hotelRooms',$hotelRooms);
		$this->assign('hotelData',$hotelData);
		$this->display();
	}
	/**
	 * [appOrder 生成订单]
	 * @Author   尹新斌
	 * @DateTime 2017-07-17
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function appOrder()
	{
		$post = session('appOrder')?:I('post.');
		if (!session('user')) {
			session('appOrder',$post);
            redirect(wx_url('',urlencode('Index/appOrder')),0,'');
        }else{
        	session('appOrder',null);
        }
        if ($post) {
        	$no = 'H'.session('user.id').NOW_TIME;
        	$allAmount = 0;
        	foreach ($post as $roomId => $nums) {
        		$room = D::find('HotelRooms',$roomId);
        		for ($i=0; $i < $nums['room']; $i++) {
        			$add_data = [
						'no'         => $no,
						'userId'     => session('user.id'),
						'hotel'      => $room['hotel'],
						'room'       => $room['id'],
						'duration'   => $nums['days'] * 24,
						'used'       => 0,
						'amount'     => $room['amount'],
						'all_amount' => $room['price'] * $nums['days'],
						'status'     => 8,
						'createTime' => NOW_TIME,
						'updateTime' => NOW_TIME,
        			];
        			$allAmount = ($room['price'] * $nums['days']) + $allAmount;
					//循环插入订单信息
					$flag = D('Order')->add($add_data);
					//组装  订单金额表信息  为了后台退款时，只能退买的房间，不能退 续时和换房的房间
					$arr[] = array(
						'orderNo' => $no,
						'orderId' => $flag,
						'money' => $room['price'] * $nums['days'],
						'type' => 'buy'
					);
        		}
        	}
        	if ($arr) {
        		/*$flag = D::add('Order',$add_data);
				redirect($url, 0, '页面跳转中...');*/
				//$str_attach = base64_encode('buy');
				$url = '/WeiXinPay/example/jsapi.php?title=房间预订&orderNo='.$no.'&amount='.($allAmount * 100).'&';
				/*临时的支付跳转链接*/
				// $url = '/Index/pay?title=房间预订&orderNo='.$no.'&amount='.$allAmount;
				redirect($url, 0, '页面跳转中...');
				exit;
        	}
        }else{
        	# code...
        }
	}
	//支付成功后回调
	public function pay(){
		//根据微信订单号判断操作方式	
		$orderNo = I('orderNo');
		$strs = substr($orderNo,0,1);
		switch ($strs) {
			//正常购买
			case 'H':
				$map['no'] = I('orderNo');
				$status = D::field('Order.status',$map);
				if ($status == '8') {
					D::save('Order',$map,[
						'status' => '0',
						'updateTime' => NOW_TIME,
						]);
					//插入订单金额表
					$sel = array(
						'A.no'=>I('orderNo'),
						'A.status' => '0'
					);
					$datas = D::get(['Order','A'],[
						'where'=> $sel,
						'join' => 'LEFT JOIN __HOTEL_ROOMS__ B ON B.id = A.room',
						'field' => 'A.id,A.no,A.duration,B.price'
					]);
					//组装订单金额表数据
					foreach ($datas as $key => &$value) {

						$value['money'] = ($value['duration']/24)*$value['price'];
						$value['orderId'] = $value['id'];
						$value['orderNo'] = $value['no'];
						$value['type'] = 'buy';
						$value['add_time'] = time();

						$id = D::add('OrderMoney',$value);

					/*
						提现更改 现在每一笔支付都需要写入订单金额表,状态 未激活 入住后才可提现
					 */
						$row = [
							'order' => $id,
							'amount' => $value['money'],
							'hotel' => D::field('Order.hotel', $value['orderId']),
							'status' => 9,
							'createTime' => time(),
						];
						D::add('PostalWait', $row);						
					}

				}
				$this->redirect('Index/showSuccess',[],0,'');
				break;
			//换房	
			case 'C':
				$map['no'] = I('orderNo');
				$str = D::field('Order.payfields',$map);
				//这里的数据必须与传过来的数据全部对应（查看Rooms里的appOrder的$call_data）
				$arr = explode(',',$str);
				$status_old = D::field('Order.status',$arr[1]);
				if($status_old != '9'){
					//逻辑删除  原订单
		            D::set('Order.status',$arr[1],'9');
		            //查询原订单--是否入住过
		            $checkIn = D::field('Order.checkIn',$arr[1]);
		            //更新   换房产生的新订单
	                D::save('Order',$arr[2],[
	                    'status' => '0',
	                    'checkIn' => $checkIn,
	                    'updateTime' => NOW_TIME
	                ]);
	            	//插入订单金额表记录
			         $data = [
		                'orderId' => $arr[2],
			        	'orderNo' => $arr[3],
			        	'money'  =>  $arr[4],
			        	'type'  => 'change',
			        	'add_time' => NOW_TIME
				        ];
			        $id = D::add('OrderMoney',$data);
			        /*
			        	换房后的订单要继承原订单的付款记录
			         */
			        $where['orderId'] = $arr[1];
			        $order['orderId'] = $arr[2];
			        $order['orderNo'] = $arr[3];
			        D::save('OrderMoney', $where, $order);
					/*
						提现更改 现在每一笔支付都需要写入订单金额表,状态 未激活 入住后才可提现
					 */
					$row = [
						'order' => $id,
						'amount' => $arr[4],
						'hotel' => D::field('Order.hotel', $arr[2]),
						'status' => 9,
						'createTime' => time(),
					];

					D::add('PostalWait', $row);		

	            }
	            $this->redirect('Index/showSuccess',[],0,'');
				break;
			//续时	
			case 'X':
				//这里的数据必须与传过来的数据全部对应（查看Rooms里的continue_pay的$call_data）
				$strs = explode('S',I('orderNo'));
				//1-订单id
				$strings = D::field('Order.payfields',$strs[1]);
				$arr = explode(',', $strings);
				$time = D::field('Order.duration',$arr[1]);
				if($time <= $arr[7]){
					$datas = array(
						'no' => $arr[2],
						'duration' => $arr[4],
						'all_amount' => $arr[5],
						'updateTime' => NOW_TIME,
					);
					D('Order')->where("id=".$arr[1])->save($datas);
					//续时给用户发送信息
			        $msg = search_order_msg($arr[1]);
			        $title = '续时提示';
			        $continue_time = $arr[6]*24;
			        $content = '尊敬的用户：'.$msg['realname'].'您于'.date('Y-m-d H:i:s').'为'.$msg['hotelName'].'房间类型为'.$msg['roomName'].'续时'.$continue_time.'小时，'.'之前剩余'.$arr[8].'小时,总剩余'.$arr[9].'小时';
			        send_user_msg($msg['id'],$title,$content);
			        //插入订单金额表
			        $data = [
			            'orderId' => $arr[1],
			            'orderNo' => $arr[2],
			            'money'  =>  $arr[3],
			            'type'  => 'continue',//表示续住
			            'add_time' => NOW_TIME
			        ];
			        $id = D::add('OrderMoney',$data);

					/*
						提现更改 现在每一笔支付都需要写入订单金额表,状态 未激活 入住后才可提现
					 */
					$row = [
						'order' => $id,
						'amount' => $arr[3],
						'hotel' => D::field('Order.hotel', $arr[1]),
						'status' => 9,
						'createTime' => time(),
					];
					D::add('PostalWait', $row);		

				}
		        $this->redirect('Index/showSuccess',[],0,'');
				break;	
		}
		
	}
	/**
	 * [orderSuccess 订单成功页面]
	 * @Author   尹新斌
	 * @DateTime 2017-07-17
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function orderSuccess()
	{
		$orderId = I('order');
		$map['no'] = $orderId;
		D::save('Order',$map,[
				'status' => '0',
				'updateTime' => NOW_TIME,
		]);
		$this->display();
	}
	public function showSuccess()
	{
		//后台设置公众号二维码提取位置
		//后台设置公众号二维码提取位置
		$this->display();
	}
	/*
	 * 	@Author   zhenHong
     *  查询当前用户所有订单，
     *  离购买时间差：
     *      1小时     30分钟    5分钟  都要发送消息给用户
     		此方法，用lunix服务器定时任务写
     * */
	/*public function search_send_msg(){
		$uid = I('userId');
		//查詢前用戶--正在入住的订单
		$map = array(
			'OH.status' => 2,
			'OH.userId' => $uid
		);
		$list = D::get(['OrderHotel','OH'],[
			'where' => $map,
			'join' => [
				'LEFT JOIN __ORDER__ A ON A.id = OH.orderId',
				'LEFT JOIN __USERS__ C ON C.id = A.userId',
				'LEFT JOIN __HOTEL_ROOMS__ D ON D.id = A.room',
				'LEFT JOIN __ROOMS__ R ON R.id = D.room',
				'LEFT JOIN __HOTELS__ E ON E.id = A.hotel'
			],
			'field' => 'C.id,C.realname,E.hotelName,R.roomName,OH.startTime,OH.`no`,A.duration,A.used,(A.duration*60*60) `second`,(A.duration-A.used) have_time'
		]);
		foreach ($list as $k=>$data){
			if($list[$k]['used'] == 0){
				$list[$k]['time'] = ($list[$k]['startTime'] + $list[$k]['second'])-NOW_TIME;
			}elseif($list[$k]['used']>0 && $list[$k]['have_time']>0){
				$list[$k]['time'] = ($list[$k]['startTime']+$list[$k]['have_time']*60*60)-NOW_TIME;
			}
		}
		$list = array_map(function($data){
			$title = '温馨提示';
			if($data['time'] >= 3590 && $data['time']<=3610){
				$content = '尊敬的用户'.$data['realname'].'您所入住的'.$data['hotelName'].'房间类型为'.$data['roomName'].'订单号为'.$data['no'].'离入住到期时间还有1小时请您及时收拾好东西,联系酒店前台方便您办理退房';
				send_user_msg($data['id'],$title,$content);
			}elseif($data['time']>=1790 && $data['time']<=1800){
				$content = '尊敬的用户'.$data['realname'].'您所入住的'.$data['hotelName'].'房间类型为'.$data['roomName'].'订单号为'.$data['no'].'离入住到期时间还有半小时请您及时收拾好东西,联系酒店前台方便您办理退房';
				send_user_msg($data['id'],$title,$content);
			}elseif($data['time']>=290 && $data['time']<=300){
				$content = '尊敬的用户'.$data['realname'].'您所入住的'.$data['hotelName'].'房间类型为'.$data['roomName'].'订单号为'.$data['no'].'离入住到期时间还有5分钟了请您及时收拾好东西,联系酒店前台方便您办理退房';
				send_user_msg($data['id'],$title,$content);
			}
		},$list);
		$info['sta'] = 1;
		$this->ajaxReturn($info);
	}*/
}