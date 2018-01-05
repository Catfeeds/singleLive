<?php
namespace Common\Model;
use Think\Model;
use Think\D;
class UsersModel extends Model {
	protected $_validate = [
		['realname','require','请填写用户姓名',0],
		['sex','require','请选择用户性别',0],
		['idCard','require','请填写证件号码',0],
		['mobile','require','请填写绑定手机号',0],
		['mobile','','手机号已经被注册',0,'unique',1],
		['mobile','/^(((13[0-9]{1})|(15[0-35-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/','手机号码不正确',0,'regex'],
		['verification','require','请填写短信验证码',0],
		['Email','require','请填写邮箱',0],
		['Email','/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/','邮箱格式不正确',0,'regex'],
		['password','require','请输入登录密码',0],
		['repassword','require','请重复输入登录密码',0],
		['repassword','password','登录密码两次输入不一致',0,'confirm'],
	];
	protected $_auto = [
		['createTime','time',1,'function'],
		['updateTime','time',2,'function'],
		['password','md5_pass',3,'callback'],
		['no_md5','get_pass1',1,'callback'],
	];
	/*
	  16-19 位卡号校验位采用 Luhm 校验方法计算：
	    1，将未带校验位的 15 位卡号从右依次编号 1 到 15，位于奇数位号上的数字乘以 2
	    2，将奇位乘积的个十位全部相加，再加上所有偶数位上的数字
	    3，将加法和加上校验位能被 10 整除。
	*/
	function luhm($no) {
		$arr_no = str_split($no);
		$last_n = $arr_no[count($arr_no)-1];
		krsort($arr_no);
		$i = 1;
		$total = 0;
		foreach ($arr_no as $n){
		    if($i%2==0){
		        $ix = $n*2;
		        if($ix>=10){
		            $nx = 1 + ($ix % 10);
		            $total += $nx;
		        }else{
		            $total += $ix;
		        }
		    }else{
		        $total += $n;
		    }
		    $i++;
		}
		$total -= $last_n;
		$total *= 9;
		return $last_n == ($total%10);
	}
	function checkgradeID($data)
	{
		if ($data) {
			return true;
		}else{
			return false;
		}
	}
	function checkValidate($data)
	{
		if ($data == S('validate')) {
			return true;
		}else{
			return false;
		}
	}
	function md5_pass($data)
	{
		if ($data) {
			return md5($data);
		}else{
			return false;
		}
	}
	function check_layerPlace($data)
	{
		if (in_array(I('layerPlace'), ['l','r'])) {
			return true;
		}else{
			return false;
		}
	}

	/**
	 * [set_layerNum description]
	 * @Author   尹新斌
	 * @DateTime 2017-06-12
	 * @Function [层级 等级 路径]
	 */
	function set_layerNum()
	{
		$Route = D::field('Users.layerRoute',['username'=>I('layerPID')]);
		return count(explode('-', $Route)) + 1;
	}
	function set_layerRoute()
	{
		$Route = D::find('Users',['where'=>['username'=>I('layerPID')],'field' => 'layerRoute,id']);
		return $Route['layerRoute'].'-'.$Route['id'];
	}
	function set_referNum()
	{
		$user = D::find('Users',[
			'where' => ['username' => I('referPID')],
			'field' => 'id,referRoute'
		]);
		if ( $user ) {
			return count(explode('-', $user['referRoute'])) + 1;
		}else{
			return '1';
		}
		// $Route = D::field('Users.referRoute',['username'=>I('referPID')]);
		// return count(explode('-', $Route)) + 1;
	}
	function set_referRoute()
	{
		$user = D::find('Users',[
			'where' => ['username' => I('referPID')],
			'field' => 'id,referRoute'
		]);
		if ( $user ) {
			return $user['referRoute'].'-'.$user['id'];
		}else{
			return '0';
		}

		// $Route = D::find('Users',['where'=>['username'=>I('referPID')],'field' => 'referRoute,id']);
		// return $Route['referRoute'].'-'.$Route['id'];
	}
	function set_placeRoute()
	{
		$Route = D::find('Users',['where'=>['username'=>I('layerPID')],'field' => 'placeRoute,id']);
		return $Route['placeRoute'].'-'.I('layerPlace');
	}
	function set_goods($data)
	{
		foreach ($data as $id => $num) {
			if ($num > 0) {
				$name = D::find('Formgoods',$id);
				$goods[] = $name['name'].'*'.$num;
			}
		}
		if ($goods) {
			$re = implode(';', $goods);
		}else{
			$re = '无';
		}
		return $re;
	}
	/**
	 * [set_userID description]
	 * @Author   尹新斌
	 * @DateTime 2017-06-12
	 * @Function [自动完成用户ID]
	 * @param    [type]     $data [description]
	 */
	function set_userID($data)
	{
		$user = D::find('Users',[
			'where' => ['username' => $data],
			'field' => 'id'
		]);
		if ( $user ) {
			return $user['id'];
		}else{
			return '0';
		}
	}
	function get_pass1()
	{
		return I('password');
	}
	function get_pass2()
	{
		return I('passcode');
	}
	/**
	 * [set_grade description]
	 * @Author   尹新斌
	 * @DateTime 2017-06-12
	 * @Function []
	 * @param    [type]     $data [description]
	 */
	function set_grade($data)
	{
		return I('gradeID');
	}
	/**
	 * [checkreferPID description]
	 * @Author   尹新斌
	 * @DateTime 2017-06-12
	 * @Function [检查报单中心]
	 * @return   [type]     [description]
	 */
	function checkreferPID($data)
	{
		if ($data) {
			$count = D::count('Users',['username' => $data]);
			if ($count > 0) {
				return true;
			}else{
				return false;
			}
		}else{
			return true;
		}
	}
	/**
	 * [checkreferPID description]
	 * @Author   尹新斌
	 * @DateTime 2017-06-12
	 * @Function []
	 * @param    [type]     $data [description]
	 * @return   [type]           [description]
	 */
	function checkregisterPID($data)
	{
		$count = D::count('Users',['username' => $data,'special' => 'y']);
		if ($count > 0) {
			return true;
		}else{
			return false;
		}
	}

	/**
	 * [checkgoods description]
	 * @Author   尹新斌
	 * @DateTime 2017-06-12
	 * @Function [验证选择商品信息]
	 * @param    [type]     $data [description]
	 * @return   [type]           [description]
	 */
	function checkgoods($data)
	{
		$amount = D::field('Grades.amount',I('gradeID'));
		foreach ($data as $id => $num) {
			if ($num > 0) {
				$amount = $amount - (D::field('Formgoods.amount',$id) * $num);
			}
		}
		if ($amount != 0 ) {
			return false;
		}else{
			return true;
		}
	}
	public function get_username_by_id($id)
	{
		return D::field('Users.username',$id);
	}
}