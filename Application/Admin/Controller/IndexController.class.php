<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;
class IndexController extends CommonController {
    public function _map(&$data)
    {
        switch (ACTION_NAME){
            case 'index':
                if ( I('start') || I('end') ) {
                    $map['time'] = get_selectTime(I('start'),I('end'));
                }
                $sql = D::get('Finance',[
                    'where' => $map,
                    'field' => "UNIX_TIMESTAMP(MAX(createDate)) time,SUM(CASE WHEN type='pay' THEN money ELSE 0 END) upPay,SUM(CASE WHEN type='recharge' THEN money ELSE 0 END) up,SUM(CASE WHEN type='back' THEN money ELSE 0 END) down",
                    'group' => 'createDate',
                ],false);
                $data = [
                    'table' => "{$sql} M",
                    'field' => 'M.time,(M.upPay+M.up) inMoney,M.down outMoney,(M.upPay+M.up-M.down) sideMoney',
                    'order' => 'M.time DESC',
                ];
                break;
            case 'see':
                if(I('title')){
                    $map['CONCAT(U.realname,U.mobile)'] = array('like','%'.I('title').'%');
                }
                $map['createDate'] = I('date');
                $data = [
                    'alias' => 'F',
                    'table' => '__FINANCE__',
                    'where' => $map,
                    'join'  => 'LEFT JOIN __USERS__ U ON U.id = F.userID',
                    'field' => 'F.*,U.realname,U.mobile',
                    'order' => 'createDate desc,id desc'
                ];
                break;
        }

    }
    public function index()
    {
        $db = parent::index(false);
        $showNum = [
            'user' => $this->countNum('users','1,2'),
            'status1' => $this->countNum('Order','1'),
            'status2' => $this->countNum('Order','2'),
            'status3' => $this->countNum('Order','3'),
            'status4' => $this->countNum('Order','4'),
            'status6' => $this->countNum('Order','6'),
            'status8' => $this->countNum('Order','8'),
            'status9' => $this->countNum('Order','9')
        ];
        //dump($showNum);die;
        $this->assign('Count',$showNum);
        $this->assign('db',$db['db']);
        $this->assign('page',$db['page']);
        $this->display();
    }
    /*
     *  统计函数
     *      查询表名 $table [string]
     *      查询状态 $status [string]
     * */
    public function countNum($table,$status){
        if($table == 'users'){
            $map['status'] = array('in',explode(',',$status));
            $count = D::count("$table",['where'=>$map]);
        }else{
            $count = D::count("$table",$status);
        }
        return $count;
    }
    //登录页面
    public function login()
    {
        session('root_user',null);

        $this->display();
    }
    //登录验证
    public function loginDo()
    {

        $_POST["pwd"]=md5(I('post.pwd'));

        $row=M("root")->where(I('post.'))->find();
        
        if($row&&$row["admin"]==0)
        {

            $Ary = [
                'login_time' => time(),
                'root_id' => $row['id'],
                'login_ip' => get_client_ip(),
                'status' => 0
            ];           
            M('root_login')->add($Ary);

            //获取管理员的权限，添加到前台
            $permRows=D::get('perm','status=0 and perm_parentid=0');

            foreach ($permRows as $key => $permRow) 
            {
                if($permRow["perm_url"]=="Pwd")
                {
                    session('root_Pwd',$permRow);
                }
                
                $permRows[$key]["subClass"]=D::get('perm','status=0 and perm_parentid='.$permRow['perm_id'].''); 
                               
            }

            unset($row['root_pwd']);
            session('root_user',$row);
            session('root_permRows',$permRows);

            $this->success('登录成功',U('Index/index'));

        }else if($row&&$row['status']==0){

            $Ary = [
                'login_time' => time(),
                'root_id' => $row['id'],
                'login_ip' => get_client_ip(),
                'status' => 0
            ];           
            M('root_login')->add($Ary);


            $rootRow=D::find(['role_root','R'],'R.root_id='.$row['id']."");

            $permRows=D::get(['perm_role','PR'],[
                    'join'  => '__PERM__ P on PR.perm_id=P.perm_id',
                    'order' => 'P.perm_id',
                    'where' => 'PR.role_id='.$rootRow["role_id"].' and P.perm_parentid=0 and P.status=0',
                    'field' => 'P.*'
                ]);  

            foreach ($permRows as $key => $permRow) 
            {
                if($permRow["perm_url"]=="Pwd")
                {
                    session('root_Pwd',$permRow);
                }
                $permRows[$key]["subClass"]=D::get(['perm_role','PR'],[
                        'join'  => '__PERM__ P on PR.perm_id=P.perm_id',
                        'order' => 'P.perm_id',
                        'where' => 'PR.role_id='.$rootRow["role_id"].' and P.perm_parentid='.$permRow["perm_id"].' and  P.status=0'
                ]);
            } 

            unset($row['root_pwd']);
            session('root_user',$row);
            session('root_permRows',$permRows);

            $this->success('登录成功', U('Index/index'));


        }else if($row){
            $this->error("您的账户已被禁用，请联系管理员！");
        }else{
            $this->error("用户名或密码错误");
        }

    }
    //欢迎页
    public function welcome()
    {

        $this->display();

    }
}
