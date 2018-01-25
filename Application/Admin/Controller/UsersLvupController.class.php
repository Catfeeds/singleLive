<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;

//会员升级记录
class UsersLvupController extends CommonController {
	public $model = ['UserLvup','P'];
    public function _map(&$data)
    {
        switch(ACTION_NAME){
            case 'index':
                if(I('startTime') || I('endTime')){
                    $map["P.createTime"] = get_selectTime(I('startTime'),I('endTime'));
                }
                if(I('title')){
                    $map['CONCAT(realname,mobile)'] = array('like','%'.I('title').'%');
                }

                $data =[
                    'field' => 'P.*,U.realname,U.mobile,GA.title beforeName,GB.title afterName',
                    'where' => $map,
                    'join'  => [
                        'LEFT JOIN __USERS__ U ON U.id = P.userID',
                        'LEFT JOIN __GRADES__ GA ON GA.id = P.`before`',
                        'LEFT JOIN __GRADES__ GB ON GB.id = P.`after`'
                    ],
                    'order' => 'P.createTime desc'
                ];
                break;
            case 'export':
                if(I('startTime') || I('endTime')){
                    $map["P.createTime"] = get_selectTime(I('startTime'),I('endTime'));
                }
                if(I('title')){
                    $map['CONCAT(realname,mobile)'] = array('like','%'.I('title').'%');
                }

                $data =[
                    'field' => 'P.*,U.realname,U.mobile,GA.title beforeName,GB.title afterName',
                    'where' => $map,
                    'join'  => [
                        'LEFT JOIN __USERS__ U ON U.id = P.userID',
                        'LEFT JOIN __GRADES__ GA ON GA.id = P.`before`',
                        'LEFT JOIN __GRADES__ GB ON GB.id = P.`after`'
                    ],
                    'order' => 'P.createTime desc'
                ];
                break;
        }

    }
    public function index()
    {
        parent::index(function($data){
        	$data['createTime'] = date('Y-m-d',$data['createTime']);
            if($data['beforeName'] == ''){
                $data['beforeName'] = '顾客';
            }
            if($data['afterName'] == ''){
                $data['afterName'] = '顾客';
            }
        	return $data;
        });
        
    }

    //导出用户列表
    public function export()
    {
    	$db = array_map(function($data){
            $data['createTime'] = date('Y-m-d',$data['createTime']);
            if($data['beforeName'] == ''){
                $data['beforeName'] = '顾客';
            }
            if($data['afterName'] == ''){
                $data['afterName'] = '顾客';
            }
            $data['regLevel'] = '顾客';
            return $data;
        },parent::index(true));
    	$dbName = array(
    		array('createTime','日期'),
    		array('realname','姓名'),
    		array('mobile','电话'),
    		array('regLevel','初始级别'),
    		array('beforeName','升级前级别'),
    		array('afterName','升级后级别'),
        );
    	$excelName = '会员晋级列表';
    	export_Excel($excelName,$dbName,$db);
    }

}






















