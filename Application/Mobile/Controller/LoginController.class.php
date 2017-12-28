<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\D;
class LoginController extends MobileCommonController {
	public $model = 'Users';
	public function index()
	{
		session('url',urldecode(I('state')));
		$code = I('code');//获取code票据
		$state = I('state');
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.C('WX_APP_ID').'&secret='.C('WX_APPSECRET').'&code='.$code.'&grant_type=authorization_code';
		$access_token = http_crul($url);
		//通过code换取网页授权access_token
		$refresh = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.C('WX_APP_ID').'&grant_type=refresh_token&refresh_token='.$access_token['refresh_token'];
		$refresh_token = http_crul($refresh);
		//通过code换取网页授权access_token
		$user = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$refresh_token['access_token'].'&openid='.$refresh_token['openid'].'&lang=zh_CN ';
		$user_data = http_crul($user);
		//通过code换取网页授权access_token

		if ($user_data['openid']) {
			$userHave = D::find('Users',['openid' => $user_data['openid']]);//检测用户是否存在于数据库中
			// dump($userHave);die;
			if ($userHave) {
				if ($userHave['status'] == 0) {//正常账号，允许登录
					session('user',$userHave);
					$this->redirect(urldecode(I('state')),'', 0, '页面跳转中...');
				}if($userHave['status'] == 1){
					header("Content-type: text/html; charset=utf-8");
					echo '<script type="text/javascript">alert("抱歉，该用户已被封停")</script>';die;
				}else{
					$this->assign('id',$userHave['id']);
					$this->display();
				}
			}else{
				/**
				 * [$image 处理用户头像信息]
				 * @var [type]
				 */
				if ($user_data['headimgurl']) {
					$img_save = put_file_from_url_content($user_data['headimgurl'],$user_data['openid'].NOW_TIME.'.jpg','./Uploads/hands/');
					if ($img_save === false) {
						// dump(1);
						$imgid = 0;
					}else{

						$image = new \Think\Image();
						$image->open('./Uploads/hands/'.$user_data['openid'].NOW_TIME.'.jpg');
						$files = [
						'savepath' => '/hands/',
						'savename' => $user_data['openid'].NOW_TIME.'.jpg',
						'name'     => $user_data['headimgurl'],
						'type'     => $image->mime(),
						'ext'      => $image->type(),
						];
						$imgid = D::add('Files',$files);
					}
				}else{
					$imgid = 0;
				}
				$newid = D::add('Users',[
					'openid'     => $user_data['openid'],
					'nickname'   => $user_data['nickname'],
					'mobile'     => '',
					'sex'        => $user_data['sex'],
					'country'    => $user_data['country'],
					'province'   => $user_data['province'],
					'city'       => $user_data['city'],
					'headimgid'  => $imgid,
					'createTime' => time(),
					'updateTime' => time(),
					'status'     => 2,
					]);
				// session('user',D::find('Users',$newid));
				$this->assign('id',$newid);
				$this->display();
			}
		}else{
			$this->display();
			$url = U('Index/index');
			$echo = <<<html
			<script type="text/javascript">
				layer.open({
					content: '您的请求已过期，请重新尝试'
					,btn: '我知道了'
					,end:function(){
						window.location.href="{$url}";
					}
				});
			</script>
html;
			echo $echo;
		}
	}
	/**
	 * [binding 用户绑定手机号等信息]
	 * @Author   尹新斌
	 * @DateTime 2017-07-14
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function binding()
	{
		if (!I('mobile') || !I('realname') || !I('validate')) {
			$this->error('请填写完整信息');
		}else{
			if (md5(I('validate')) != S('validate')) {
				$this->error('验证码填写不正确或已失效');
			}else{
				$url = session('url')?U(session('url')):U('Self/index');
				$flag = D::save('Users',I('id'),[
					'status' => 0,
					'mobile' => I('mobile'),
					'realname' => I('realname')
					]);
				if ($flag) {
					session('user',D::find('Users',I('id')));
					$this->success('绑定成功',$url);
				}else{
					$this->error('不明错误');
				}
			}
		}
	}
	/**
	 * [messages 短信验证码发送类]
	 * @Author   尹新斌
	 * @DateTime 2017-07-14
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function message()
	{
		if(preg_match("/^1[34578]{1}\d{9}$/",I('mobile'))){
			$validate = get_number(4);//获取随机数
			S('validate',md5($validate),300);//5分钟内有效
			//短信接口位置
			//
			vendor("PHPSms.PHPSms");
			$appid = C('SMS_APPID');
		    $appkey = C('SMS_APPKEY');
			$ojb = new \PHPSms($appid,$appkey);
			$a = $ojb -> sendSms(I('mobile'),C('SMS_TEMPLID'),[$validate]);//手机号 模板号 正文部分
			$flag = json_decode($a)->errmsg;
			//dump($flag);die;
			//$flag = 发送至I('mobile') 发送代码$validate
			//短信接口位置
			//$flag = true;//接上短信接口后请注释此行代码
			if ($flag == 'OK') {
				$this->ajaxReturn('true');
			}else{
				$this->ajaxReturn('false');
			}
		}else{
			$this->ajaxReturn('mobileError');
		}
	}
}