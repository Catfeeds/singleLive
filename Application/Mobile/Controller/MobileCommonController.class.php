<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;
class MobileCommonController extends Controller {
    public static $open = true;//控制器开放
	/**
	 * [_initialize 启用方法]
	 * @Author   尹新斌
	 * @DateTime 2017-07-13
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function _initialize()
	{
        $this->assign('AdminMobile',D::field('Config.value',['key' => 'mobile']));
        /**
         * [$open 判定是否开放控制器，需要登录]
         * @var [type]
         */
        if (session('user')) {
            $s = D::field('Users.status',session('user.id'));
            if ($s == 9) {
                session('user',null);
            }
            if ($s == 2) {
                session('user',null);
            }
        }
		if (static::$open === false) {
            if (!session('user')) {
                redirect(wx_url('',urlencode(CONTROLLER_NAME.'/'.ACTION_NAME)),0,'');//如果未登录跳转到微信授权页面进行页面登录
            }else{
                $c = D::count('Users',session('user.id'));
                //查询未读系统消息
                $count=D::count(['news_user','NU'],[
                        'where' => 'NU.users='.session('user.id').' and NU.status=0 and N.status!=9',
                        'join' => '__NEWS__ N on N.id=NU.news'
                    ]);
                session('counts',$count);
                //查询当前用户 是否有正在入住的订单
                $search = array(
                    'userId' => session('user.id'),
                    'status' => 2
                );
                $ing_hotel = D::count('OrderHotel',['where'=>$search]);
                session('ing_hotel',$ing_hotel);
                if ($c < 1) {
                    session('user',null);
                    redirect(wx_url('',urlencode(CONTROLLER_NAME.'/'.ACTION_NAME)),0,'');//如果未登录跳转到微信授权页面进行页面登录
                }
            }
        }
        //读取网站配置：title和logo AND 用户使用协议
        $comm['title'] = getConfig('clientName',0);
        $comm['head'] = getConfig('head',0);
        $comm['UserAgree'] = getConfig('UserAgree',0);

        $this->assign('ing_hotel',session('ing_hotel'));
        $this->assign('uid',session('user.id'));
        $this->assign('msg',session('counts'));
        $this->assign('com',$comm);
	}
	/**
	 * [__call 框架基层方法]
	 * @Author   尹新斌
	 * @DateTime 2017-07-13
	 * @Function []
	 */
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