<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;
//财务统计
class MoneryController extends CommonController {
	//财务流水
 //   public $model = ['Postal','Postal'];
    public function _map(&$data)
    {
        if(I('startTime')||I('endTime')){
           $map['Y.dateTime'] = get_selectTime( I('startTime'),I('endTime') );
        }
        if (I('title')) {
            $map['Y.hotelName'] = ['like','%'.I('title').'%'];
        }

        $Sql[] = D::get(['OrderMoney','OM'],[
            'join' => [
            	'LEFT JOIN __ORDER__ O on OM.orderId=O.`id`',
            	'LEFT JOIN __HOTEL_ROOMS__ HR on HR.id = O.room',
            	'LEFT JOIN __HOTELS__ H on H.id = HR.hotel'
            ],
            'field' => "OM.money as `change`,O.id,O.createTime dateTime,H.hotelName,'会员充值' as `type`",
            ],false);
        $Sql[] = D::get(['Drawback','O'],[
            'join' => [
            	'LEFT JOIN __ORDER__ `OR` on `OR`.id = O.orderId',
            	'LEFT JOIN __HOTELS__ H on H.id = `OR`.hotel'          
            ],
            'field' => "-money as `change`,O.id,O.createTime dateTime,H.hotelName,'退款' as `type`",
            ],false);
        $Sql[] = D::get(['Postal','P'],[
            'where' => 'P.status=1',
            'join' => 'LEFT JOIN __HOTELS__ H on H.id=P.hotel',
            'field' => "-monery as `change`,P.id,passdate dateTime,H.hotelName,'酒店提现' as `type`",
            ],false);
        $listSql = implode(' UNION ALL ',$Sql);
        $lastSql =D::get('',[
            'table' => '('.$listSql.') X',
            'order' => 'X.dateTime',
            'field' => 'X.*,IFNULL((select SUM(`change`) from ('.$listSql.')  Z where (Z.`dateTime` <= X.`dateTime` AND Z.`id` < X.`id`) OR ( Z.`dateTime` < X.`dateTime` ) ),0) `amount`'
            ],false);
// 
        $data = [
            'where' => $map,
        	'table' => $lastSql.' Y',
            'order' => 'Y.dateTime DESC',
        	'field' => 'Y.*,(`amount`+`change`) `current`'
        ];
    }
    //导出财务流水记录
    public function export()
    {   
        $db = parent::index(true);
        foreach ($db as $key => $data) {
           $db[$key]['dateTime'] = date('Y-m-d',$data['dateTime']);
           $db[$key]['amount'] = sprintf("%.2f", $data['amount']); 
           $db[$key]['change'] = sprintf("%.2f", $data['change']); 
           $db[$key]['current'] = sprintf("%.2f", $data['current']); 
        }

        $dbName = array(
            array('dateTime','日期'),
            array('hotelName','酒店名称'),
            array('amount','变更前金额'),
            array('change','变更金额'),
            array('current','变更后金额'),
            array('type','变更类型'),
            );
        $excelName = "财务流水记录_".date('Ymd');

        export_Excel($excelName,$dbName,$db);
    }
}
