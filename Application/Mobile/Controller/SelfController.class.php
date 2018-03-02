<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\D;
class SelfController extends CommonController{
	public static $login = true;
	public function _map(&$data)
	{
		switch (ACTION_NAME) {
			case 'message':
			case 'activity':
				$map['N.status']     = '1';
				$map['N.createTime'] = ['gt',D::field('Users.createTime',session('user'))];
				$map['N.users']      = ['in',[session('user'),'0']];
				if (ACTION_NAME  == 'message') {
					$map['N.type']       = 'sys';
				}else{
					$map['N.type']       = 'act';
				}
				$data = [
					'alias' => 'N',
					'table' => '__NEWS__',
					'where' => $map,
					'order' => 'N.createTime DESC'
				];
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
			case 'balance':
				$map['userID'] = session('user');
				$map['status'] = 1;
				$data = [
					'table' => '__BALANCE__',
					'where' => $map,
					'order' => 'createTime desc'
				];
				break;
			case 'integral':
				$map['userID'] = session('user');
				$data = [
					'table' => '__USER_SORCE__',
					'where' => $map,
					'order' => 'createTime desc'
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
		$look = D::find('NewsUser',['userId' => session('user')]);
		$time = D::field('Users.createTime',session('user'));
		/* 未读的系统消息 */
		$newLooks = $look['newsIds']?explode(',', $look['newsIds']):[0];
		$newsMap = [
			'status'     => '1',
			'createTime' => ['gt',$time],
			'users'      => ['in',[session('user'),'0']],
			'type'       => 'sys',
			'id'         => ['notin',$newLooks],
		];
		$count['sys'] = D::count('News',$newsMap);
		/* 未读的系统消息 */
		/* 未读的活动消息 */
		$activityLooks = $look['activitysIds']?explode(',', $look['activitysIds']):[0];
		$activitysMap = [
			'status'     => '1',
			'createTime' => ['gt',$time],
			'users'      => ['in',[session('user'),'0']],
			'type'       => 'act',
			'id'         => ['notin',$activityLooks],
		];
		$count['act'] = D::count('News',$activitysMap);
		/* 未读的活动消息 */
		$this->assign('count',$count);
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
	 * 	zhenHong~
	 */
	public function balance()
	{
		if(IS_AJAX){
			parent::index(function($data){
				if($data['method'] == 'plus'){
					$data['method_name'] = '充值';
				}elseif($data['method'] == 'back'){
					$data['method_name'] = '退款反还';
				}else{
					$data['method_name'] = '余额消费';
				}
				$data['createTime'] = date('Y-m-d');
				return $data;
			});
		}
		$this->display();
	}
	//充值余额显示页面
	public function balanceIn(){
		$db = D::get('Recharge',[
			'where' => "`status`=1",
		]);
		$this->assign('db',$db);
		$this->display();
	}
	/*
	 *充值余额 生成订单  并跳转微信支付
	 * */
	public function add_my_balance(){
		//插入余额 充值表(其实就是充值订单表)
		$id = I('id');
		$recharge = D::find('Recharge',$id);
		$orderNo = 'C'.time().get_random_number(5);
		$order = [
			'userID' => session('user'),
			'money' => $recharge['money'],
			'orderNo' => $orderNo,
			'method' => 'plus',
			'sorce' => $recharge['sorce'],
			'createTime' => time(),
			'status' => 2,
		];
		M('Balance')->add($order);
		//本地测试
		//A('Index')->wechatPay($orderNo);
	    //微信支付
		$url = '/WeiXinPay/example/jsapi.php?title=房间预订&orderNo='.$orderNo.'&amount='.($recharge['money']	 * 100).'&';
		redirect($url, 0, '页面跳转中...');
		exit;
	}
	/**
	 * 	我的电子券
	 *	zhenHong~
	 */
	public function coupon()
	{
		$map['E.userID'] = session('user');
		$list = D::get(['CouponExchange','E'],[
			'where' => $map,
			'join'  => 'LEFT JOIN __COUPON__ C ON C.id = E.cID',
			'field' => 'E.*,C.exprie_start,exprie_end,C.money'
		]);
		$this->assign('list',$list);
		$this->display();
	}
	/*
	 *
	 * 	转赠电子券 页面
	 * */
	public function couponGive(){
		$map = [
			'E.userID' => session('user'),
			'E.status' => 1
		];
		$myCoupon = D::get(['CouponExchange','E'],[
			'where' => $map,
			'join' => 'INNER JOIN __COUPON__ C ON C.id = E.cID',
			'field' => 'E.*,C.title,C.money'
		]);
		$myCoupon = array_map(function($data){
			$data['coupon'] = $data['title'].'-'.$data['money'].'元';
			return $data;
		},$myCoupon);
		$this->assign('list',$myCoupon);
		$this->display();
	}
	/*
	 *  转赠电子券处理
	 * 		更新当用用户电子券 拥有状态
	 * 		插入转赠用户电子券的拥有记录
	 * 		插入转赠记录
	 * */
	public function giveCheck(){
		$post  = I('post.');
		$map = [
			'mobile' => $post['mobile'],
			'status' => 1
		];
		$accept = D::find('Users',[
			'where' => $map,
			'field' => 'id'
		]);
		if(!$accept){
			$this->error('您要转赠的用户不存在,或被删除');
		}
		if($accept['id'] == session('user')){
			$this->error('不能转赠给自己');
		}
		if(!array_key_exists('card',$post) || !$post['card']){
			$this->error('您还未选择或未拥有电子券');
		}
		$my = D::find('CouponExchange',[
			'where' => "card='{$post['card']}'"
		]);
		M('CouponExchange')->where("card=".$post['card'])->setField(['status'=>3,'updateTime'=>time()]);
		$give = [
			'cID' => $my['card'],
			'sendID' => session('user'),
			'acceptID' => $accept['id'],
			'sendTime' => time(),
		];
		M('CouponGive')->add($give);
		$exchange = [
			'cID' => $my['cID'],
			'createTime' => time(),
			'updateTime' => time(),
			'userID' => $accept['id'],
			'card' => $my['card'],
			'type' => 'accept',
			'status' => 1
		];
		M('CouponExchange')->add($exchange);
		$this->success('转赠成功',U('Self/coupon'));
	}
	/**
	 * [couponExchange description]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-09
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function couponExchange()
	{
		$db = D::get('Coupon',[
			'alias' => 'G',
			'where' => ['G.status' => '1'],
			'order' => 'G.exprie_end ASC',
			'join'  => 'LEFT JOIN __FILES__ F ON F.id = G.pic',
			'field' => "G.id,G.title,G.sorce,CONCAT('/Uploads',F.savepath,F.savename) `icon`"
		]);
		$this->assign('db',$db);
		$this->display();
	}
	//积分明细
	public function integral(){
		if(IS_AJAX){
			parent::index(function($data){
				$data['createTime'] = date('Y-m-d',$data['createTime']);
				$data['type_name'] = getTypes($data['type']);
				return $data;
			});
		}
		$this->display();
	}
	/**
	 * [couponEdit 积分兑换详情查看页面]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-09
	 * @Function []
	 * @param    [type]     $id     [description]
	 * @return   [type]             [description]
	 */
	public function couponEdit($id)
	{
		$db = D::find('Coupon',$id);
		$db['houseCate'] = D::get('HouseCate',['id' => ['in',$db['hcate']]]);
		$db['packageCate'] = D::get('HouseCate',['id' => ['in',$db['tcate']]]);
		$db['level'] = D::get('Grades',['id' => ['in',$db['userLevel']]]);
		if($db['exprie_end'] < date('Y-m-d')){
			$db['can']  = 'no';
		}else{
			$db['can']  = 'yes';
		}
		$this->assign('db',$db);
		$this->display();
	}
	/*
	 *
	 * 	优惠券兑换逻辑
	 * 		1、判断个人积分
	 * 		2、判断库存
	 * 		3、减库存
	 * 		4、插入兑换记录
	 * 		5、减积分
	 *  zhenHong~
	 * */
	public function couponCheck()
	{
		$post  = I('post.');
		$ex = D('CouponExchange');
		$bool = true;
		if($post['mysorce']<$post['sorce']){
			$bool = false;
			$this->error('您当前账号积分不足,无法兑换');
		}
		$kucun = D::field('Coupon.num',$post['cID']);
		if($kucun<1){
			$bool = false;
			$this->error('当前电子券库存不足,无法兑换');
		}
		$level = D::field('Users.nowLevel',session('user'));
		$allow = D::field('Coupon.userLevel',$post['cID']);
		if(!in_array($level,explode(',',$allow))){
			$bool = false;
			$this->error('您当前会员等级不够，无法兑换');
		}
		if($bool === true){
			$data = $ex->create();
			$data['userID'] = session('user');
			$ex->add($data);
			D::dec('Coupon.num',$data['cID'],1);
			$add_data = [
				'userID' =>session('user'),
				'type' => 'exchange',
				'sorce' => D::field('Coupon.sorce',$data['cID']),
				'method' =>'sub',
				'createTime' => time()
			];
			M('UserSorce')->add($add_data);
			$this->success('兑换成功,可到我的电子券中查看',U('Self/couponExchange'));
		}

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
	/*
	 * 	购买积分升级卡  逻辑
	 * 		插入积分明细记录
	 * 		更新user表nowLevel字段
	 * 		插入升级记录
	 * */
	public function upGradeBuy(){
		$get = I('get.');
		$uid = session('user');
		$user = D::find(['Users','U'],[
			'join' => 'LEFT JOIN __GRADES__ G ON G.id = U.nowLevel',
			'field' => 'U.nowLevel,G.sort'
		]);

		$grade = D::find('Grades',$get['id']);
		if($get['my']<$get['grade']){
			$this->error('积分不足,无法购买会员升级卡');
		}elseif($user['nowLevel'] == $get['id']){
			$this->error('您当前等级已是此等级无法购买');
		}elseif($grade['sort'] < $user['sort']){
			$this->error('您不能购买等级比您低的会员升级卡');
		}else{
			//插入积分明细记录
			$sorce = [
				'userID' => $uid,
				'type' => 'lvup',
				'sorce' => $get['grade'],
				'method' => 'sub',
				'createTime' => time()
			];
			M('UserSorce')->add($sorce);
			//插入升级记录
			$user = D::find('Users',$uid);
			$level = [
				'userID' => $uid,
				'before' => $user['nowLevel'],
				'after'  => $get['id'],
				'createTime' => time(),
				'achs' => $get['grade'],
				'admin' => 0
			];
			M('UserLvup')->add($level);
			//更新user表nowLevel字段
			D::set('Users.nowLevel',$uid,$get['id']);
			$this->success("购买成功,现在您的级别已经成为{$grade['title']}",U('Self/index'));
		}

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
		if (IS_AJAX) {
			$look = D::find('NewsUser',['userId' => session('user')]);
			$looks = $look['newsIds']?explode(',', $look['newsIds']):[];
			parent::index(function($data)use($looks){
				if (!in_array($data['id'], $looks)) {
					$data['look'] = 'false';
				}
				$data['createTime'] = date('Y-m-d',$data['createTime']);
				return $data;
			});
		}else{
			$this->display();
		}
	}
	/**
	 * [messageEdit 系统消息-详情]
	 * @Author   ヽ(•ω•。)ノ   Mr.Solo
	 * @DateTime 2018-01-05
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function messageEdit($id)
	{
		/*首先查询到该消息的ID*/
		$data = D::find('News',$id);
		switch ($data['type']) {
			case 'sys':
				$field = 'newsIds';
				break;
			case 'act':
				$field = 'activitysIds';
				break;
		}
		$look = D::find('NewsUser',['userId' => session('user')]);  // 查询该用户的阅读情况
		if ($look) {
			//如果该用户有数据
			$looks = $look[$field]?explode(',', $look[$field]):[];
		}else{
			//如果没有数据 先新增数据
			$look['id'] = D::add('NewsUser',[
				'userId'       => session('user'),
				'newsIds'      => '',
				'activitysIds' => '',
			]);
			$looks = [];
		}
		if (!in_array($id, $looks)) {
			//如果不存在数组中 则更新数据信息
			$looks[] = $id;
			D::save('NewsUser',$look['id'],[
				$field => implode(',', $looks)
			]);
		}
		$this->assign('db',$data);
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
		if (IS_AJAX) {
			$look = D::find('NewsUser',['userId' => session('user')]);
			$looks = $look['activitysIds']?explode(',', $look['activitysIds']):[];
			parent::index(function($data)use($looks){
				if (!in_array($data['id'], $looks)) {
					$data['look'] = 'false';
				}
				$data['createTime'] = date('Y-m-d',$data['createTime']);
				return $data;
			});
		}else{
			$this->display();
		}
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
			$this->assign('banners',getBanner('m'));
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