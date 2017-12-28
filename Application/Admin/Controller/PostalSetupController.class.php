<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;
//提现管理模块
class PostalSetupController extends CommonController {
	  public $model = 'postaldate';
     public function index()
     {

         $dates = D::get('postaldate');

         $data = "";
         foreach ($dates as $key => $date) {

            if($key!=count($dates)-1){
               $data .= $date['date'].',';
            }else{
               $data .= $date['date'];
            }
         }

         $this->assign('data',$data);
         $this->display();
     }
    //提现设置执行
   	public function setUpDo()
   	{
   		$lines = explode(',', I('date'));

   		foreach($lines as $key => $value){
   			if(!preg_match('/^[1-9]\d*$/',$value)){
   				$this->error('输入的日期有误，请重新输入！');
   			}else if((int)($value)>31){
   				$this->error('输入的日期有误，请重新输入！');
   			}else if(!$value){
   				unset($lines[$key]);
   			}
   		}

   		D::delete('postaldate','1=1');

   		foreach ($lines as $date) {

   			$Ary['date'] = $date;

   			D::add('postaldate',$Ary);
   		}

   		$this->success('设置成功！',U('PostalSetup/index'));
   	}
    
}
