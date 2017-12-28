<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;
//扣除管理模块
class DeductionRecordController extends CommonController {
	public $model = ['DeductRecord','D'];
	public function _map(&$data)
	{
        $map["_string"] = "1=1 ";
        if(I('startTime')){
            $map["_string"] .= ' And createTime >= '.strtotime(I('startTime')).'';
        }
        if(I('endTime')){
            $map["_string"] .= ' And createTime <= '.strtotime(I('endTime')).'';
        }
        if(I('title')){
            $where["realname"] = ['like','%'.I('title').'%'];
            $where["hotelName"]  = ['like','%'.I('title').'%'];
            $where["_logic"] = 'or';
            $map['_complex'] = $where;
        }

        $data = [
         	'where' => $map,
        ];
	}
	//扣除记录
    public function index()
    {
    	$db = parent::index(function($data){

    		$data['createTime'] = date('Y-m-d',$data['createTime']);

    		return $data;
    	});
    }

    //导出记录
    public function export()
    {
    	$db = parent::index(true);

    	foreach ($db as $key => $data) {

    		$db[$key]['createTime'] = date('Y-m-d',$data['createTime']);

    	}

    	$dbName = array(
            array('no','记录编号'),
    		array('createTime','扣除时间'),
    		array('realname','会员姓名'),
    		array('mobile','手机号'),
    		array('hotelName','酒店名称'),
    		array('deduct','扣除时间/小时'),
    		array('amount','扣除金额')
    		);

    	$excelName = '扣除记录_'.date('Ymd');

    	export_Excel($excelName,$dbName,$db);
    }

}
