<?php
namespace Home\Controller;
use Think\Controller;
use Think\D;
//欢迎页面
class IndexController extends CommonController {
//	public $model = ['']
	public function index()
	{
		$this->display();
	}
	public function login()	
	{
		session('hotel_user',null);
		$this->display();
	}	
	//登录验证
    public function loginDo()
    {
        $_POST["password"]=md5(I('post.password'));

        $row=D::find(['hotel_admins','HA'],[
                'join'  => 'left join __HOTELS__ H on HA.hotel=H.id',
                'where' => "HA.username='".I('post.username')."' and HA.password='".I('post.password')."'",
                'field' => 'HA.*,H.hotelName,H.status sta'
            ]);

            /*
                获取酒店配置信息
             */
            $configs = getConfig('',$row['hotel']);

            session('hotel_config',$configs);

        if($row&&$row["root"]==0&&$row["sta"]==0){

            $Ary = [
                'login_time' => time(),
                'root_id' => $row['id'],
                'login_ip' => get_client_ip(),
                'status' => 1
            ];           
            M('root_login')->add($Ary);

            //获取管理员的权限，添加到前台
            $permRows=D::get('perm','status=1 and perm_parentid=0');
            foreach ($permRows as $key => $permRow) 
            {
                if($permRow["perm_url"]=="Pwd")
                {
                    session('hotel_Pwd',$permRow);
                }
                $permRows[$key]["subClass"]=D::get('perm','status=1 and perm_parentid='.$permRow['perm_id'].'');
            }


            unset($row['root_pwd']);
            session('hotel_user',$row);
            session('hotel_permRows',$permRows);

            $this->success('登录成功',U('Index/index')); 
        }else if($row&&($row["status"]==1||$row["status"]==9||$row["sta"]==1||$row["sta"]==9)){

            $this->error('对不起，你的账号已被封停，请于管理员联系！');


        }else if(!$row){

            $this->error('用户名或密码错误，请重新输入!');

        }else if($row&&$row["status"]==0){


            $Ary = [
                'login_time' => time(),
                'root_id' => $row['id'],
                'login_ip' => get_client_ip(),
                'status' => 1
            ];           
            M('root_login')->add($Ary);

            $permRows=D::get(['perm_role','PR'],[
                    'join'  => '__PERM__ P on PR.perm_id=P.perm_id',
                    'order' => 'P.perm_id',
                    'where' => 'role_id='.$row["group"].' and perm_parentid=0 and P.status=1'
                ]);


            foreach ($permRows as $key => $permRow) 
            {
                if($permRow["perm_url"]=="Pwd")
                {
                    session('hotel_Pwd',$permRow);
                }
                $permRows[$key]["subClass"]=D::get(['perm_role','PR'],[
                        'join'  => '__PERM__ P on PR.perm_id=P.perm_id',
                        'order' => 'P.perm_id',
                        'where' => 'role_id='.$row["group"].' and perm_parentid='. $permRow["perm_id"].' and P.status=1'
                ]);
            } 
            unset($row['root_pwd']);
            session('hotel_user',$row);
            session('hotel_permRows',$permRows);

            $this->success('登录成功',U('Index/index')); 

        }
        
	}
}