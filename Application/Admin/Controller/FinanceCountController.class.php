<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;

/*
   订单统计
*/
class FinanceCountController extends CommonController
{
    public $model = 'Order';
    public function _map(&$data)
    {
        if ( I('title') ) {
            $map['orderNo'] = ['like','%'.I('title').'%'];
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
        $this->assign('info',$info);
        $this->assign('db',$db['db']);
        $this->assign('db',$db['page']);
        $this->display();
    }
    //导出
    public function export(){
        $db = parent::index(true);
        $xlsName  = date('Y-m-d_H:i:s',time()).'客户统计';
        $xlsCell  = array(
            array('createTime','日期'),
            array('orderNo','订单号'),
            array('username','姓名'),
            array('mobile','电话'),
            array('price','金额'),
            array('status','状态')
        );
        export_Excel($xlsName,$xlsCell,$db);
    }

}
