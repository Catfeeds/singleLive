<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;
//权限管理模块
class PermController extends CommonController {
    public $model=['Role','R'];
    public function _map(&$data)
    {
        //前台值进行查询
        $SQL['hotel']     = 0;
        $SQL['role_sta']  = ['neq','9'];

        //前台值进行查询
        if(I('title')){
            $map['role_type'] = ['like','%'.I('get.title').'%'];
            $map['role_info'] = ['like','%'.I('get.title').'%'];
            $map['_logic'] = 'OR';
            $SQL['_complex'] = $map;
        }

        $data=[
            'where' => $SQL
        ];
    }   
    //新增权限组
    public function index_added()
    {
         
        $this->display();

    }

    //增加权限组
    public function roleAdd()
    {

        if(!I('role_type'))
        {
            $this->error("添加失败，用户组名不能为空！");
        }
        else 
        {
            $SQL="`role_type`='".I('post.role_type')."'";

            $roleCount=D::count('role',$SQL);

            if($roleCount>0)
            {
                $this->error("添加失败，已存在该用户组！");
            }
            else
            {
                $_POST["role_sta"]=1;
                $_POST['hotel'] = 0;

                $row=D::add('role');

                if($row>0)
                {
                    $this->success("添加成功！",U("Perm/index"));
                }
                else
                {
                    $this->error("添加失败，请刷新后重试！");
                }

            }
        }
    }

    //启用或禁用权限组
    public function RoleSta()
    {
        if(I('get.sta')==1)
        {
            $Ary["role_sta"]=0;

            if(!D::count('perm_role','role_id='.I('get.role_id').''))
            {
                $this->error("管理组尚未设置权限，请先设置权限！");
            }
            else if(D::save('role','role_id='.I('get.role_id').'',$Ary))
            {
                $this->success("启用成功！");
            }
            else
            {
                $this->error("启用失败，请刷新后重试");
            }
        }
        else if(I('get.sta')==0)
        {
            $Ary["role_sta"]=1;

            //禁用管理组时将使用此管理组的管理员的状态也变为禁用
            $rows=D::get('role_root','role_id='.I('get.role_id').'');

            foreach ($rows as $key => $row) 
            {
                $rootAry["root_sta"]=1;
                D::save('root','root_id='.$row["root_id"].'',$rootAry);
            }

            if(D::save('role','role_id='.I('get.role_id').'',$Ary))
            {
                $this->success("禁用成功！");
            }
            else
            {
                 $this->error("禁用失败，请刷新后重试");
            }
        }
    }

    //删除权限组
    public function RoleDel()
    {
        $Ary['role_sta'] = 9;        
        $row=D::save('role','role_id='.I('get.role_id').'',$Ary);

        $permRow=D::delete('perm_role','role_id='.I('get.role_id').'');

        //删除管理组时将使用此管理组的管理员的状态将变为禁用
        $rows=D::get('role_root','role_id='.I('get.role_id').'');

        foreach ($rows as $key => $row) 
        {
            $rootAry["status"]=1;
            D::save('root','id='.$row["root_id"].'$rootAry');
        }

        if($row>0){
            $this->success("删除成功！");
        }else{
            $this->error("删除失败，请刷新后重试！");
        }
    }
    //权限组授权
    public function index_edit()
    {

        $permRows=D::get('perm',[
                'where' => 'status=0 and perm_parentid=0',
                'order' => 'perm_id'
            ]);

        foreach ($permRows as $key => $permRow) 
        {
            $type=D::find('perm','perm_id='.$permRow["perm_id"].'');

            $permRows[$key]["type"]=$type["perm_type"];

            //讲二级分类存储为一个二维数组，以便前台查看
            $permRows[$key]["subClass"]=array();

            $permRows[$key]["subClass"]=D::get('perm',[
                    'where' => 'status=0 and perm_parentid='.$permRow['perm_id'].'',
                    'order' => 'perm_id'
            ]);
        }

        //查询想要更改权限的权限组原有的权限
        $role=D::get('perm_role','role_id='.I('get.role_id').'');

        $code=array();
        foreach ($role as $perm) 
        {
            $code[]=$perm["perm_id"];
        }
        $this->assign("role_id",I('get.role_id'));
        $this->assign("codes",$code);
        $this->assign("permRows",$permRows);
        $this->display();
    }
    //权限组授权执行
    public function permEditDo()
    {

        $Ary["role_id"]=I('post.role_id');
        $temp=0;
        if(I('post.perm_id')==""){

             $this->error("错误，权限不能设置为空！");
             $temp=1;
        }else if($temp==0){
                
            if(D::delete('perm_role',$Ary)===false){
                    $this->error("更新失败,请刷新后重试！");
                    $temp=1;
            }

            foreach ($_POST["perm_id"] as $Key => $perm){

                $Ary["perm_id"]=$Key;

                D::add('perm_role',$Ary);
                foreach ($perm as $key => $subClass){

                    $Ary["perm_id"]=$key;

                   D::add('perm_role',$Ary);
                }
            }

            $this->success("更新成功！",U("Perm/index"));
        }  

    }

}
