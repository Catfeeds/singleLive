<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\D;
class SelfController extends CommonController{
	public static $login = true;
	public function _map(&$data)
	{
		switch (ACTION_NAME) {
			case 'value':
				# code...
				break;
			case 'club':
				$map['H.type'] = 'm';
				$data = [
					'alias' => 'H',
					'table' => '__ENVIRONMENT__',
					'where' => $map,
					'join'  => 'LEFT JOIN __FILES__ F ON F.id = H.pic',
					'field' => "H.id,H.name,H.word,CONCAT('/Uploads',F.savepath,F.savename) `icon`"
				];
				break;
		}
	}
	/**
	 * [index 我的]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function index()
	{
		$this->display();
	}
	/**
	 * [information 基本信息]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function information()
	{
		$db = D::find('Users',session('user'));
		$this->assign('db',$db);
		$this->display();
	}
	/**
	 * [balance 账户余额]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function balance()
	{
		$this->display();
	}
	/**
	 * [coupon 电子券]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function coupon()
	{
		$this->display();
	}
	/**
	 * [upgrade 积分升级]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function upgrade()
	{
		$db = D::get('Grades',[
			'alias' => 'G',
			'where' => ['G.status' => '1'],
			'order' => 'sort ASC',
			'join'  => 'LEFT JOIN __FILES__ F ON F.id = G.pic',
			'field' => "G.*,CONCAT('/Uploads',F.savepath,F.savename) `icon`"
		]);
		$this->assign('db',$db);
		$this->display();
	}
	/**
	 * [message 系统消息]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function message()
	{
		$this->display();
	}
	/**
	 * [messageEdit 系统消息-详情]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function messageEdit()
	{
		$this->display();
	}
	/**
	 * [activity 活动消息]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function activity()
	{
		$this->display();
	}
	/**
	 * [activityEdit 活动消息-详情]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function activityEdit()
	{
		$this->display();
	}
	/**
	 * [password 修改密码]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function password()
	{
		$this->display();
	}
	public function updatePass()
	{
		$password = D::field('Users.password',session('user'));
		if ( I('password') != I('oldpassword') ) {
			$this->error('密码不能与原密码一致');die;
		}
		if ( I('password') != I('repassword') ) {
			$this->error('两次密码输入不一致');die;
		}
		if (md5(I('oldpassword')) != $password ) {
			$this->error('原密码错误');die;
		}
		$flag = D::save('Users',session('user'));
		if ($flag) {
			$this->success('修改成功');
		}else{
			$this->error('修改失败');die;
		}
	}
	/**
	 * [problem 常见问题]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function problem()
	{
		$db = D::get('Problem',['order' => 'add_time DESC']);
		$this->assign('db',$db);
		$this->display();
	}
	/**
	 * [club 会员俱乐部]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function club()
	{
		if (IS_AJAX) {
			parent::index();
		}else{
			$this->display();
		}
	}
	/**
	 * [club 会员俱乐部]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function clubEdit()
	{
		$house = D::find('Environment',$id);
		$bannerMap['id'] = ['in',array_filter(explode(',', $house['imgs']))]; //banner ids
		$Banners = D::get('Files',$bannerMap); //获取banner
		$this->assign('banners',$Banners);
		$this->assign('db',$house);
		$this->display();
	}
}