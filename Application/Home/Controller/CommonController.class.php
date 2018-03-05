<?php
namespace Home\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;
class CommonController extends Controller{
	public static $login = false;
	public function _initialize(){
        S('url',__SELF__); //缓存当前浏览页面
		if ( static::$login === true ) {
			//需要登录
			if ( !session('user') ) {
				$this->redirect('Login/index', [], 0, '页面跳转中...');
			}else{
                //获取当前会员的  等级 积分 余额以便页面赋值
                $userID = session('user');
                $users = D::find('Users',$userID);
                //调用积分监控函数
                event_user_level($userID);
                $sorce  = D::find('UserSorce',[
                    'where' => ['userID'=>$userID],
                    'field' => "SUM(CASE WHEN method = 'plus' THEN sorce ELSE 0 END) up,SUM(CASE WHEN method = 'sub' THEN sorce ELSE 0 END) down"
                ]);
                $nowgrade = $users['nowLevel'] != 0 ? D::field('Grades.title',$users['nowLevel']) : '顾客';
                $myBalance = D::find('Balance',[
                    'where' => ['userID'=>$userID,'status'=>1],
                    'field' => "SUM(CASE WHEN method = 'plus' THEN money ELSE 0 END) upPay,SUM(CASE WHEN method = 'back' THEN money ELSE 0 END) upBack,SUM(CASE WHEN method = 'sub' THEN money ELSE 0 END) down"
                ]);
                $userMsg = [
                    'mySorce' => $sorce['up'] - $sorce['down'],
                    'myGrade' => $nowgrade,
                    'myBalance' => $myBalance['upPay']+$myBalance['upBack'] - $myBalance['down'],
                    'myName' => $users['realname'],
                    'uid'   => $users['id'],
                    'headImg'   => $users['headImg']
                ];
                $this->assign('my',$users);
                $this->assign('userMsg',$userMsg);
			}
		}else{
			//不需要登录
            if(session('user')){
                $users = D::find('Users',session('user'));
                $this->assign('my',$users);
            }
		}
        //加载网站设置
        $webConfig = D('Config')->get_config();
        /*
         *  搜索查询
         * */
        $nowTime = date('Y-m-d');
        $endTime = date('Y-m-d',strtotime("$nowTime +3 month"));
        $allowTimes = [
            'min' =>$nowTime,
            'max' =>$endTime
        ];
        $houses = D::get('House',['where'=>['status'=>1]]);
        $this->assign('houses',$houses);
        $this->assign('allowTimes',$allowTimes);
        $this->assign('web',$webConfig);
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