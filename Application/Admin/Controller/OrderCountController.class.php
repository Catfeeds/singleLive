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
        $db = array_map(function($data){
            switch($data['status']){
                case '1':
                    $data['status'] = '已支付';
                    break;
                case '2':
                    $data['status'] = '已完成';
                    break;
                case '3':
                    $data['status'] = '已超时';
                    break;
                case '4':
                    $data['status'] = '已取消';
                    break;
                case '5':
                    $data['status'] = '退款待审核';
                    break;
                case '6':
                    $data['status'] = '已退款';
                    break;
                case '7':
                    $data['status'] = '已驳回';
                    break;
                case '8':
                    $data['status'] = '待付款';
                    break;
                case '9':
                    $data['status'] = '已入住';
                    break;
            }
            return $data;
        },$db['db']);
        $this->assign('info',$info);
        $this->assign('db',$db['db']);
        $this->assign('db',$db['page']);
        $this->display();
    }
    //导出
    public function export(){
        $db = parent::index(true);
        $db = array_map(function($data){
            switch($data['status']){
                case '1':
                    $data['status'] = '已支付';
                    break;
                case '2':
                    $data['status'] = '已完成';
                    break;
                case '3':
                    $data['status'] = '已超时';
                    break;
                case '4':
                    $data['status'] = '已取消';
                    break;
                case '5':
                    $data['status'] = '退款待审核';
                    break;
                case '6':
                    $data['status'] = '已退款';
                    break;
                case '7':
                    $data['status'] = '已驳回';
                    break;
                case '8':
                    $data['status'] = '待付款';
                    break;
                case '9':
                    $data['status'] = '已入住';
                    break;
            }
            return $data;
        },$db);
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
