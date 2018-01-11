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
         parent::index(function($data){
        	$data['createTime'] = date('Y-m-d',$data['createTime']);
            if($data['nowLevel'] == '0'){
                $data['nowLevel'] = '无级别';
            }else{
                $data['nowLevel'] = D::field('Grades.title',$data['nowLevel']);
            }
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
    //重置密码
    public function setPassword(){
        $post = I('post.');
        if(empty($post['password'])){
            $this->success('密码不能为空');
        }
        if(empty($post['pwd'])){
            $this->success('确认密码不能为空');
        }
        if($post['pwd']!=$post['password']){
            $this->success('确认密码不一致');
        }
        
    }

    //删除用户
    public function delUser()
    {
    	$Ary['status'] = 3;
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






















