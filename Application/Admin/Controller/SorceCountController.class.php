<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;

//积分统计
class SorceCountController extends CommonController {
	public $model = ['UserSorce','S'];
    public function _map(&$data)
    {
        if(I('startTime') || I('endTime')){
            $map["S.createTime"] = get_selectTime(I('startTime'),I('endTime'));
        }
        if(I('title')){
            $map['U.realname'] = array('like','%'.I('title').'%');
        }

        $data =[
            'where' => $map,
			'field'	=> 'S.*,U.realname',
			'join'	=> "LEFT JOIN __USERS__ U ON U.id = S.userID",
			'order' => 'id desc',
        ];
    }
    public function index()
    {
		parent::index(function($data){
			$data['before'] = D::field('UserSorce.IFNULL(sum(sorce),0)',['userID' => $data['userID'],'id' => ['lt',$data['id']]]);
			$data['createTime'] = date('Y-m-d H:i:s',$data['createTime']);
			$data['after'] = $data['before'] + $data['sorce'];
			$data['type'] = getTypes($data['type']);
			return $data;
		});
    }

}






















