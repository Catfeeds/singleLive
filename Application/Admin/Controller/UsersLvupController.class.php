<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;

//会员升级记录
class UsersLvupController extends CommonController {
	public $model = ['UserLvup','P'];
    public function _map(&$data)
    {
        if(I('startTime') || I('endTime')){
            $map["createTime"] = get_selectTime(I('startTime'),I('endTime'));
        }
        if(I('title')){
            $map['CONCAT(realname,mobile)'] = array('like','%'.I('title').'%');
        }

        $data =[
            'field' => 'P.*,U.realname,U.mobile,GA.title beforeName,GB.title afterName',
            'where' => $map,
            'join'  => [
                'LEFT JOIN __USERS__ U ON U.id = P.userID',
                'LEFT JOIN __GRADES__ GA ON GA.id = P.before',
                'LEFT JOIN __GRADES__ GB ON GB.id = P.after'
            ],
            'order' => 'createTime desc'
        ];
    }
    public function index()
    {
        parent::index(function($data){
        	$data['createTime'] = date('Y-m-d',$data['createTime']);
            if($data['beforeName'] == ''){
                $data['beforeName'] = '无级别';
            }
        	return $data;
        });
        
    }

    //导出用户列表
    /*public function export()
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
    */
}






















