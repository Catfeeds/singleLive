<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\D;
class OrderController extends MobileCommonController {
    public static $open = false;//控制器不开放,需登录
    public $model = ['OrderHotel','O'];
    public function _map(&$data)
    {
    	$data = [
    	'where' => ['O.userId' => session('user.id'),
    	    	'O.status' => ['neq',9],],
    	'order' => 'O.createTime DESC',
    	'join' => [
	    	'LEFT JOIN __ORDER__ H ON H.id = O.orderId',
	    	],
	    'field' => 'O.*,H.room Hroom,(H.duration - H.used) have',
    	];
    }
    /**
     * [index 订单页]
     * @Author   尹新斌
     * @DateTime 2017-07-18
     * @Function []
     * @return   [type]     [description]
     */
    public function index()
    {
    	if (IS_AJAX) {
    		parent::index(function($data){
                $order = D::find('Order',$data['orderId']);
                $data['hotelName'] = D::field('Hotels.hotelName',$order['hotel']);
                $roomId = D::find('HotelRooms',$order['room']);
                $data['roomName'] = D::field('Rooms.roomName',$roomId['room']);
                $data['have'] = formatTime($data['have']);
                if ($data['status'] == '8') {
                    $data['msg'] = D::field("OrderBack.reason",['orderId' => $data['id']])?:'未填写原因';
                }
                $data['time'] = NOW_TIME - $data['startTime'];
                $data['min'] = formatTime($roomId['minimum']);
                if ($data['status'] == '1') {
                    $data['time'] = getTimeFormat($data['endTime'] - $data['startTime']);
                }
    			return $data;
    		});
    	}else{
    		$this->display();
    	}
    }
    /**
     * [backOrder 取消订单]
     * @Author   尹新斌
     * @DateTime 2017-07-19
     * @Function []
     * @return   [type]     [description]
     */
    public function backOrder()
    {
        $id = I('id');
        $now = D::find('OrderHotel',$id);
        if ($now['status'] != '0') {
            $this->error('当前订单无法取消，请刷新页面重试');exit;
        }
        $num = D::save('OrderHotel',$id,[
            'status' => 8,
            ]);
        if ($num) {
            $flag = D::save('Order',$now['orderId'],[
                'status' => '0',
                'updateTime' => NOW_TIME,
                ]);
            $this->success('订单已取消');
        }else{
            $this->error('当前订单无法取消，请刷新页面重试');exit;
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
            'sign' => 'continue_ing',
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
        if(I('get.state') == 'ok'){

            //原订单的够买时间
            $duration = D::field('Order.duration',I('id'));
            //总时间
            $all = intval($duration) + (I('daysnum')*24);
            $datas = array(
                'duration' => $all,
                'updateTime' => time(),
            );

            //新增需求，续住的时候要将上一个订单的累计金额相加
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
            $this->success('支付成功',U('Order/index'));
        }
        $this->assign('info',$info);
        $this->display();*/
    }
}