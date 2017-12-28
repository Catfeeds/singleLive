<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;
class IndexController extends CommonController {

    public $model = ['OrderHotel','OH'];
    public function _map(&$data)
    {
        if ( I('title') ) {
            $map['CONCAT(OH.no,U.realname,U.mobile)'] = ['like','%'.I('title').'%'];
        }
        if ( I('start') || I('end') ) {
            $map['OH.createTime'] = get_selectTime( I('start'),I('end') );
        }

        $where['OH.status'] = ['eq',2];        
        $where['OH.endTime'] = ['gt',strtotime(date('Y-m-d'))]; 
        $where['_logic'] = 'or';
        $map['_complex'] = $where;

        $data = [
            'where' => $map,
            'field' => 'OH.*,U.realname,U.nickname,U.mobile',
            'join'  => [
                'LEFT JOIN __USERS__ U ON U.id = OH.userId'
            ],
            'order' => 'OH.createTime DESC',
        ];

    }
    public function index()
    {
        $Count = [
            'userCount' => D::count('users','status!=9'),           //用户总数
            'hotelCount' => D::count('hotels','status!=9'),         //酒店总数
            'orderHangCount' => D::count('order_hotel','status=2'), //截止现在未完成订单数
            'orderCompleteCount' => D::count('order_hotel','endTime>'.strtotime(date('Y-m-d')).'')//今日已完成订单数
        ];

        $info = http_build_query(I('get.'));

        $this->assign('Count',$Count);
        $this->assign('info',$info);
        parent::index('checkData');
    }

    /*
        封装一个数据集函数
    */
    public function checkData( $data ){
        $order = D::find(['Order','O'],[
            'where' => ['O.id' => $data['orderId']],
            'join'  => [
                'LEFT JOIN __HOTEL_ROOMS__ R ON R.id = O.room',
                'LEFT JOIN __HOTELS__ C ON C.id = R.hotel'
            ],
            'field' => 'R.*,(O.duration - O.used) have,C.hotelName',
        ]);
        $data['roomType'] = D::field('Rooms.roomName',$order['room']);
        $data['hotelName'] = $order['hotelName'];
        $data['uname'] = D::field('Users.realname',$data['userId']);
        $data['utel'] = D::field('Users.mobile',$data['userId']);
        $data['have'] = $order['have'];
        $data['min'] = $order['minimum'];
        $data['minute'] = $order['minute'];
        $data['now'] = NOW_TIME - $data['startTime'];
        $h = floor(($data['endTime'] - $data['startTime']) / 3600);
        $m = floor((($data['endTime'] - $data['startTime'])% 3600) / 60);
        $s = floor((($data['endTime'] - $data['startTime'])% 3600) % 60);
        $data['old'] = ($data['status'] == 1)?str_pad($h,2, "0", STR_PAD_LEFT).':'.str_pad($m,2, "0", STR_PAD_LEFT).':'.str_pad($s,2, "0", STR_PAD_LEFT):'00:00:00';
        if ($data['status'] == 1) {
            if ($h < $order['minimum']) {
                $data['use'] = $order['minimum'];
            }else{
                $data['use'] = ($order['minute'] <= $m )?$h + 1:$h;
            }
        }else{
            $data['use'] = 0;
        }
        //订单金额
        if($data['use']==0){
            $data['umoney'] = 0;
        }else{
            $data['umoney'] = $data['use']*$order['amount'];
        }
        return $data;
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
