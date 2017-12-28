<?php
namespace Admin\Controller;

use Think\Controller;
use Think\Faster;
use Think\D;

//订单管理模块
class OrderController extends CommonController
{
    // public $model = ['OrderHotel','OH'];
    public function _map(&$data)
    {
        if ( I('title') ) {
            $map['CONCAT(OH.no,U.realname,U.mobile)'] = ['like','%'.I('title').'%'];
        }
        if ( I('start') || I('end') ) {
            $map['OH.createTime'] = get_selectTime( I('start'),I('end') );
        }
        switch (I('time_sta')){
            case '1': $map['_string'] = "M.use_time = M.buy_time OR M.use_time < M.buy_time";break;
            case '2': $map['_string'] = "M.use_time > M.buy_time";break;
        }
        $sql = D::get(['OrderHotel','OH'],[
            'field' => 'OH.*,U.realname,U.nickname,U.mobile,A.used use_time,A.duration buy_time',
            'join'  => [
                'LEFT JOIN __USERS__ U ON U.id = OH.userId',
                'LEFT JOIN __ORDER__ A ON A.id = OH.orderId'
            ],
            'order' => 'OH.createTime DESC',
        ],false);
        $data = [
            'table' => '('.$sql.') M',
            'where' => $map
        ];
    }
    public function index()
    {
        $info = http_build_query(I('get.'));
        $this->assign('info',$info);
        parent::index('checkData');
    }
    public function export(){
        $db = array_map([$this,'checkData'], parent::index(true));
        foreach ($db as $key => $value) {
            $db[$key]['createTime'] = date_out($db[$key]['createTime']);
            switch ($db[$key]['status']) {
                case '0':
                    $db[$key]['status'] = '预约中';
                    break;
                case '1':
                    $db[$key]['status'] = '订单已完成';
                    break;
                case '2':
                    $db[$key]['status'] = '入住中';
                    break;
                case '8':
                    $db[$key]['status'] = '订单已取消';
                    break;
            }
        }

        $xlsName  = date('Y-m-d_H:i:s',time()).'酒店订单列表';
        $xlsCell  = array(
            array('createTime','日期'),
            array('no','订单编号'),
            array('hotelName','酒店名称'),
            array('roomType','房间类型'),
            array('realname','客户姓名'),
            array('mobile','客户电话'),
            array('createTime','入住时间'),
            array('old','已入住时间'),
            array('use','折算小时数'),
            array('status','状态'),
        );
        export_Excel($xlsName,$xlsCell,$db);
    }

    //查看订单列表
    public function order_list_edit()
    {
        $id = I('id');
        $data = D::find(['OrderHotel','OH'],$id);
        $db = $this->checkData($data);
        $this->assign('db',$db);
        $this->display();
    }
    /*
        封装一个数据集函数
    */
    public function checkData( $data ){
        $order = D::find(['Order','O'],[
            'where' => ['O.id' => $data['orderId']],
            'join'  => [
                'LEFT JOIN __HOTEL_ROOMS__ R ON R.id = O.room',
                'LEFT JOIN __HOTELS__ C ON C.id = R.hotel'
            ],
            'field' => 'R.*,(O.duration - O.used) have,C.hotelName',
        ]);
        $data['roomType'] = D::field('Rooms.roomName',$order['room']);
        $data['hotelName'] = $order['hotelName'];
        $data['uname'] = D::field('Users.realname',$data['userId']);
        $data['utel'] = D::field('Users.mobile',$data['userId']);
        $data['have'] = $order['have'];
        $data['min'] = $order['minimum'];
        $data['minute'] = $order['minute'];
        $data['now'] = NOW_TIME - $data['startTime'];
        $h = floor(($data['endTime'] - $data['startTime']) / 3600);
        $m = floor((($data['endTime'] - $data['startTime'])% 3600) / 60);
        $s = floor((($data['endTime'] - $data['startTime'])% 3600) % 60);
        $data['old'] = ($data['status'] == 1)?str_pad($h,2, "0", STR_PAD_LEFT).':'.str_pad($m,2, "0", STR_PAD_LEFT).':'.str_pad($s,2, "0", STR_PAD_LEFT):'00:00:00';
        if ($data['status'] == 1) {
            if ($h < $order['minimum']) {
                $data['use'] = $order['minimum'];
            }else{
                $data['use'] = ($order['minute'] <= $m )?$h + 1:$h;
            }
        }else{
            $data['use'] = 0;
        }
        //订单金额
        if($data['use']==0){
            $data['umoney'] = 0;
        }else{
            $data['umoney'] = $data['use']*$order['amount'];
        }
        return $data;
    }
}
