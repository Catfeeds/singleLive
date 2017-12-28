<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;
//用户管理模块
class UserManagementController extends CommonController {
	public $model = 'Users';
    public function _map(&$data)
    {
        $map["_string"] = 'status!=9';

        if(I('startTime')){
            $map["_string"] .= ' And createTime >= '.strtotime(I('startTime')).'';
        }
        if(I('endTime')){
            $map["_string"] .= ' And createTime <= '.strtotime(I('endTime')).'';          
        }
        if(I('title')){
            $where["nickname"] = ['like','%'.I('title').'%'];
            $where["mobile"]  = ['like','%'.I('title').'%'];
            $where["_logic"] = 'or';
            $map['_complex'] = $where;
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
        	$data['status'] = $data['status'] == 0 ?  '禁用' : '启用';

        	$items=D::find('files',$data['headimgid']);
            $data['headUrl'] = '/Uploads'.$items['savepath'].$items['savename'];

        	return $data;
        });
        
    }

    //修改用户列表
    public function user_list_edit()
    {
 		$row = D::find('users',I('id'));

		$items = D::find('files',$row['headimgid']);

        $row['headUrl'] = '/Uploads'.$items['savepath'].$items['savename']; 
        $row['createTime'] =  date('Y-m-d',$row['createTime']);


        $this->assign('row',$row);
        $this->display();
    }

    //修改用户信息执行
    public function updateDo()
    {
    	if(I('head')){
    		$_POST['headimgid'] = I('head');
    	}

    	$_POST['createTime'] = strtotime(I('createTime'));

    	if(D::save('users',I('id'),I('post.'))!==false){
    		$this->success('更新成功！',U('UserManagement/index'));
    	}else{
    		$this->error('更新失败，请重试！');
    	}
    }

    //启用或禁用用户
    public function unlock()
    {
    	$Ary['status'] = I('status')=='禁用' ? 1 : 0;

    	if(D::save('users',I('id'),$Ary)){
    		$this->success('更新成功',U('UserManagement/index'));
    	}else{
    		$this->error('更新失败,请重试!');
    	}
    }

    //删除用户
    public function delUser()
    {
    	$Ary['status'] = 9;

    	if(D::save('users',I('id'),$Ary)){
    		$this->success('删除成功！',U('UserManagement/index'));
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
        	$db[$key]['status'] = $data['status'] == 0 ?  '禁用' : '启用';

    	}

    	$dbName = array(
    		array('nickname','微信昵称'),
    		array('realname','真实姓名'),
    		array('mobile','手机号'),
    		array('createTime','注册时间'),
    		array('status','状态')
    		);

    	$excelName = '用户信息列表';

    	export_Excel($excelName,$dbName,$db);
    }

    //查看用户余额
    public function user_balance()
    {
        $rows = D::get(['order','O'],[
                'join' => '__HOTELS__ H on O.hotel=H.id',
                'where' => '(O.status=0 or O.status=2) and O.userId='.I('id').'',
                'group' => 'H.id',
                'field' => 'H.hotelName,O.*'
            ]);

        foreach ($rows as $key => $row) {

            $rows[$key]['Time'] = "";
            $rows[$key]['monery'] = "";

            $orderResult = D::get('order','hotel='.$row['hotel'].' and userId='.I('id').' and (status=0 or status=2)');

            foreach ($orderResult as $Key => $orderRow) {

                 $roomRow = D::find('hotel_rooms','id='.$orderRow['room'].'');

                 $rows[$key]['Time'] += $orderRow['duration'] - $orderRow['used']; 
                 $rows[$key]['monery'] += ($rows[$key]['Time']/24*$roomRow['amount'])+round($rows[$key]['Time']%24*$roomRow['amount']);
            }
        }

        $this->assign('rows',$rows);
    	$this->display();
    }
}






















