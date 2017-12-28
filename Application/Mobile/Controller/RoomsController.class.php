<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\D;
class RoomsController extends MobileCommonController {
    public static $open = false;//控制器不开放,需登录
    public $model = ['Order','O'];
    public function _map(&$data)
    {
        $map = [
                'userId' => session('user.id'),
                'status' => ['in', ['0', '7']],
                ];
        $nos = D::get('Order',['where' => $map,'group' => 'no'],false);
        $data = [
        'table' => $nos,
        'order' => 'O.status ASC,O.updateTime DESC',
        ];

    }
    public function index()
    {
    	if (IS_AJAX) {
            parent::index(function($data){
                $map = [
                'O.no'     => $data['no'],
                'O.status' => ['in', ['0', '7']],
                ];
                $data['roomLists'] = array_map(function($data){
                    $data['img'] = getSrc($data['head']);
                    $data['roomName'] = D::field('Rooms.roomName',$data['roomid']);
                    $data['have'] = formatTime($data['duration'] - $data['used']);
                    return $data;
                },D::get(['Order','O'],[
                    'where' => $map,
                    'join' => [
                    'LEFT JOIN __HOTELS__ H ON H.id = O.hotel',
                    'LEFT JOIN __HOTEL_ROOMS__ R ON R.id = O.room'
                    ],
                    'field' => 'O.*,H.hotelName,R.room roomid,H.head,R.minimum',
                    ]));
                $data['createTime'] = date('Y-m-d H:i:s',$data['createTime']);
                return $data;
            });
    	}else{
    		$this->display();
    	}
    }
    /**
     * [check 入住申请]
     * @Author   尹新斌
     * @DateTime 2017-07-19
     * @Function []
     * @return   [type]     [description]
     */
    public function check()
    {
        $id = I( 'id' );
        $self = D::find( 'Order' , $id );
        if ( $self['status'] != '0' ) {
            $this->error( '此房间已入住成功或房间不存在' );exit;
        }
        $roomConfig = D::find( 'HotelRooms' , $self['room'] );
        /*if ( $roomConfig['minimum'] > ( $self['duration'] - $self['used'] ) ) {
            $this->error( '此房间可用时长不足该房间的最低入住时长' );exit;
        }*/
        if(( $self['duration'] - $self['used'] ) < 24){
             $this->error( '此房间可用时长不足24小时,请先续时' );exit;
        }
        $orderNo = 'A'.session('user.id').NOW_TIME;//订单编号算法
        $add_data = [
            'no'         => $orderNo,
            'userId'     => session('user.id'),
            'orderId'    => $id,
            'used'       => 0,
            'status'     => 0,
            'createTime' => NOW_TIME,
            'startTime'  => 0,
            'endTime'    => 0,
        ];
        $newDataId = D::add( 'OrderHotel' , $add_data );
        if ($newDataId) {
            $flag = D::save( 'Order' , $id , [
                'status' => 2,
                ] );
            if ( $flag ) {
                $this->success('入住申请成功，请在之后的页面查看订单号告知酒店前台',U('Order/index'));
            }else{
                $this->error('不明错误，请刷新页面后重试');
            }
        }else{
            $this->error('不明错误，请刷新页面后重试');
        }
    }
    /*
     *  续住操作
     *
     * */
    public function continue_stay(){
        $orderId = I('orderid');
        $order = D::find(['Order','OH'],[
            'where' => ['OH.id' => $orderId],
            'join' => [
                'LEFT JOIN __HOTEL_ROOMS__ B ON B.id = OH.room',
                'LEFT JOIN __ROOMS__ A ON A.id = B.room'
            ],
            'field' => 'OH.*,B.price,A.roomName,B.amount'
        ]);
        $order['useable'] = $order['duration'] - $order['used'];
        $this->assign('msg',$order);
        $this->display();
    }
    /*
     *  续住操作-支付
     * */
    public function continue_pay(){
        $no = 'X'.random_num().'S'.I('orderId');
        //$no = D::field('Order.no',I('orderId'));
        //原订单的够买时间
        $duration = D::field('Order.duration',I('orderId'));
        //已经用的时长
        $used = D::field('Order.used',I('orderId'));
        //总时间
        $all = intval($duration) + (I('daysnum')*24);

        $call_data = array(
            'sign' => 'continue_no',
            'orderId' => I('orderId'),
            'orderNo' => $no,
            'money' => I('all_amounts'),
            'duration' => $all,
            'all_amount' => D::field('Order.all_amount',I('orderId')) + I('all_amounts'),
            'daysnum' => I('daysnum'),
            'old_duration' => $duration,
            //原订单剩余时长
            'surplus' => $duration - $used,
            //总剩余时长
            'old_surplus' => $all-$used
        );
        $str_attach = implode(',', $call_data);
        D::set('Order.payfields',I('orderId'),$str_attach);
        $url = '/WeiXinPay/example/jsapi.php?title=续时&orderNo='.$no.'&amount='.(I('all_amounts') * 100).'&';
        redirect($url, 0, '页面跳转中...');
        
        /*$info = I('post.');
        $info['no'] = D::field('Order.no',$info['orderId']);
        $duration = D::field('Order.duration',I('id'));
        //总时间
        $all = intval($duration) + (I('daysnum')*24);
        $datas = array(
            'duration' => $all,
            'updateTime' => time()
        );

        //新增需求，续住和换房的时候要将上一个订单的累计金额相加
        //主要用作提现 当点击退房操作时会将订单累计金额清零，代表此单已提现
        $datas['all_amount'] = D::field('Order.all_amount',I('id')) + I('all_amounts');


        //更新order表的购买时间
        D('Order')->where("id=".I('id'))->save($datas);

        //续时给用户发送信息
        $orderId = I('id');
        $msg = search_order_msg($orderId);
        $title = '续时提示';
        $continue_time = I('daysnum')*24;
        $content = '尊敬的用户：'.$msg['realname'].'您于'.date('Y-m-d H:i:s').'为'.$msg['hotelName'].'房间类型为'.$msg['roomName'].'续时时长为'.$continue_time.'小时，'.'原时长为'.$duration.'小时,总时长为'.$all.'小时';
        send_user_msg($msg['id'],$title,$content);
        //插入订单金额表
        $data = [
            'orderId' => I('id'),
            'orderNo' => I('no'),
            'money'  => I('all_amounts'),
            'type'  => 'continue',//表示续住
            'add_time' => NOW_TIME
        ];
        D::add('OrderMoney',$data);
        $this->success('支付成功',U('Rooms/index'));
        $this->assign('info',$info);
        $this->display();*/
    }
    /**
     *  换房
     * @Author zhenhong
     */
    public function change(){
        $orderId = I('orderid');
        $order = D::find(['Order','OH'],[
            'where' => ['OH.id' => $orderId],
            'join' => [
                        'LEFT JOIN __HOTEL_ROOMS__ B ON B.id = OH.room',
                        'LEFT JOIN __ROOMS__ A ON A.id = B.room'
                      ],
            'field' => 'OH.*,B.price,A.roomName,B.amount,A.id roomId'
        ]);
        //这条订单的可用时间
        $order['useable'] = $order['duration'] - $order['used'];
        $order['money'] = round($order['useable']*$order['amount'],0);
        $order['roomlist']= D::get(['HotelRooms','A'],[
            'where' => [
                    'A.hotel'=>$order['hotel'],
                    'A.status'=>0,
                    'B.id' => array('neq',$order['roomId'])
                    ],
            'join' => 'LEFT JOIN __ROOMS__ B ON B.id=A.room',
            'field' => 'A.*,B.roomName'
        ]);
        $this->assign('order',$order);
        $this->display();
    }
    //生成订单
    public function appOrder()
    {
        $post = session('appOrder')?:I('post.');
        //查询原始房间订单
        $old = D::find(['Order','OH'],[
            'where' => ['OH.id' => $post['orderId']],
            'join' => [
                'LEFT JOIN __HOTEL_ROOMS__ B ON B.id = OH.room',
                'LEFT JOIN __ROOMS__ A ON A.id = B.room'
            ],
            'field' => 'OH.*,B.price,A.roomName,B.amount'
        ]);
        if (!session('user')) {
            session('appOrder',$post);
            redirect(wx_url('',urlencode('Rooms/appOrder')),0,'');
        }else{
            session('appOrder',null);
        }
        if ($post) {
            $no = 'C'.session('user.id').NOW_TIME;
            $room = D::find('HotelRooms',$post['roomId']);
            $add_data = array();
            $add_data['no'] = $no;
            $add_data['userId'] = session('user.id');
            $add_data['hotel'] = $room['hotel'];
            $add_data['room'] = $room['id'];
            //不论花不花钱,都看总时长(前台change.html已写)
            $add_data['duration'] = $post['all_times'];
            $add_data['used'] = 0;
            $add_data['amount'] = $room['amount'];
            $add_data['status'] = 8;
            $add_data['createTime'] = NOW_TIME;
            $add_data['updateTime'] = NOW_TIME;

            //新增需求，换房的时候要将上一个订单的累计金额相加
            //主要用作提现 当点击退房操作时会将订单累计金额清零，代表此单已提现
            //如果上一单还有累计金额代表它还未入住，所以加到下一单上
            $add_data['all_amount'] = $old['all_amount'] + $post['all_amounts'];
            if($post['all_amounts']){
            	//得花钱
                if ($add_data) {
                    $newId = D::add('Order',$add_data);
                    $call_data = array(
                        'sign'  => 'change',
                        'oldId' => $old['id'],
                        'orderId' => $newId,
                        'orderNo' => $no,
                        'money'  => $post['all_amounts']
                    );
                $str_attach = implode(',', $call_data);
                D::set('Order.payfields',$newId,$str_attach);    
                //$str_attach = base64_encode(implode(',', $call_data));
                $url = '/WeiXinPay/example/jsapi.php?title=换房&orderNo='.$no.'&amount='.($post['all_amounts'] * 100).'&';
                redirect($url, 0, '页面跳转中...');
                exit;
                }
            }else{
            	//不用花钱
                $bool = $old['checkIn'] == '1' ? true : false;
                $flag = D::add('Order',$add_data);
                if($flag>0){
                    D::set('Order.status',$post['orderId'],'9');
                    if($bool === true){
                        D::save('Order',$flag,[
                            'status' => '0',
                            'checkIn' => 1,
                            'updateTime' => NOW_TIME,
                        ]);
                    }else{
                        D::save('Order',$flag,[
                            'status' => '0',
                            'updateTime' => NOW_TIME,
                        ]);
                    }

                    /*
                        新产生的订单要继承原订单的付款记录
                     */
                    $map['orderId'] = $post['orderId'];
                    $order['orderId'] = $flag;
                    $order['orderNo'] = $add_data['no'];
                    D::save('OrderMoney', $map, $order);

                    //$this->success('换房成功',U('Rooms/index'));
                    redirect('/Rooms/index',0,'换房成功...');
                }
            }

        }else{

        }
    }
    //换房假支付
    public function pay(){
        $map['no'] = I('orderNo');
        if(strpos(I('orderNo'),'C') !== false){
            //将接到的订单编号,打散为数组  0-新订单编号  1-原订单id
            $arr = explode('S',I('orderNo')); 
            //逻辑删除  原订单
            D::set('Order.status',$arr[1],'9');
            //查询原订单--是否入住过
            $checkIn = D::field('Order.checkIn',$arr[1]);
            //更新   换房产生的新订单
            if($checkIn == '1'){
                D::save('Order',$orderId,[
                    'status' => '0',
                    'checkIn' => 1,
                    'updateTime' => NOW_TIME
                ]);
            }else{
                D::save('Order',$orderId,[
                    'status' => '0',
                    'updateTime' => NOW_TIME,
                ]);
            }
            //插入订单金额表记录
	         $data = [
                'orderId' => I('newId'),
	        	'orderNo' => I('order'),
	        	'money'  => I('amount'),
	        	'type'  => 'change',
	        	'add_time' => NOW_TIME
	        ];
            D::add('OrderMoney',$data);
            $this->redirect('Index/showSuccess',[],0,'');
        }else if(I('get.state') == 'no'){
        	$items = D::delete('Order',['where' => "`id`='".I('get.newId')."'"]);
        	//$this->error('换房取消');
        	if($items){
        		redirect(U('Rooms/index'),0,'换房取消...');
        	}
        }
        $this->display();
    }
    //订单成功页面
    public function orderSuccess()
    {
        $orderId = I('order');
        $map['no'] = $orderId;
        D::set('Order.status',I('old'),'9');
        D::save('Order',$map,[
            'status' => '0',
            'updateTime' => NOW_TIME,
        ]);
        $this->success('支付成功',U('Rooms/index'));
        $this->display();
    }
    public function showSuccess()
    {
        //后台设置公众号二维码提取位置
        //后台设置公众号二维码提取位置
        $this->display();
    }
    /*
        用户自己申请退款
    */
    public function back_money(){
        $orderId = I('orderid');
        D::set('Order.status',$orderId,'7');
        $this->success('退款申请成功,请等待管理员审核');
    }
}