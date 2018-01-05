<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;
class CommonController extends Controller{
	public static $login = false;
	public function _initialize(){
		if ( static::$login === true ) {
			//需要登录
			if ( !session('user') ) {
				S('url',__SELF__); //缓存当前浏览页面
				$this->redirect('Login/index', [], 0, '页面跳转中...');
			}else{

			}
		}else{
			//不需要登录
		}
	}
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
}