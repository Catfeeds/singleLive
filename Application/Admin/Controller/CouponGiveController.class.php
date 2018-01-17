<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;

//电子券转增记录
class CouponGiveController extends CommonController {
    public $model = 'CouponGive';
    public function _map(&$data)
    {
        if(I('startTime') || I('endTime')){
            $map["G.sendTime"] = get_selectTime(I('startTime'),I('endTime'));
        }
        if(I('title')){
            $map['CONTACT(UA.realname,UB.realname)'] = ['like','%'.I('title').'%'];
        }
        $sql = D::get(['CouponGive','G'],[
            'where' => $map,
            'field' => 'G.*,UA.realname sendName,UB.realname acceptName,C.title,C.money',
            'join'  => [
                'LEFT JOIN __USERS__ UA ON UA.id = G.sendID',
                'LEFT JOIN __USERS__ UB ON UB.id = G.acceptID',
                'INNER JOIN __COUPON_EXCHANGE__ E ON E.card = G.cID',
                'LEFT JOIN __COUPON__ C ON C.id = E.cID',
            ],
            'group' => 'G.cID'
        ],false);
        $data = [
            'table' => "{$sql} M",
            'order' => 'M.sendTime desc',
            'field' => 'M.*'
        ];
    }

}
