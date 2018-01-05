<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;

//订单管理模块
class OrderListController extends CommonController
{
    public function _map(&$data)
    {
        if ( I('title') ) {
            $map['CONCAT(O.orderNo,O.username,U.mobile)'] = ['like','%'.I('title').'%'];
        }
        if ( I('start') || I('end') ) {
            $map['O.createTime'] = get_selectTime(I('start'),I('end'));
        }
        if(I('status')){
            $map['O.status'] = I('status');
        }else{
            $map['O.status'] = array('in',"1,2,3,4,8,9");
        }
        /*
         *  因为order表既存在客房的订单又存在套餐的订单---roomID
         *  故而两个表都要链接 必须是全匹配
         * */
        $sql = D::get(['Order','O'],[
            'field' => 'O.*,H.name houseName,P.title packName',
            'join'  => [
                'LEFT JOIN __HOUSE__ H ON H.id = O.roomID',
                'LEFT JOIN __PACKAGE__ P ON P.id = O.roomID'
            ],
            'order' => 'O.createTime DESC',
        ],false);

        $data = [
            'table' => '('.$sql.') M',
            'where' => $map
        ];

    }
    public function index()
    {
        $info = http_build_query(I('get.'));
        $db = parent::index(false);
        $db = array_map(function($data){
            $data['type'] = $data['type'] == 'k' ? '客房' : '套餐';
            if(!empty($data['houseName'])){
                $data['house'] = $data['houseName'];
            }else{
                $data['house'] = $data['packName'];
            }
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
        $this->assign('page',$db['page']);
        $this->display();
    }
    public function export(){
        $db = parent::index(false);
        $list = array_map(function($data){
            $data['type'] = $data['type'] == 'k' ? '客房' : '套餐';
            $data['createTime'] = date('Y-m-d H:i:s',$data['createTime']);
            if(!empty($data['houseName'])){
                $data['house'] = $data['houseName'];
            }else{
                $data['house'] = $data['packName'];
            }
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
                case '8':
                    $data['status'] = '待付款';
                    break;
                case '9':
                    $data['status'] = '已入住';
                    break;
            }
            return $data;
        },$db['db']);
        $xlsName  = date('Y-m-d_H:i:s',time()).'订单列表';
        $xlsCell  = array(
            array('createTime','日期'),
            array('orderNo','订单编号'),
            array('house','房间名称'),
            array('username','客户姓名'),
            array('mobile','客户电话'),
            array('person','成人(个数)'),
            array('child','儿童(个数)'),
            array('inTime','入住日期'),
            array('outTime','离开日期'),
            array('use','订单金额'),
            array('type','订单类型'),
            array('price','订单金额'),
            array('status','状态'),
        );
        export_Excel($xlsName,$xlsCell,$list);
    }

    /*
     *  查看订单详情
     *      现在不加此功能，因为没有用
     * */
    public function look()
    {
        $this->display();
    }
    //确认入住
    public function sure(){
        $id = I('id');
        M('Order')->where("id=".$id)->setField('status','9');
        $this->success('退款成功',U('OrderList/index'));
    }
    //离开
    public function leave(){
        $id = I('id');
        M('Order')->where("id=".$id)->setField('status','2');
        $this->success('退款成功',U('OrderList/index'));
    }
}
