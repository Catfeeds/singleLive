<?php
namespace Admin\Controller;

use Think\Controller;
use Think\Faster;
use Think\D;

/*
   用户订单 
*/
class UserorderController extends CommonController
{
    public $model = ['Order','OH'];
    public function _map(&$data)
    {
        if ( I('title') ) {
            $map['CONCAT(OH.no,U.realname,U.mobile)'] = ['like','%'.I('title').'%'];
        }
        if ( I('start') || I('end') ) {
            $map['OH.createTime'] = get_selectTime( I('start'),I('end') );
        }
        if(I('status') == 0 && I('status') != ''){
            $map['_string'] = 'OH.status=0 AND OH.used=0';
        }elseif(I('status') == 10){
            $map['_string'] = 'OH.status=0 AND OH.used>0 AND OH.used<OH.duration';
        }elseif(I('status')){
            $map['OH.status'] = I('status');
        }else{
            $map['OH.status'] = array('in','1,0,3,7');
        }
        $data = [
            'where' => $map,
            'field' => 'OH.*,U.realname,U.mobile,A.hotelName,D.roomName,B.price',
            'join'  => [
                'LEFT JOIN __USERS__ U ON U.id = OH.userId',
                'LEFT JOIN __HOTELS__ A ON A.id = OH.hotel',
                'LEFT JOIN __HOTEL_ROOMS__ B ON B.id = OH.room',
                'LEFT JOIN __ROOMS__ D ON D.id = B.room',
            ],
            'order' => 'OH.createTime DESC',
        ];
    }
    //用户订单列表
    public function index()
    {   
        $info = http_build_query(I('get.'));
        $this->assign('info',$info);
        parent::index(function($data){
            if($data['status'] == 0 && $data['used']<$data['duration'] && $data['used']>0){
                $data['push'] = '使用中';
            }elseif($data['status'] == 1){
                $data['push'] = '已全部使用';
            }elseif($data['status'] == 0){
                $data['push'] = '已付款';
            }elseif($data['status'] == 3){
                $data['push'] = '已退款';
            }elseif($data['status'] == 7){
                $data['push'] = '退款申请';
            }
            $data['money'] = round($data['duration']*$data['amount'],0);
            return $data;
        },true);
    }
    /*
        退款
        能退款的情况---order表里：used为0 status为0
        因为可能存在买24 住了20小时的情况
    */
    public function backMoney(){
        $draw = M('Drawback');
        $data['orderId'] = I('id');
        $data['money'] = I('old_price');
        $data['createTime'] = time();
        $draw->add($data);

        $row = ['status' => 3, 'updateTime' => time()];
        M('Order')->where("id=".I('id'))->save($row);
        $inTime = D::field('Order.duration', $id);
        $info = search_order_msg(I('id'));
        send_refund_msg($info['id'], $info['hotelName'], $info['roomName'], $inTime);   
        $this->success('操作成功');
    }
    //查看用户订单
    public function see()
    {
        $id = I('id');
        $db = D::find(['Order','OH'],[
            'where'=> ['OH.id' => $id],
            'join' => [
                'LEFT JOIN __USERS__ U ON U.id = OH.userId',
                'LEFT JOIN __HOTELS__ A ON A.id = OH.hotel',
                'LEFT JOIN __HOTEL_ROOMS__ B ON B.id = OH.room',
                'LEFT JOIN __ROOMS__ D ON D.id = B.room'
            ],
            'field'=> 'OH.*,U.realname,U.mobile,A.hotelName,D.roomName,B.price'
        ]);
        $db['startTime'] = D::field('OrderHotel.startTime',[
                    'orderId' => $db['id'],
                    'status'  =>1
                ]);
        // if($db['status']==0 && $db['used']==0){
            $db['umoney'] = round($db['duration']*$db['amount'],0);
        // }else{
        //     $db['umoney'] = round($db['amount']*$db['used'],0);
        // }
        if($db['status'] == 0 && $db['used']>0 && $db['used']<$db['duration']){
            $db['push'] = '使用中';
        }elseif($db['status'] == 1){
            $db['push'] = '已全部使用';
        }elseif($db['status'] == 0){
            $db['push'] = '已付款';
        }elseif($db['status'] == 3){
            $db['push'] = '已退款';
        }elseif($db['status'] == 7){
            $db['push'] = '退款申请';
        }
        $this->assign('db',$db);
        $this->display();
    }
    //导出
    public function export(){
        $db = array_map(function($data){
            if($data['status'] == 0 && $data['used']>0 && $data['used']<$data['duration']){
                $data['push'] = '使用中';
            }elseif($data['status'] == 1){
                $data['push'] = '已全部使用';
            }elseif($data['status'] == 0){
                $data['push'] = '已付款';
            }elseif($data['status'] == 3){
                $data['push'] = '已退款';
            }elseif($data['status'] == 7){
                $data['push'] = '退款申请';
            }
            $data['money'] = round($data['duration']*$data['amount'],0);
            return $data;
        }, parent::index(true));
        foreach ($db as $key => $value) {
            $db[$key]['createTime'] = date_out($db[$key]['createTime']);
        }
        $xlsName  = date('Y-m-d_H:i:s',time()).'用户订单列表';
        $xlsCell  = array(
            array('createTime','日期'),
            array('no','订单编号'),
            array('hotelName','酒店名称'),
            array('roomName','房间类型'),
            array('realname','客户姓名'),
            array('mobile','客户电话'),
            array('money','订单金额'),
            array('push','状态'),
        );
        export_Excel($xlsName,$xlsCell,$db);
    }
}
