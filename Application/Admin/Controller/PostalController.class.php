<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;
//提现管理模块
class PostalController extends CommonController {
    public $model = ['postal','P'];
    public function _map(&$data){

        $map["_string"] = 'P.status = 0';
        if(I('startTime')){
            $map["_string"] .= ' And applydate >= '.strtotime(I('startTime')).'';
        }
        if(I('endTime')){
            $map["_string"] .= ' And applydate <= '.strtotime(I('endTime')).'';          
        }
        if(I('title')){
            $where["hotelName"] = ['like','%'.I('title').'%'];
            $map['_complex'] = $where;
        }

        $data = [
            'where'  => $map,
            'join'  => 'zc_hotels H on P.hotel=H.id',
            'order' => 'applydate',
            'field' => 'P.*,H.hotelName'
        ];
    }
    //提现处理
    public function index()
    {
        $db = parent::index(function($data){
            $data['applydate'] = date('Y-m-d',$data['applydate']);
            return $data;
        });
    }
    //查看提现处理详情
    public function index_edit()
    {
        //获取需要对照的平台订单信息
        $orderID = D::get(['Postalrecord','P'],[
                        'where' => 'P.postal='.I('id').'',
                        'join'  => [
                            'LEFT JOIN __POSTAL_WAIT__ PW on P.`order`=PW.id',
                            'LEFT JOIN __ORDER_MONEY__ OM on PW.`order`=OM.id',
                            'LEFT JOIN __ORDER__ O on OM.`orderId`=O.id',
                            'LEFT JOIN __USERS__ U on U.id=O.userId',
                        ],
                        'field' => 'O.*,PW.amount Pamount,U.realname userName'
                    ]);
        $orderHotel = $orderID;
        // foreach ($orderID as $key => $ID) {
        //    $orderHotel[] = D::find(['order_hotel','O'],[
        //                         'where' => 'id='.$ID['orderhotel_id'].'',
        //                     ]);
        // }

        $row = D::find('postal',I('id')); 

        $this->assign('row',$row);
        $this->assign('OH',$orderHotel);
        $this->assign('OD',$orderID);
        $this->display();     

    }
    //未处理记录导出
    public function export()
    {
        $db = parent::index(true);

        foreach ($db as $key => $data) {
            $db[$key]['applydate'] = date('Y-m-d',$data['applydate']);  
        }

        $dbName = array(
                array('hotelName','酒店名称'),
                array('applydate','申请日期'),
                array('count','订单总数'),
                array('monery','提现金额'),
                array('info','提现说明')
        );

        $excelName = date('Ymd').'-未处理提现记录';

        export_Excel($excelName,$dbName,$db);
    }
    //提现处理详情导出
    public function index_edit_export(){
        // echo 111111;
        // die;
        //获取需要对照的平台订单信息
        $orderID = D::get(['Postalrecord','P'],[
                        'where' => 'P.postal='.I('id').'',
                        'join'  => [
                            'LEFT JOIN __POSTAL_WAIT__ PW on P.`order`=PW.id',
                            'LEFT JOIN __ORDER_MONEY__ OM on PW.`order`=OM.id',
                            'LEFT JOIN __ORDER__ O on PW.`order`=O.id',
                            'LEFT JOIN __USERS__ U on U.id=O.userId',
                        ],
                        'field' => 'O.*,PW.amount Pamount,U.realname userName'
                    ]);
        // $orderHotel = [];
        // foreach ($orderID as $key => $ID) {
        //    $orderHotel[] = D::find(['order_hotel','O'],[
        //                         'where' => 'id='.$ID['orderhotel_id'].'',
        //                     ]);
        // }
        $orderID = array_map(function($data){

            $data['createTime'] = date('Y-m-d',$data['createTime']);
            // $data['orderhotel_used'] = $data['orderhotel_used'].'小时';
            $data['Pamount'] = sprintf('%.2f',$data['Pamount']);

            return $data;
        },$orderID);
        $db = [
            'hotel' => $orderHotel,
            'admin' => $orderID,
        ];
 
        $adminDbName = array(
                array('orderhotel_no','订单编号'),
                array('userName','姓名'),
                // array('orderhotel_used','入住时长'),
                array('Pamount','消费金额'),
        );
        $adminExcelName = date('Y-m-d_His').'后台提现记录详情';
 
        export_Excel($adminExcelName,$adminDbName,$orderID);

        // $hotelDbName = array(
        //         array('no','订单编号'),
        //         array('startTime','入住时间'),
        //         array('used','入住时长'),
        // );
        // $hotelExcelName = date('Y-m-d_His').'酒店提现记录详情';

        // export_Excel($hotelExcelName,$hotelDbName,$orderHotel);   
   
    }
    //同意提现申请
    public function agree()
    {
        $Ary = [
            'status' => 2,
        ];
        $postalId = D::get(['postalrecord','P'],'postal='.I('id').'');

        foreach ($postalId as $key => $postal) {
            D::set('PostalWait.status',$postal['order'],1);
        }
        $Ary['status'] = 1;
        $Ary['passdate'] = time(); 
        $Ary['actualmonery'] = D::field('postal.monery',I('id')); //提现通过后更新实际提现金额 
        if(D::save('postal',I('id'),$Ary)){
            $this->success('已通过',U('Postal/index'));
        }else{
            $this->error('网络错误，请刷新后重试!');
        }
    }
    //提现申请退回
    public function back()
    {
        $this->display();
    } 
    //提现申请退回执行
    public function backDo()
    {

        $Ary = [
            'status' => 0,
        ];
        $postalId = D::get(['postalrecord','P'],'postal='.I('id').'');
        foreach ($postalId as $key => $postal) {
            D::save('orderflow','id='.$postal['order'].'',$Ary);         
        }

        $Ary['status'] = 2;
        $Ary['passdate'] = time(); 
        $Ary['return'] = I('return'); 

        if(D::save('postal',I('id'),$Ary)){
            $this->success('已退回申请',U('Postal/index'));
        }else{
            $this->error('网络错误，请刷新后重试!');
        };     
    }
}   
