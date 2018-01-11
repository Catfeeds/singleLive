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
            $map["G.sendTime"] = get_selectTime(I('startTime'),I('endTime'));
        }
        if(I('title')){
            $map['CONTACT(UA.realname,UB.realname)'] = ['like','%'.I('title').'%'];
        }
        $data = [
            'where' => $map,
            'field' => 'C.*,U.realname,C.title,C.money',
            'join'  => [
                'LEFT JOIN __USERS__ U ON U.id = C.userID',
                'LEFT JOIN __COUPON__ P ON P.id = C.cID'
            ],
            'order' => 'createTime desc'
        ];
    }
    public function index(){
        $db = parent::index(false);
        $db = array_map(function($data){
            switch ($data['type']){
                case 'k':
                    $data['showName'] = D::field('Package.title',$data['roomID']);
                    break;
                case 't':
                    $data['showName'] = D::field('House.name',$data['roomID']);
                    break;
            }
            return $data;
        },$db['db']);
        $this->assign('db',$db['db']);
        $this->assign('page',$db['page']);
        $this->display();
    }
}
