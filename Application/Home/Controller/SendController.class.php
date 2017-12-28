<?php
namespace Home\Controller;
use Think\Controller;
use Think\D;
//系统管理
class SendController extends Controller {
    /*
	 * 	@Author   zhenHong
     *  查询当前用户所有订单，
     *  离购买时间差：
     *      1小时     30分钟    5分钟  都要发送消息给用户
     * */
    public function send_user(){
        $map = array(
            'OH.status'=> 2,
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
            if($data['time'] >= 3300 && $data['time']<=3900){
                // $content = '尊敬的用户'.$data['realname'].'您所入住的'.$data['hotelName'].'房间类型为'.$data['roomName'].'订单号为'.$data['no'].'离入住到期时间还有1小时请您及时收拾好东西,联系酒店前台方便您办理退房';
                send_when_msg($data['id'], $data['hotelName'], $data['roomName'], $data['no']);
            }
        },$list);
        echo '正确';
    }
}
