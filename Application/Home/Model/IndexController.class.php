<?php
namespace Home\Controller;
use Think\Controller;
use Think\Faster;
//欢迎页面
class IndexController extends CommonController {
	public function index()
	{
		$this->display();
	}
}