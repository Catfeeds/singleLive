<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;

/*
   订单统计
*/
class FinanceCountController extends CommonController
{
   // public $model = 'Finance';
    public function _map(&$data)
    {
        switch (ACTION_NAME){
            case 'index':
                if ( I('start') || I('end') ) {
                $map['time'] = get_selectTime(I('start'),I('end'));
                }
                $sql = D::get('Finance',[
                    'where' => $map,
                    'field' => "UNIX_TIMESTAMP(MAX(createDate)) time,SUM(CASE WHEN type='pay' THEN money ELSE 0 END) upPay,SUM(CASE WHEN type='recharge' THEN money ELSE 0 END) up,SUM(CASE WHEN type='back' THEN money ELSE 0 END) down",
                    'group' => 'createDate',
                ],false);
                $data = [
                    'table' => "{$sql} M",
                    'field' => 'M.time,(M.upPay+M.up) inMoney,M.down outMoney,(M.upPay+M.up-M.down) sideMoney',
                    'order' => 'M.time DESC',
                ];
                break;
            case 'see':
                $map['createDate'] = I('date');
                $data = [
                    'table' => '__FINANCE__',
                    'where' => $map,
                    'order' => 'createDate desc,id desc'
                ];
                break;
        }

    }
    //用户订单列表
    public function index()
    {
        $db = parent::index(false);
        $this->assign('db',$db['db']);
        $this->assign('page',$db['page']);
        $this->display();
    }
    //查看明细
    public function see(){
        echo parent::index('sql');die;
        $this->display();
    }
    //导出
    public function export(){
        $db = parent::index(true);
        $xlsName  = date('Y-m-d_H:i:s',time()).'客户统计';
        $xlsCell  = array(
            array('createTime','日期'),
            array('orderNo','订单号'),
            array('username','姓名'),
            array('mobile','电话'),
            array('price','金额'),
            array('status','状态')
        );
        export_Excel($xlsName,$xlsCell,$db);
    }

}
