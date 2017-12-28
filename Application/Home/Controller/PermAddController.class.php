<?php
namespace Home\Controller;
use Think\Controller;
use Think\D;
//增加子账号
class PermAddController extends CommonController {
	public $model = ['hotel_admins','H'];
	public function _map(&$data)
	{
		if(I('title')){
			$map['nickname'] = ['like','%'.I('title').'%'];
		}
		$SQL = D::get('root_login',[
				'order' => 'login_time DESC',
				'where' => 'status=1'
			],false);
		$SQL = D::get('',[
				'table' => $SQL.'H',
				'group' => 'root_id',
				'field' => 'login_time,count(*) count,root_id,login_ip'
			],false);

		$map['hotel']   = session('hotel_user.hotel');
		$map['_string'] = 'status!=9';

		$data = [
			'where' => $map,
			'join'  => 'left join '.$SQL.' L on L.root_id = H.id',
			'field' => "H.*,IFNULL(L.login_time,'0') `time`,IFNULL(L.count,0) count,IFNULL(L.login_ip,'-') `IP`"
		];
	}
	public function index()
	{
		$db = parent::index(function($data){
			$data['time'] = $data['time'] ? date('Y-m-d H:i:s',$data['time']) : "未登录";
			return $data;
		});
	}
	//启用或禁用管理员
    public function rootSta()
    {

        if(I('get.sta')==1) {

            $Ary["status"]=0;

            if(!D::count(['hotel_admins','RT'],'`RT`.`id`='.I('get.id').' and `group`!=0')) {
                $this->error("管理员尚未分配管理组，请先分配管理组！");
            }
            $row=D::find(['hotel_admins','RT'],[
                'join'  => "role on RT.group=role.role_id",
                'where' => "RT.id=".I('get.id')." and role.hotel=".session('hotel_user.hotel').""
                ]);
            if($row["role_sta"]==1)
            {
                $this->error("该用户的所属的管理组已被禁用，请先启用管理组或为该用户重新分配管理组！");
            }
            else
            {
                if(D::save('HotelAdmins',I('get.id'),$Ary)){

                    $this->success("启用成功！");
                }else{

                     $this->error("启用失败，请刷新后重试");
                }

           }
        }
        else if(I('get.sta')==0)
        {
            $Ary["status"]=1;

            if(D::save('HotelAdmins',I('id'),$Ary))
            {
                $this->success("禁用成功！");
            }
            else
            {
                 $this->error("禁用失败，请刷新后重试");
            }
        }  

    }	
	//新增管理员
	public function rootAdd()
	{
		$this->display();
	}
	//新增管理员执行
	public function rootAddDo()
	{

        if(I('post.username')==""||I('post.nickname')==""||I('post.realname')==""||I('post.mobile')=="")
        {
            $this->error("所有项均不能为空！");
        }

        else if(I('post.pwd1')!=I('post.pwd2'))
        {
            $this->error("两次输入的密码不一致！");
        }
        else
        {

            if(D::count("hotel_admins",[
                	'where' => "username='".I('post.username')."'"
                ])){
                    $this->error("已存在此账户名！");
            }else{

                unset($_POST["root_pwd2"]);

                $_POST["password"]=md5($_POST["password"]);
                $_POST["status"]=1;
                $_POST["hotel"]=session('hotel_user.hotel');
                $_POST["root"]=session('hotel_user.id');
                if(D::add('hotel_admins')>0)
                {   
                    $this->success("添加成功!",U("PermAdd/index"));
                }
                else
                {
                    $this->error("添加失败，请刷新页面后重试！");
                }
            }
        }

	}
    //管理员授权
    public function rootPerm()
    {

        $roleRows=D::get('role',[
            'where' => "hotel=".session('hotel_user.hotel')." and role_sta=1"
        ]);

        $this->assign("roleRows",$roleRows);
        $this->assign("id",I('get.id'));
        $this->display();       
    }
    //管理员授权执行
    public function roleGetRoot()
    {
        if(D::save('hotel_admins','id='.I('post.id').'')!==false){
            $this->success("授权成功！",U("PermAdd/index"));
        }else{
            $this->error("授权失败,请刷新页面后重试！");
        }
    }

	//管理员信息修改
	public function rootUpdate()
	{

        $row=D::find(["hotel_admins"],[
            	'where' => "id=".I('get.id').""
            ]);

        $this->assign("row",$row);
        $this->display();
	}
	//管理员信息修改执行
	public function rootUpdeDo()
	{

        $temp=1;
        $i=0;
        if(I('post.username')==""||I('post.nickname')==""||I('post.realname')==""||I('post.mobile')=="")
        {
            $temp=0;
            $this->error("所有项均不能为空！");
        }

        if(!I('post.pwd')&&!I('post.pwd2'))
        {
            $i=1;
        }
        else if(I('post.pwd1')!=I('post.pwd2'))
        {
            $temp=0;
            $this->error("两次输入的密码不一致！");
        }

        if($temp==1)
        {
            if($i==0){

                $_POST["password"]=md5($_POST["pwd1"]);

            }

            unset($_POST["pwd1"]);  
            unset($_POST["pwd2"]);

            $result=D::save("hotel_admins","id=".I('id')."",$_POST);

            if($result===0)
            {   
                $this->success("更新成功,数据无变化!",U("PermAdd/index"));
            }
            else if($result>0)
            {
                $this->success("更新成功!",U("PermAdd/index"));
            }
            else
            {
                $this->error("更新失败，请刷新页面后重试！");
            }
        }
	}
    //删除管理员
    public function rootDel()
    {
    	$Ary['status'] = 9;
        
        $row=D::save('hotel_admins','id='.I('get.id').'',$Ary);

        if($row>0){
            $this->success("删除成功!",U("PermAdd/index"));
        }else{
            $this->error("删除失败，请刷新页面后重试！");
        }
    }

}