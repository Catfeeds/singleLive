<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;

/*
   订单统计
*/
class OrderCountController extends CommonController
{
    public $model = 'Order';
    public function _map(&$data)
    {
        if ( I('title') ) {
            $map['CONCAT(orderNo,username,mobile)'] = ['like','%'.I('title').'%'];
        }
        if(I('start') || I('end')){
            $map['createTime'] = get_selectTime(I('start'),I('end'));
        }
        $data = [
            'where' => $map,
            'order' => 'createTime DESC',
        ];
    }
    //用户订单列表
    public function index()
    {   
        $info = http_build_query(I('get.'));
        $db = parent::index(false);
        $db['db'] = array_map(function($data){
            $data['status_name'] = getTypes($data['status']);
            return $data;
        },$db['db']);
        $this->assign('info',$info);
        $this->assign('db',$db['db']);
        $this->assign('page',$db['page']);
        $this->display();
    }
    //导出
    public function export(){
        $db = parent::index(true);
        $db = array_map(function($data){
            $data['status_name'] = getTypes($data['status']);
            return $data;
        },$db);
        $xlsName  = date('Y-m-d_H:i:s',time()).'客户统计';
        $xlsCell  = array(
            array('createTime','日期'),
            array('orderNo','订单号'),
            array('username','姓名'),
            array('mobile','电话'),
            array('price','金额'),
            array('status_name','状态')
        );
        export_Excel($xlsName,$xlsCell,$db);
    }

}
