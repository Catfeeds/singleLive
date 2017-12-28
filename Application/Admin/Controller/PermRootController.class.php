<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;
//权限管理模块
class PermRootController extends CommonController {
    public $model = ['Root','R'];
    public function _map(&$data)
    {

        $map['status'] = ['neq','9'];
        //根据前台传来的值查询
        if(I('title')) {
            $map['infoname'] = ['like','%'.I('title').'%'];
        }

        $sql = D::get('RootLogin',[
            'order' => 'login_time DESC',
            'where' => 'status=0'
            ],false);
        $sql = D::get('',[
            'table' => $sql.' R',
            'group' => 'root_id',
            'field' => 'root_id,login_time,login_ip,count(*) count'
            ],false);
        $data = [
            'where' => $map,
            'join'  => "left join ".$sql." L on L.root_id = R.`id`",
            'field' => "R.*,IFNULL(L.login_time,0) `time`,IFNULL(L.login_ip,'-') `ip`,IFNULL(L.count,0) count"
        ];

    }
    public function index(){
        $db = parent::index(function($data){
            $data['time'] = $data['time'] == 0?'未登录':date('Y-m-d H:i:s',$data['time']);
            return $data;
        });
    }
    public function rootSta()
    {

        if(I('get.sta')==1) {

            $Ary["status"]=0;

            if(!D::count(['role_root','RT'],'RT.id='.I('get.id').'')) {
                $this->error("管理员尚未分配管理组，请先分配管理组！");
            }

            $row=D::find(['role_root','RT'],[
                'join'  => "__ROLE__ R on RT.role_id=R.role_id",
                'where' => "RT.root_id=".I('get.id').""
                ]);

            if($row["role_sta"]==1)
            {
                $this->error("该用户的所属的管理组已被禁用，请先启用管理组或为该用户重新分配管理组！");
            }
            else
            {
                if(D::save('root','id='.I('get.id').'',$Ary)>0)
                {
                    $this->success("启用成功！");
                }
                else
                {
                     $this->error("启用失败，请刷新后重试");
                }
           }
        }
        else if(I('get.sta')==0)
        {
            $Ary["status"]=1;

            if(D::save('root','id='.I('get.id').'',$Ary)>0)
            {
                $this->success("禁用成功！");
            }
            else
            {
                 $this->error("禁用失败，请刷新后重试");
            }
        }  

    }
    //修改管理员信息
    public function root_update()
    {

        $row=D::find(["root"],[
            'where' => "id=".I('get.id').""
            ]);

        $this->assign("row",$row);
        $this->display();
        
    }
    //修改管理员信息执行
    public function rootUpdeDo()
    {

        $temp=1;
        $i=0;
        if(I('post.infoname')==""||I('post.name')==""||I('post.number')==""||I('post.realname')=="")
        {
            $temp=0;
            $this->error("所有项均不能为空！");
        }

        if(!I('post.pwd1')&&!I('post.pwd2'))
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
            if($i==0)
            {
                $_POST["pwd"]=md5($_POST["pwd1"]);
            }

            unset($_POST["root_pwd2"]);
            unset($_POST["root_pwd1"]);

            $result=D::save("root","id=".I('post.id')."",$_POST);


            if($result===0){   
                $this->success("更新成功,数据无变化!",U("PermRoot/index"));
            }else if($result>0){
                $this->success("更新成功!",U("PermRoot/index"));
            }else{
                $this->error("更新失败，请刷新页面后重试！");
            }
        }
    }
    //添加管理员
    public function root_add()
    {

        $this->display();
        
    }
    public function rootAddDo()
    {
        if(I('post.pwd1')==""||I('post.infoname')==""||I('post.name')==""||I('post.number')==""||I('post.realname')=="")
        {
            $this->error("所有项均不能为空！");
        }

        else if(I('post.pwd1')!=I('post.pwd2'))
        {
            $this->error("两次输入的密码不一致！");
        }
        else
        {

            if(D::count("root",[
                'where' => "name='".I('post.name')."'"
                ])){
                    $this->error("已存在此账户名！");
            }else{
                unset($_POST["root_pwd2"]);
                unset($_POST["root_pwd1"]);

                $_POST["pwd"]=md5($_POST["pwd1"]);
                $_POST["status"]=1;
                $_POST["admin"]=$_SESSION["root_user"]["id"];
                if(D::add('root')>0)
                {   
                    $this->success("添加成功!",U("PermRoot/index"));
                }
                else
                {
                    $this->error("添加失败，请刷新页面后重试！");
                }
            }
        }
    }
    //管理员授权
    public function root_edit()
    {

        $roleRows=D::get('role',[
            'where' => "hotel=0 and role_sta=0"
        ]);

        $this->assign("roleRows",$roleRows);
        $this->assign("id",I('get.id'));
        $this->display();
        
    }
    //管理员授权执行
    public function roleGetRoot()
    {
        $row=D::delete('role_root',[
            'where' => "root_id=".I('root_id').""
            ]);
        if(($row===0||$row>0)&&D::add('role_root'))
        {
            $this->success("授权成功！",U("PermRoot/index"));
        }
        else
        {
            $this->error("授权失败,请刷新页面后重试！");
        }
    }
    //删除管理员
    public function rootDel()
    {
        
        $row=D::delete('role_root',[
            'where' => "root_id=".I('get.id').""
            ]);
        $Ary['status'] = 9;

        if(($row===0||$row>0)&&D::save('root',"id=".I('get.id')."",$Ary))
        {
            $this->success("删除成功!",U("PermRoot/index"));
        }
        else
        {
            $this->error("删除失败，请刷新页面后重试！");
        }

    }
}
