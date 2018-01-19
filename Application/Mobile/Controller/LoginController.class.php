<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\D;
class LoginController extends CommonController{
	public $model = "Users";
	public $success = ['insert' => '注册成功，正在跳转...'];
	/**
	 * [index 登录页]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function index()
	{
		C('LAYOUT_ON',false);
		$this->display();
	}
	/**
	 * [password 忘记密码]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function password()
	{
		$this->display();
	}
	/**
	 * [register 用户注册]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function register()
	{
		// sendSMS(18622977554,1234);
		$this->display();
	}
	/**
	 * [verification 发送验证码]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @param    [type]     $mobile [手机号码]
	 * @return   [type]             [description]
	 */
	public function verification($mobile)
	{
		$code = rand(1000,9999);
		S('code',[
			'mobile' => $mobile,
			'code' =>$code,
		]);
		$this->ajaxReturn(sendSMS($mobile,$code));
	}
	/**
	 * [login 登录操作]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function login()
	{
		if ( I('mobile') && I('password') ) {
			$map = [
				'mobile' => I('mobile')
			];
			$user = D::find('Users',$map);
			if ($user) {
				if ( md5(I('password')) === $user['password']) {
					if($user['status'] == 1){
						session('user',$user['id']);
						$this->success('登录成功，正在跳转...',S('url'));
					}else{
						$this->error('您的账号已被禁用或删除');
					}
				}else{
					$this->error('密码错误');
				}
			}else{
				$this->error('手机号未注册');
			}
		}else{
			$this->error('请填写手机号和登录密码');
		}
	}
	/**
	 * [logout 用户退出操作]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function logout()
	{
		session('user',null);
		$this->success('正在退出...',U('Index/index'));
	}
	/**
	 * [insert 用户注册]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function insert()
	{
		parent::insert(function($id){
			session('user',$id);
		});
	}
}