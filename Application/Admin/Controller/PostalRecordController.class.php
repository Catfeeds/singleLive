<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;
//提现管理模块
class PostalRecordController extends CommonController {
    public $model = ['hotels','H'];
    public function _map(&$data){

        $map["_string"] = '1=1';
        if(I('startTime')){
            $map["_string"] .= ' And P.endDate >= '.strtotime(I('startTime')).'';
        }
        if(I('endTime')){
            $map["_string"] .= ' And P.endDate <= '.strtotime(I('endTime')).'';          
        }
        if(I('title')){
            $where["H.hotelName"] = ['like','%'.I('title').'%'];
            $map['_complex'] = $where;
        }

        $SQL = D::get(['postal','P'],[
                'order' => 'passdate DESC',
                'join'  => 'zc_hotels H on P.hotel=H.id',
                'field' => 'P.*,H.hotelName'
            ],false);
        $SQL = D::get('',[
                'table'  => $SQL.'R',
                'group' => 'hotel',
                'field' => 'SUM(actualmonery) monerys,id,hotel,hotelName,MAX(passdate) endDate'
            ],false);

        $data = [
            'where' => $map,
            'join'  => 'inner join '.$SQL.'P on P.hotel=H.id',
            'field' => 'P.hotel,P.monerys,P.endDate,H.hotelName'
        ];
    }
    //提现记录
    public function index()
    {
        $db = parent::index(function($data){
            $data['endDate'] = $data['endDate'] ? date('Y-m-d',$data['endDate']) : '-';
            return $data; 
        });
    }

    //提现记录导出
    public function export()
    {
        $db = parent::index(true);

        foreach ($db as $key => $data) {

            $db[$key]['endDate'] = date('Y-m-d',$data['endDate']);  
        }

        $dbName = array(
                array('hotelName','酒店名称'),
                array('endDate','提现处理日期'),
                array('monerys','提现金额'),
        );

        $excelName = date('Ymd').'_提现信息记录';
        export_Excel($excelName,$dbName,$db);
    }

    //查看提现记录详情
    public function record_edit()
    {

        $map["_string"] = 'hotel='.I('hotel').'';

        if(I('startTime')){
            $map["_string"] .= ' And applydate >= '.strtotime(I('startTime')).'';
        }
        if(I('endTime')){
            $map["_string"] .= ' And applydate <= '.strtotime(I('endTime')).'';          
        }       

        $db = D::get('postal',[
                'where' => $map,
                'order' => 'applydate DESC'
            ]);

        foreach ($db as $key => $data) {

            $db[$key]['applydate'] = date('Y-m-d',$data['applydate']);

            switch ($data['status']) {
                case '0':$db[$key]['status']='正在审核';break;
                case '1':$db[$key]['status']='通过';break;
                default:$db[$key]['status']='失败';break;
            }

        }

        $this->assign('db',$db);
        $this->display();
    }

    //酒店提现记录导出
    public function excel()
    {

        $map["_string"] = 'hotel='.I('hotel').'';

        if(I('startTime')){
            $map["_string"] .= ' And applydate >= '.strtotime(I('startTime')).'';
        }
        if(I('endTime')){
            $map["_string"] .= ' And applydate <= '.strtotime(I('endTime')).'';          
        }       

        $db = D::get('postal',[
                'where' => $map,
                'order' => 'applydate DESC'
            ]);

        foreach ($db as $key => $data) {

            $db[$key]['applydate'] = date('Y-m-d',$data['applydate']);

            switch ($data['status']) {
                case '0':$db[$key]['status']='正在审核';break;
                case '1':$db[$key]['status']='通过';break;
                default:$db[$key]['status']='失败';break;
            }

        }

        $dbName = array(
                array('applydate','申请日期'),
                array('monery','提现金额'),
                array('status','提现状态'),
        );

        $row = D::find('hotels',I('hotel'));
        $excelName = date('Y-m-d').$row['hotelName'].'提现记录';

       export_Excel($excelName,$dbName,$db);
    }
}
