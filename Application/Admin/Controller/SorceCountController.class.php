<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;

//积分统计
class SorceCountController extends CommonController {
	public $model = ['UserSorce','S'];
    public function _map(&$data)
    {
        switch(ACTION_NAME){
            case 'index':
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
                break;
            case 'export':
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
                break;
        }
    }
    public function index()
    {
		parent::index(function($data){
			$data['before'] = D::field("UserSorce.IFNULL(sum(CASE WHEN method='plus' THEN sorce ELSE -sorce END),0)",['userID' => $data['userID'],'id' => ['lt',$data['id']]]);
			$data['createTime'] = date('Y-m-d',$data['createTime']);
            if($data['method'] == 'plus'){
                $data['after'] = $data['before'] + $data['sorce'];
            }else{
                $data['after'] = $data['before'] - $data['sorce'];
            }
			$data['type'] = getTypes($data['type']);
			return $data;
		});
    }
    public function export()
    {
        $db = array_map(function($data){
            $data['before'] = D::field("UserSorce.IFNULL(sum(CASE WHEN method='plus' THEN sorce ELSE -sorce END),0)",['userID' => $data['userID'],'id' => ['lt',$data['id']]]);
            $data['createTime'] = date('Y-m-d',$data['createTime']);
            if($data['method'] == 'plus'){
                $data['sign']  = '↑↑';
                $data['after'] = $data['before'] + $data['sorce'];
            }else{
                $data['sign']  = '↓↓';
                $data['after'] = $data['before'] - $data['sorce'];
            }
            $data['type'] = getTypes($data['type']);
            return $data;
        },parent::index(true));
        $dbName = array(
            array('createTime','日期'),
            array('realname','姓名'),
            array('before','变更前'),
            array('sorce','变更'),
            array('after','变更后'),
            array('sign','状态'),
            array('type','类型')
        );
        $excelName = '积分统计列表';
        export_Excel($excelName,$dbName,$db);
    }

}






















