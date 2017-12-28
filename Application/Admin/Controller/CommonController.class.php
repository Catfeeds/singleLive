<?php
namespace Admin\Controller;
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
        $URL=CONTROLLER_NAME;

        $config = getConfig('',0);

        session('config',$config);

        if(ACTION_NAME!="login"&&ACTION_NAME!="loginDo"&&!isset($_SESSION["root_user"])){
            $this->redirect('/Admin/Index/login', array(), 0, '页面跳转中...');
        }else{
            $row=M("role_root")->where("root_id=".$_SESSION["root_user"]["root_id"]."")->find();
            $permCount=M("perm_role")->join("perm on perm_role.perm_id=perm.perm_id")
                      ->where("role_id=".$row["role_id"]." and perm_url='$URL'")->count();
            if($permCount===0){
                $this->error("对不起，您没有权限访问当前页面！",U("Admin/Index/login"));
            }else{
                $temp=M('perm')->where("perm_url='$URL' and status=0")->find();
                $item=M('perm')->where('perm_id='.$temp['perm_parentid'].' and status=0')->find();
                if($temp["perm_parentid"]==0){
                    $item['perm_type']="首页";
                }

                session('config', getConfig());
                session('ParentContor',$item['perm_type']);
                session('Controller',$URL);
            }
        }
    }
}