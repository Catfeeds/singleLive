<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;

//电子券转增记录
class CouponUsedController extends CommonController {
    public $model = ['CouponUsed','C'];
    public function _map(&$data)
    {
        if(I('startTime') || I('endTime')){
            $map["C.createTime"] = get_selectTime(I('startTime'),I('endTime'));
        }
        if(I('title')){
            $map['CONTACT(U.realname,P.title)'] = ['like','%'.I('title').'%'];
        }
        $data = [
            'where' => $map,
            'field' => 'C.*,U.realname,P.title,P.money',
            'join'  => [
                'LEFT JOIN __USERS__ U ON U.id = C.userID',
                'LEFT JOIN __COUPON_EXCHANGE__ E ON E.card = C.cID',
                'LEFT JOIN __COUPON__ P ON P.id = E.cID'
            ],
            'order' => 'C.createTime desc'
        ];
    }
    public function index(){
        $db = parent::index(false);
        $db['db'] = array_map(function($data){
            switch ($data['type']){
                case 'k':
                    $data['showName'] = D::field('Package.title',$data['roomID']);
                    break;
                case 't':
                    $data['showName'] = D::field('House.name',$data['roomID']);
                    break;
            }
            $data['createTime'] = date('Y-m-d',$data['createTime']);
            return $data;
        },$db['db']);
        $this->assign('db',$db['db']);
        $this->assign('page',$db['page']);
        $this->display();
    }
}
