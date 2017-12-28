<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;
//系统管理
class SystemSetUpController extends CommonController {
	public $model = 'config';
	public function index()
	{
		$db = parent::index(true);

		foreach ($db as $key => $value) {
			if($value['key']=='head'){
				$img = D::find('files','id='.$value['value'].'');
			} 
		}

		$this->assign('img',$img);
		$this->assign('db',$db);
		$this->display();
	}


    //系统设置执行
    public function setUpDo()
    {
    	D::delete('config','status=0');
    	
    	unset($_POST['width']);
    	unset($_POST['height']);

    	if(!I('head')){

    		$_POST['head'] = $_POST['realhead'];

    	}

        unset($_POST['realhead']);
        
    	foreach ($_POST as $key => $value) {
    		D::add('config',[
    				'key' 	=> $key,
    				'value' => $value,
    				'startTime' => '0'
    			]);
    	}
    	$this->success('设置成功！',U('SystemSetUp/index'));
    }
}
