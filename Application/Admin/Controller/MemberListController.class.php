<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;

//用户列表
class MemberListController extends CommonController {
	public $model = 'Users';
    public function _map(&$data)
    {
        if(I('startTime') || I('endTime')){
            $map["createTime"] = get_selectTime(I('startTime'),I('endTime'));
        }
        if(I('title')){
            $map['CONCAT(realname,mobile,idCard)'] = array('like','%'.I('title').'%');
        }
        $map["status"] = array('neq',9);

        $data =[
            'where' => $map,
            'order' => 'createTime'
        ];

    }
    public function index()
    {
        $db = parent::index(function($data){
        	$data['createTime'] = date('Y-m-d',$data['createTime']);
        	$data['status'] = $data['status'] == 0 ?  '禁用' : '启用';
        	return $data;
        });
        
    }

    //修改用户列表
    public function edit()
    {
 		$row = D::find('users',I('id'));
        $this->assign('row',$row);
        $this->display();
    }

    //启用或禁用用户
    public function set_status(){
        switch(I('set')){
            case '1':
                $set = 2;
                $option = '禁用成功';
                break;
            case '2':
                $set = 1;
                $option = '启用成功';
                break;
        }
        D::set('Users.status',I('id'),$set);
        $this->success($option);
    }

    //删除用户
    public function delUser()
    {
    	$Ary['status'] = 9;
    	if(D::save('users',I('id'),$Ary)){
    		$this->success('删除成功！',U('MemberList/index'));
    	}else{
    		$this->error('删除失败，请重试！');
    	}
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






















