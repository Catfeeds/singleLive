<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;
//财务明细
class MoneryDetailedController extends CommonController {
    public $model = ['Postal','Postal'];
    public function _map(&$data)
    {
        if(I('startTime')||I('endTime')){
           $map['O.createTime'] = get_selectTime( I('startTime'),I('endTime') );
           $where['P.passdate'] = get_selectTime( I('startTime'),I('endTime') );
        }

        $where['status'] = ['eq','1'];
        $Sql[] = D::get(['OrderMoney','OM'],[
            'where' => $map,
            'join' => [
                'LEFT JOIN __ORDER__ O on OM.orderId=O.`id`',
                'LEFT JOIN __HOTEL_ROOMS__ HR on HR.id = O.room'            
            ],
            'field' => "OM.money as `in`,0 as `out`,FROM_UNIXTIME( O.createTime, '%Y-%m-%d' ) dateTime",
            ],false);

        $Sql[] = D::get(['Drawback','O'],[
            'where' => $map,
            'field' => "0 as `in`,money as `out`,FROM_UNIXTIME( createTime, '%Y-%m-%d' ) dateTime",
            ],false);
        $Sql[] = D::get(['Postal','P'],[
            'where' => $where,
            'field' => "0 as `in`,monery as `out`,FROM_UNIXTIME( passdate, '%Y-%m-%d' ) dateTime",
            ],false);
        $listSql = implode(' UNION ALL ',$Sql);

        $lastSql = D::get('Order',[
            'table' => '('.$listSql.') X',
            'field' => "sum(`in`) as inAll, sum(`out`) as outAll,dateTime",
            'group' => 'dateTime',
            ],false);

        $data = [
            'table' => $lastSql,
            'order' => 'dateTime DESC'
            ];

    }
    public function index()
    {
        parent::index(function($data){
            $data['inAll'] =   sprintf("%.2f", $data['inAll']);
            $data['outAll'] =  sprintf("%.2f", $data['outAll']);
            return $data;
        });
    }
    //财务明细查看
    public function detailed_edit()
    {
        //前台酒店名称条件搜索
        if(I('hotelName')){
           $map['H.hotelName'] = ['like','%'.I('hotelName').'%'];
           $where['H.hotelName'] = ['like','%'.I('hotelName').'%'];
           $Filter['H.hotelName'] = ['like','%'.I('hotelName').'%'];
        }
        //前台用户名称条件搜索
        if(I('realname')){
            $map['U.realname'] = ['like','%'.I('realname').'%'];
        }
        //前台收支类型条件搜索
        if(I('select')){
            $type['type'] = ['eq',I('select')];
        }
        //收入详情（Order表）
        $map['O.createTime'] = get_selectTime( I('date'),I('date') );

        $include = D::get(['OrderMoney','OM'],[
            'where' => $map,
            'join' => [
                'LEFT JOIN __ORDER__ O on OM.orderId=O.`id`',
                'LEFT JOIN __HOTEL_ROOMS__ HR on HR.id = O.room',
                'LEFT JOIN __HOTELS__ H on H.id = HR.hotel',
                'LEFT JOIN __USERS__ U on O.userId=U.id'
            ],
            'field' => "O.createTime,H.hotelName,U.realname,U.mobile,OM.money as monery,'会员充值' as `type`"
            ],false);
        //分页总数
        $includeCount = D::count('',[
                'table' => $include.' I',
            ]);
        $includePage = new \Org\Util\Page($includeCount,C('PAGE_NUMBER'),I('get.'));

        $include = D::get('',[
                'table' => $include.' I',
                'where' => $type,
                'limit' => ($includePage->firstRow.','.$includePage->listRows)
            ]);
        //退款详情（Drawback表）
        $where['D.createTime'] = get_selectTime( I('date'),I('date') );
        $Sql = D::get(['Drawback','D'],[
                'where' => $where,
                'join' =>[
                    'LEFT JOIN __ORDER__ O on D.orderId=O.id',
                    'LEFT JOIN __HOTELS__ H on H.id=O.hotel'
                ],
                'field' => "D.createTime,H.hotelName,H.mobile,D.money,'退款' as `type`"
            ],false);

        //提现详情（Postal表）
        $Filter['P.passdate'] = get_selectTime( I('date'),I('date') );
        $defray = D::get(['postal','P'],[
                'union' => $Sql,
                'where' => $Filter,
                'join' => [
                    'LEFT JOIN __HOTELS__ H on H.id=P.hotel'
                ],
                'field' => "P.passdate createTime,H.hotelName,H.mobile,P.monery money,'提现' as `type`"
            ],false);
        //分页总数
        $defrayCount = D::count('',[
                'table' => $defray.' I',
            ]);
        $defrayPage = new \Org\Util\Page($defrayCount,C('PAGE_NUMBER'),I('get.'),'defrayPage');

        $defray = D::get('',[
                'table' => $defray.' I',
                'where' => $type,
                'limit' => ($defrayPage->firstRow.','.$defrayPage->listRows)
            ]);

        $this->assign('include',$include);
        $this->assign('defray',$defray);
        $this->assign('includePage',$includePage->show());
        $this->assign('defrayPage',$defrayPage->show());
        $this->display();
    }
    //财务明细 导出
   public function export()
   {
        $db = parent::index(true);

        $dbName = array(
            array('dateTime','日期'),
            array('inAll','总收入'),
            array('outAll','总支出'),
            );
        $excelName = '财务明细记录_'.date('Ymd');

        export_Excel($excelName,$dbName,$db);
   }
}
