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
            array('payType','支付方式'),
            array('status_name','状态'),
        );
        export_Excel($xlsName,$xlsCell,$list);
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
        if($db['coupon']){
            $coupon = D::find(['CouponUsed','CU'],[
                'where'=>['CU.orderNo'=>$db['orderNo']],
                'join' =>[
                    'LEFT JOIN __COUPON_EXCHANGE__ CE ON CE.card = CU.cID',
                    'LEFT JOIN __COUPON__ C ON C.id = CE.cID'
                ],
                'field'=>'CU.cID,C.*'
            ]);
            $coupon['notDate_show'] = $coupon['notDate'] ? explode("\r\n",$coupon['notDate']) : '' ;
        }
        $myDate = get_minDate_maxDate();
        $this->assign('coupon',$coupon);
        $this->assign('myDate',$myDate);
        $this->assign('db',$db);
        $this->display();
    }
    //变更套餐日期
    public function changePackage(){
        $orderID = I('id');
        $db = D::find('Order',$orderID);
        $db['houseName'] = D::field('Package.title',$db['roomID']);
        if($db['coupon']){
            $map = [
                'CU.orderNo'=>$db['orderNo'],
                'CU.status' =>1
            ];
            $coupon = D::find(['CouponUsed','CU'],[
                'where'=>$map,
                'join' =>[
                    'LEFT JOIN __COUPON_EXCHANGE__ CE ON CE.card = CU.cID',
                    'LEFT JOIN __COUPON__ C ON C.id = CE.cID'
                ],
                'field'=>'CU.cID,C.*'
            ]);
            $coupon['notDate_show'] = $coupon['notDate'] ? explode("\r\n",$coupon['notDate']) : '' ;
        }
        $package = D::find('Package',$db['roomID']);
        if(date('Y-m-d') > $package['allowIn']){
            $date = date('Y-m-d');
        }else{
            $date = $package['allowIn'];
        }
        $myDate = [
            'min' => $date,
            'max' => $package['allowOut']
        ];
        $this->assign('myDate',$myDate);
        $this->assign('coupon',$coupon);
        $this->assign('db',$db);
        $this->display();
    }
    /*
     *  变更客房日期处理
     *  这里需要判断3中清况
     *      1、满房
     *      2、是否使用了优惠券  使用了 ？ 判断所选日期内是否存在优惠券不可用或已过期（存在不能改） : 跳过
     *      3、价格不一致也不能改
     *      4、若所选时间和原订单时间都一致也不能改
     * */
    public function change_order_date(){
        $post = I('post.');
        $order = D::find('Order',$post['id']);
        $is_like = true;
        if($post['type'] == 'k'){
            $parameter = push_select_time($post['inTime'],$post['outTime']);
            if($order['inTime'] == $post['inTime'] && $order['inTime'] == $post['outTime']){
                $is_like = false;
            }
        }else{
            $parameter = $post['inTime'];
            if($order['inTime'] == $post['inTime']){
                $is_like = false;
            }
        }
        if($is_like !== true){
            $this->error('您所选日期与原订单一致,无法更改');
        }
        $array = ['roomID'=>$post['roomID'],'type'=>$post['type']];
        $bool = is_house_all($parameter,$array);
        if($bool !== true){
           $this->error('您所选日期内,存在满房情况无法预订');
        }
       //判断该订单是否使用了优惠券
       if(array_key_exists('coupon',$post) && $post['coupon']){
           $coupon = D::find('coupon',$post['cID']);
           $arr = explode("\r\n",$coupon['notDate']);
           //这里必须要判断   优惠券设置的特定不可用日期
           if($post['type'] == 'k'){
               $boolen = true;
               foreach ($arr as $val){
                   if($val>=$post['inTime'] && $val<=$post['outTime']){
                       $boolen = false;
                   }
               }
               if($post['inTime']<$coupon['exprie_start'] || $post['outTime']<$coupon['exprie_start'] || $post['inTime']>$coupon['exprie_end'] || $post['outTime']>$coupon['exprie_end'] || $boolen!==true){
                   $this->error('您选择的日期内,存在优惠券的不可用日期');
               }
           }else{
               $boo = true;
               if(in_array($post['inTime'],$arr)){
                   $boo = false;
               }
               if($post['inTime']<$coupon['exprie_start'] || $post['inTime']>$coupon['exprie_end'] || $boo!==true){
                   $this->error('您选择的日期内,存在优惠券的不可用日期');
               }
           }
       }
        //判断提交的订单价格
       $price = get_order_price($post);
       if($price!=$post['price']){
           $this->error('您所选择的日期,与原订单价格不符无法更改');
       }
       //将原订单所选日期减去 在添加新的更改日期
        $roomDate = search_room_date($order['roomID'],$order['type']);
       if($post['type'] == 'k'){
           //减去
           $sub_before_date = date('Y-m-d',strtotime("{$order['outTime']} -1 day"));
           $arr = push_select_time($order['inTime'],$sub_before_date);
           $where['createDate'] = array('in',$arr);
           $where['roomID'] = $order['roomID'];
           $where['type'] = $order['type'];
           M('RoomDate')->where($where)->setDec('order_num',1);
           //更新订单时间
           $save_data = [
               'inTime' => $post['inTime'],
               'outTime' => $post['outTime'],
           ];
           M('Order')->where("id=".$post['id'])->save($save_data);
           //新增 修改后的时间
           $add_before_date = date('Y-m-d',strtotime("{$post['outTime']} -1 day"));
           $arrNew =  push_select_time($post['inTime'],$add_before_date);
           foreach($arrNew as $key => $val){
               if(in_array($val,$roomDate)){
                   $save_date[] = $val;
               }else{
                   $add_date[$key]['createDate'] = $val;
               }
           }
           //已经存在日期,则更新
           if($save_date){
               $save['createDate'] = implode(',',$save_date);
               $save['type'] = 'k';
               $save['roomID'] = $order['roomID'];
               M('RoomDate')->where($save)->setInc('order_num',1);
           }
           //不存在的日期,则新增
           if($add_date){
               $add_date = array_map(function($data)use($order){
                   $data['roomID'] = $order['roomID'];
                   $data['order_num'] = 1;
                   $data['type'] = 'k';
                   return $data;
               },$add_date);
               M('RoomDate')->addAll($add_date);
           }
       }else{
           //减去
           $aa = [
               'createDate' => $order['inTime'],
               'type' => 't',
               'roomID' => $order['roomID']
           ];
           M('RoomDate')->where($aa)->setDec('order_num',$order['num']);
           //更新
           $save_data['inTime'] = $post['inTime'];
           M('Order')->where("id=".$post['id'])->save($save_data);
           //新增提交的时间
           if(in_array($post['inTime'],$roomDate)){
               $save = [
                   'createDate' => $post['inTime'],
                   'type' => 't',
                   'roomID'=> $order['roomID']
               ];
               M('RoomDate')->where($save)->setInc('order_num',$order['num']);
           }else{
               $add = [
                   'createDate' => $post['inTime'],
                   'order_num' => $order['num'],
                   'type' => 't',
                   'roomID' => $order['roomID']
               ];
               M('RoomDate')->add($add);
           }
       }
       $this->success('变更日期订单日期成功,请记得通知用户！！！',U('OrderList/index'));
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
