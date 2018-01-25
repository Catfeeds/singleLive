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
    /*
     *  取消订单
     *      若是已驳回状态，取消订单走退款流程
     *      若是待付款状态,直接变更状态
     * */
    public function outOrder(){
        $get  = I('get.');
        if($get['status'] == '7'){
            do_order_back($get['id']);
            event_user_level($get['id']);
        }else{
            D::set('Order.status',$get['id'],4);
        }
        $this->success('操作成功');
    }
    //导出
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
            array('payType','订单金额'),
            array('status_name','状态'),
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
    //变更客房日期
    public function changeHouse(){
        $orderID = I('id');
        $db = D::find('Order',$orderID);
        $db['houseName'] = D::field('House.name',$db['roomID']);
        $myDate = get_minDate_maxDate();
        $this->assign('myDate',$myDate);
        $this->assign('db',$db);
        $this->display();
    }
    //变更套餐日期
    public function changePackage(){
        $orderID = I('id');
        $db = D::find('Order',$orderID);
        $this->assign('db',$db);
        $this->display();
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
            if($data['type'] == 'k' && $data['status'] ==1){
                $data['url'] = U('OrderList/changeHouse?id='.$data['id']);
                $data['url_name'] = '变更客房日期';
            }else{
                $data['url'] = U('OrderList/changePackage?id='.$data['id']);
                $data['url_name'] = '变更套餐日期';
            }
            $data['createTime'] = date_out($data['createTime']);
            $data['status_name'] = getTypes($data['status']);
            $data['payType'] = getTypes($data['payType']);
            return $data;
        },$db);
    }
    /*
     * 获取时间日期格式信息
     * */
    public function getStrtotime()
    {
        $post = I('post.');
        $data = get_postDate_roomNum_coupon($post);
        $this->ajaxReturn($data);
    }
}
