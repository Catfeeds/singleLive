<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;

//订单管理模块
class OrderListController extends CommonController
{
    public $model = 'Order';
    public function _map(&$data)
    {
        //这里注意7为已驳回的订单  因为客户确实付了款了,驳回了肯定是要让他入住的
        switch (ACTION_NAME){
            case 'index':
                if ( I('title') ) {
                    $map['CONCAT(orderNo,username,mobile)'] = ['like','%'.I('title').'%'];
                }
                if ( I('start') || I('end') ) {
                    $map['createTime'] = get_selectTime(I('start'),I('end'));
                }
                if(I('status')){
                    $map['status'] = I('status');
                }else{
                    $map['status'] = array('in',"1,2,3,4,7,8,9");
                }
                $data = [
                    'where' => $map,
                    'order' => 'createTime DESC',
                ];
                break;
            case 'export':
                if ( I('title') ) {
                    $map['CONCAT(orderNo,username,mobile)'] = ['like','%'.I('title').'%'];
                }
                if ( I('start') || I('end') ) {
                    $map['createTime'] = get_selectTime(I('start'),I('end'));
                }
                if(I('status')){
                    $map['status'] = I('status');
                }else{
                    $map['status'] = array('in',"1,2,3,4,7,8,9");
                }
                $data = [
                    'where' => $map,
                    'order' => 'createTime DESC',
                ];
                break;
        }
    }
    public function index()
    {
        $db = parent::index(false);
        $db['db'] = $this->checkData($db['db']);
        $this->assign('db',$db['db']);
        $this->assign('page',$db['page']);
        $this->display();
    }
    public function export(){
        $db = parent::index(false);
        $list = $this->checkData($db['db']);
        $xlsName  = date('Y-m-d_H:i:s',time()).'订单列表';
        $xlsCell  = array(
            array('createTime','下单时间'),
            array('orderNo','订单编号'),
            array('houseName','房间名称'),
            array('username','客户姓名'),
            array('mobile','客户电话'),
            array('person','成人(个数)'),
            array('child','儿童(个数)'),
            array('date_show','日期区段'),
            array('type_name','订单类型'),
            array('price','订单金额'),
            array('status_name','状态'),
        );
        export_Excel($xlsName,$xlsCell,$list);
    }
    /*
     *  数据集处理
     *  $db--二维数组
     * */
    public function checkData($db){
        return array_map(function($data){
            $data['type_name'] = $data['type'] == 'k' ? '客房' : '套餐';
            if($data['type'] == 'k'){
                $data['houseName'] = D::field('House.name',$data['roomID']);
                $data['date_show'] = $data['inTime'].' ~ '.$data['outTime'];
            }else{
                $data['houseName'] = D::field('Package.title',$data['roomID']);
                $data['date_show'] = $data['inTime'];
            }
            $data['createTime'] = date_out($data['createTime']);
            $data['status_name'] = getTypes($data['status']);
            return $data;
        },$db);
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
        $this->success('操作成功',U('OrderList/index'));
    }
    //离开
    public function leave(){
        $id = I('id');
        M('Order')->where("id=".$id)->setField('status','2');
        $msg = D::find('Order',$id);
        if($msg['type'] == 'k'){
            $arr = push_select_time($msg['inTime'],$msg['outTime']);
            $map['createDate'] = array('in',$arr);
            $map['roomID'] = $msg['roomID'];
            D::dec('RoomDate.order_num',['where'=>$map],1);
        }else{
            $map['createDate'] = $msg['inTime'];
            $map['roomID'] = $msg['roomID'];
            D::dec('RoomDate.order_num',['where'=>$map],$msg['num']);
        }
        $this->success('操作成功',U('OrderList/index'));
    }
}
