<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;

//退款订单
class BackMoneyListController extends CommonController
{
    public $model = 'Order';
    public function _map(&$data)
    {
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
                    $map['status'] = array('in',"5,6,7");
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
                    $map['status'] = array('in',"5,6,7");
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
            array('payType','支付方式'),
            array('status_name','状态'),
        );
        export_Excel($xlsName,$xlsCell,$list);
    }
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
            $data['payType'] = getTypes($data['payType']);
            return $data;
        },$db);
    }

    //查看详情  目前没用
    public function look()
    {
        $this->display();
    }
    /*
     *  确认退款
     *      更新order表字段
     *      插入财务明细表
     *      插入退款表
     *      增加房间数量 && 客房  ？存在多天 ？则所有天数都要减1 :否则只减所选的天数
     *      判断此订单是否用了优惠券  ？ 返优惠券(更新电子券拥有表状态，删除电子券使用记录) : 不做操作
     *      判断支付方式 余额支付 ？  插入余额表记录  :  线下退款
     *      减去已返积分
     * */
    public function pass(){
        $id = I('id');
        $uid = I('uid');
        do_order_back($id);
        event_user_level($uid);
        $this->success('操作成功',U('BackMoneyList/index'));
    }
    //退款申请驳回
    public function down(){
        $id = I('id');
        M('Order')->where("id=".$id)->setField('status','7');
        $this->success('操作成功',U('BackMoneyList/index'));
    }
}
