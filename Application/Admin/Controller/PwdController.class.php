<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;
//密码修改模块
class PwdController extends CommonController {
    public function index()
    {
        $this->display();        
    }

    //修改密码
    public function updatePwd()
    {
    	if(I('post.pwd')=="")
    	{
    		$this->error("原密码不能为空！");
    	}
    	else if(I('post.pwd2')!=I('post.pwd2'))
    	{
    		$this->error("两次输入的密码不一致！");
    	}
    	else if(I('post.pwd')==I('post.pwd1'))
    	{
    		$this->error("更改后的密码与原密码相同！");
    	}
    	else
    	{
    		$id=session('root_user.id');

    		$row=D::find('root',$id);

    		if($row["pwd"]!=md5(I('pwd')))
    		{
    			$this->error("原密码错误！");
    		}
    		else
    		{
    			$Ary["pwd"]=md5(I('pwd1'));
    			if(D::save('root',$id,$Ary)>0)
    			{
    				$this->success("密码修改成功！请重新登录",U('Index/login'));
    			}
    			else
    			{
    				$this->error("密码格式错误！");
    			}
    		}
    	}

    }
}
