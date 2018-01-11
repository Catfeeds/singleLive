<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;

//电子券转增记录
class CouponGiveController extends CommonController {
    public $model = ['CouponGive','G'];
    public function _map(&$data)
    {
        if(I('startTime') || I('endTime')){
            $map["G.sendTime"] = get_selectTime(I('startTime'),I('endTime'));
        }
        if(I('title')){
            $map['CONTACT(UA.realname,UB.realname)'] = ['like','%'.I('title').'%'];
        }
        $data = [
            'where' => $map,
            'field' => 'G.*,UA.realname sendName,UB.realname acceptName,C.title,C.money',
            'join'  => [
                'LEFT JOIN __USERS__ UA ON U.id = G.sendID',
                'LEFT JOIN __USERS__ UB ON U.id = G.acceptID',
                'LEFT JOIN __COUPON__ C ON C.id = G.cID',
            ],
            'order' => 'sendTime desc'
        ];
    }
}
