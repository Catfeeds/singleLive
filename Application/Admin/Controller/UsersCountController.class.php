<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;

/*
   用户统计
*/
class UsersCountController extends CommonController
{
    //public $model = 'Finance';
    public function _map(&$data)
    {
        switch (ACTION_NAME){
            case 'index':
                if ( I('title') ) {
                    $map['CONCAT(U.realname,U.mobile)'] = ['like','%'.I('title').'%'];
                }
                if ( I('start') || I('end') ) {
                    $map['M.lastDate'] = get_selectTime( I('start'),I('end') );
                }
                $sql = D::get(['Finance','F'],[
                    'field' => "userID,UNIX_TIMESTAMP(MAX(createDate)) lastDate,SUM(CASE WHEN type='pay' THEN money ELSE 0 END) pay,SUM(CASE WHEN type='recharge' THEN money ELSE 0 END) recharge,SUM(CASE WHEN type='back' THEN money ELSE 0 END) back",
                    'join'  => 'LEFT JOIN __USERS__ U ON U.id = F.userID',
                    'group' => 'F.userID'
                ],false);
                $data = [
                    'where' => $map,
                    'table' => "{$sql} M",
                    'field' => 'M.*,(M.pay+M.recharge-M.back) total,U.realname,U.mobile',
                    'join'  => "LEFT JOIN __USERS__ U ON U.id = M.userID",
                    'order' => 'M.lastDate DESC',
                ];
                break;
            case 'export':
                if ( I('title') ) {
                    $map['CONCAT(U.realname,U.mobile)'] = ['like','%'.I('title').'%'];
                }
                if ( I('start') || I('end') ) {
                    $map['M.lastDate'] = get_selectTime( I('start'),I('end') );
                }

                $sql = D::get(['Finance','F'],[
                    'field' => "userID,UNIX_TIMESTAMP(MAX(createDate)) lastDate,SUM(CASE WHEN type='pay' THEN money ELSE 0 END) pay,SUM(CASE WHEN type='recharge' THEN money ELSE 0 END) recharge,SUM(CASE WHEN type='back' THEN money ELSE 0 END) back",
                    'join'  => 'LEFT JOIN __USERS__ U ON U.id = F.userID',
                    'group' => 'F.userID'
                ],false);
                $data = [
                    'where' => $map,
                    'table' => "{$sql} M",
                    'field' => 'M.*,(M.pay+M.recharge-M.back) total,U.realname,U.mobile',
                    'join'  => "LEFT JOIN __USERS__ U ON U.id = M.userID",
                    'order' => 'M.lastDate DESC',
                ];
                break;
        }

    }
    //用户订单列表
    public function index()
    {
        $info = http_build_query(I('get.'));
        $db = parent::index(false);
        $this->assign('info',$info);
        $this->assign('db',$db['db']);
        $this->assign('page',$db['page']);
        $this->display();
    }
    //导出
    public function export(){
        $db = array_map(function($data){
            $data['lastDate'] = date('Y-m-d',$data['lastDate']);
            return $data;
        },parent::index(true));
        $xlsName  = date('Y-m-d_H:i:s',time()).'客户统计';
        $xlsCell  = array(
            array('lastDate','日期'),
            array('realname','姓名'),
            array('mobile','电话'),
            array('total','成交总金额')
        );
        export_Excel($xlsName,$xlsCell,$db);
    }
    //查看明细
    public function see(){
        $map['userID'] = I('uid');
        if ( I('start') || I('end') ) {
            $map['F.createDate'] = get_DateTime(I('start'),I('end'));
        }
        if(I('title')){
            $map['F.orderNO'] = I('title');
        }
        $count = D::count(['Finance','F'],[
            'where' => $map
        ]);
        $page = new \Org\Util\Page($count,C('PAGE_NUMBER'));
        $list = D::get(['Finance','F'],[
            'where' => $map,
            'field' => 'F.*,U.realname,U.mobile',
            'join'  => 'LEFT JOIN __USERS__ U ON U.id = F.userID',
            'order' => 'F.createDate desc,F.id desc',
            'limit' => $page->firstRow.','.$page->listRows
        ]);
        $list = array_map(function($data){
            $data['type_name'] = getTypes($data['type']);
            return $data;
        },$list);
        $this->assign('uid',I('uid'));
        $this->assign('db',$list);
        $this->assign('page',$page->show());
        $this->display();
    }
}
