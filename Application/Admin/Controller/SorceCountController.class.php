<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;

//积分统计
class SorceCountController extends CommonController {
	public $model = 'UserSorce';
    public function _map(&$data)
    {
        if(I('startTime') || I('endTime')){
            $map["createTime"] = get_selectTime(I('startTime'),I('endTime'));
        }
        if(I('title')){
            $map['CONCAT(realname,mobile,idCard)'] = array('like','%'.I('title').'%');
        }

        $data =[
            'where' => $map,
            'order' => 'createTime'
        ];
    }
    public function index()
    {
        $db = parent::index(function($data){
        	$data['createTime'] = date('Y-m-d',$data['createTime']);
        	return $data;
        });
        
    }


    //导出用户列表
    public function export()
    {
    	$db = parent::index(true);
    	foreach ($db as $key => $data) {
    		$db[$key]['createTime'] = date('Y-m-d',$data['createTime']);
        	$db[$key]['status'] = $data['status'] == 1 ?  '启用' : '禁用';
    	}
    	$dbName = array(
    		array('nickname','真实姓名'),
    		array('idCard','身份号'),
    		array('sex','性别'),
    		array('mobile','手机号'),
    		array('Email','电子邮箱'),
    		array('money','余额'),
    		array('sorce','积分'),
    		array('no_md5','登陆密码'),
    		array('createTime','注册时间'),
    		array('status','状态')
        );
    	$excelName = '用户信息列表';
    	export_Excel($excelName,$dbName,$db);
    }


}






















