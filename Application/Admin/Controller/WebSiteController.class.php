<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;
//网站设置
class WebSiteController extends CommonController {
	public function index()
	{
		$data = D('Config')->get_config();
		$this->assign('config',$data);
		$this->display();
	}

	public function set_config()
	{
		$post = I('post.');
		foreach ($post['config'] as $name => $value) {
			$updata[] = ['name'=>$name,'value'=>$value];
		}
		$updata_flag = array_map([D('Config'),'set_config'],$updata);
		if (!in_array(true,$updata_flag)) {
			$this->error('设置保存失败，请稍后重试');
		}else{
			$this->success('设置已保存');
		}
	}
}
