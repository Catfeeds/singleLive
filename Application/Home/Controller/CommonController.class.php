<?php
namespace Home\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;
class CommonController extends Controller {
    public $Config;
    public function __call($function_name,$argments)
    {
        $model = Faster::start($this->model);
        if (method_exists($model,$function_name)) {
            $data = call_user_func_array([$model,$function_name],[$this,$argments]);
            switch ($data['type']) {
                case 'return': return $data['data']; break;
                case 'display':
                    foreach ($data['assign'] as $key => $value) {
                        $this->assign($key,$value);
                    }
                    $this->display();
                    break;
                case 'error': $this->error($data['msg']); break;
                case 'success': $this->success($data['msg'],$data['url']); break;
                case 'ajax': $this->ajaxReturn($data['assign']); break;
            }
        }else{
            parent::__call($function_name,$argments);
        }
    }

    public function _initialize()
    {
        $mobile = getConfig('mobile',0);
        $this->assign('mobile',$mobile);
        $URL=CONTROLLER_NAME;
        if(ACTION_NAME!="login"&&ACTION_NAME!="loginDo"&&!isset($_SESSION["hotel_user"])){
            $this->redirect('/Home/Index/login', array(), 0, '页面跳转中...');
        }else{
            $i = 0;
            if($_SESSION["hotel_user"]["root"]==0){
                $i = 1;
            }else{
                $row = M("role_root")->where("root_id=".$_SESSION["hotel_user"]["group"]."")->find();

                $permCount=M("perm_role")->join("perm on perm_role.perm_id=perm.perm_id")
                        ->where("role_id=".$row["role_id"]." and perm_url='$URL'")->count();   
                if($permCount===0){
                    $this->error("对不起，您没有权限访问当前页面！",U("Admin/Index/login"));
                }else{
                    $i = 1;
                }                     
            }
            if($i==1){
                $temp=M('perm')->where("perm_url='$URL' and status=1")->find();
                $item=M('perm')->where('perm_id='.$temp['perm_parentid'].' and status=1')->find();
                $count=D::count(['news_hotel','NH'],[
                        'where' => 'NH.hotel='.session('hotel_user.hotel').' and NH.status=0 and N.status!=9',
                        'join' => '__NEWS__ N on N.id=NH.news'
                    ]);
                session('counts',$count);
                session('ParentContor',$item['perm_type']);
                session('Controller',$URL); 
                session('config', getConfig());
            }
            /*
             *  查询当前酒店  是否存在正在入住的订单
             * */
            $sel = array(
                'A.status' => 2,
                'B.hotel' =>session('hotel_user.hotel')
            );
            $hotel_ing = D::count(['OrderHotel','A'],[
                'where' => $sel,
                'join'  => [
                    'LEFT JOIN __ORDER__ B ON B.id = A.orderId'
                ]
            ]);
            $this->assign('hotel_ing',$hotel_ing);
        }
    }


}