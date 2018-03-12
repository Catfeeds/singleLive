<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;

//用户列表
class MemberListController extends CommonController {
	public $model = ['Users','U'];
    public function _map(&$data)
    {
        switch (ACTION_NAME) {
            case 'index':
                if(I('startTime') || I('endTime')){
                    $map["U.createTime"] = get_selectTime(I('startTime'),I('endTime'));
                }
                if(I('title')){
                    $map['CONCAT(U.realname,U.mobile,U.idCard)'] = array('like','%'.I('title').'%');
                }
                $map["U.status"] = array('neq',3);
                $balance_sql = D::get('Balance',[
                    'where' => "`status` = 1",
                    'field' => "userID,SUM(CASE WHEN method='plus' THEN money ELSE 0 END) BalanceUp,SUM(CASE WHEN method='sub' THEN money ELSE 0 END) BalanceDown",
                    'group' => 'userID'
                ],false);
                $sorce_sql = D::get('UserSorce',[
                    'field' => "userID,SUM(CASE WHEN method='plus' THEN sorce ELSE 0 END) SorceUp,SUM(CASE WHEN method='sub' THEN sorce ELSE 0 END) SorceDown",
                    'group' => 'userID'
                ],false);
                $data =[
                    'where' => $map,
                    'join'  => [
                        "LEFT JOIN $balance_sql B ON B.userID = U.id",
                        "LEFT JOIN $sorce_sql S ON S.userID = U.id",
                    ],
                    'field' => 'U.*,(B.BalanceUp-B.BalanceDown) allBalance,(S.SorceUp-S.SorceDown) allSorce',
                    'order' => 'createTime'
                ];
                break;
            case 'export':
                if(I('startTime') || I('endTime')){
                    $map["U.createTime"] = get_selectTime(I('startTime'),I('endTime'));
                }
                if(I('title')){
                    $map['CONCAT(U.realname,U.mobile,U.idCard)'] = array('like','%'.I('title').'%');
                }
                $map["U.status"] = array('neq',3);
                $balance_sql = D::get('Balance',[
                    'where' => "`status` = 1",
                    'field' => "userID,IFNULL(SUM(CASE WHEN method='plus' THEN money ELSE 0 END),0) BalanceUp,IFNULL(SUM(CASE WHEN method='sub' THEN money ELSE 0 END),0) BalanceDown",
                    'group' => 'userID'
                ],false);
                $sorce_sql = D::get('UserSorce',[
                    'field' => "userID,IFNULL(SUM(CASE WHEN method='plus' THEN sorce ELSE 0 END),0) SorceUp,IFNULL(SUM(CASE WHEN method='sub' THEN sorce ELSE 0 END),0) SorceDown",
                    'group' => 'userID'
                ],false);
                $data =[
                    'where' => $map,
                    'join'  => [
                        "LEFT JOIN $balance_sql B ON B.userID = U.id",
                        "LEFT JOIN $sorce_sql S ON S.userID = U.id",
                    ],
                    'field' => 'U.*,IFNULL((B.BalanceUp-B.BalanceDown),0) allBalance,IFNULL((S.SorceUp-S.SorceDown),0) allSorce',
                    'order' => 'createTime'
                ];
                break;
        }
    }
    public function index()
    {
         parent::index(function($data){
        	$data['createTime'] = date('Y-m-d',$data['createTime']);
            $data['regLevel'] =  '顾客';
            if($data['nowLevel'] == '0'){
                $data['nowLevel'] = '顾客';
            }else{
                $data['nowLevel'] = D::field('Grades.title',$data['nowLevel']);
            }
             if(!$data['allBalance']){
                 $data['allBalance'] = 0;
             }
             if(!$data['allSorce']){
                 $data['allSorce'] = 0;
             }
        	return $data;
        });
        
    }

    //修改用户列表
    public function edit()
    {
        header('Content-type:text/html;charset=UTF-8');
        $admin = session('root_user');
 		$row = D::find('users',I('id'));
        if(IS_POST){
            $sorce = D('UserSorce');
            if($data = $sorce->create()){
                $data['admin'] = $admin['id'];
                $data['type'] = 'admin';
                $sorce->add($data);
                //若是管理员修改  会员积分则记录修改人id
                event_user_level($data['userID'],$admin['id']);
                $this->success('修改成功',U('MemberList/index'));
            }else{
                $this->error($sorce->getError());
            }
        }
        $this->assign('row',$row);
        $this->display();
    }
    //修改会员资料
    public function editMsg(){
        $id = I('id');
        $db = D::find('Users',$id);
        if(IS_POST){
            $user = D('Users');
            if($user->create()){
                $user->save();
                $this->success('修改成功',U('MemberList/index'));
            }else{
                $this->error($user->getError());
            }
        }
        $this->assign('db',$db);
        $this->display();
    }
    //重置密码
    public function setPassword(){
        $id = I('id');
        $data = [
            'password' => md5('123456'),
            'no_md5' => '123456'
        ];
        M('Users')->where("id=".$id)->setField($data);
        $this->success('重置密码成功',U('MemberList/index'));
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
        $db = array_map(function($data){
            $data['createTime'] = date('Y-m-d',$data['createTime']);
            $data['regLevel'] =  '顾客';
            if($data['nowLevel'] == '0'){
                $data['nowLevel'] = '顾客';
            }else{
                $data['nowLevel'] = D::field('Grades.title',$data['nowLevel']);
            }
            $data['status'] = $data['status'] == '1' ? '正常' : '禁用';
            return $data;
        },parent::index(true));
    	$dbName = array(
            array('codes','会员编号'),
    		array('realname','真实姓名'),
    		array('idCard','身份号'),
    		array('sex','性别'),
    		array('mobile','手机号'),
    		array('Email','电子邮箱'),
    		array('allBalance','余额'),
    		array('allSorce','积分'),
    		array('no_md5','登陆密码'),
    		array('createTime','注册时间'),
    		array('status','状态')
        );
    	$excelName = date('Y-m-d_H:i:s').'用户信息列表';
    	export_Excel($excelName,$dbName,$db);
    }


}






















