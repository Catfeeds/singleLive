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
    public $model = ['Users','U'];
    public function _map(&$data)
    {
        if ( I('title') ) {
            $map['CONCAT(U.realname,U.mobile)'] = ['like','%'.I('title').'%'];
        }
        if ( I('start') || I('end') ) {
            $map['O.createTime'] = get_selectTime( I('start'),I('end') );
        }
        $sql = D::get('Order',[
            'field' => 'userID,MAX(createTime) orderTime,SUM(price) total',
            'where' => "`status` IN ('1,2,9')",
            'group' => 'userID'
        ],false);
        $data = [
            'where' => $map,
            'field' => 'U.*,O.total,O.orderTime',
            'join'  => "LEFT JOIN $sql O ON O.userID = U.id",
            'order' => 'O.createTime DESC',
        ];
    }
    //用户订单列表
    public function index()
    {   
        $info = http_build_query(I('get.'));
        $db = parent::index(false);
        $this->assign('info',$info);
        $this->assign('db',$db['db']);
        $this->assign('db',$db['page']);
        $this->display();
    }
    //导出
    public function export(){
        $db = parent::index(true);
        $xlsName  = date('Y-m-d_H:i:s',time()).'客户统计';
        $xlsCell  = array(
            array('orderTime','日期'),
            array('realname','姓名'),
            array('mobile','电话'),
            array('total','成交金额')
        );
        export_Excel($xlsName,$xlsCell,$db);
    }
    //查看明细
    public function see(){
        $id = I('id');
        if ( I('start') || I('end') ) {
            $map['O.createTime'] = get_selectTime( I('start'),I('end') );
        }
        if(I('title')){
            $map['O.orderNo'] = I('title');
        }
        $map['O.status'] = ['in','1,2,9'];
        $map['O.userID'] = $id;
        $count = D::count(['Order','O'],[
            'where' => $map
        ]);
        $page = new \Org\Util\Page($count,C('PAGE_NUMBER'));
        $list = D::get(['Order','O'],[
            'where' => $map,
            'field' => 'O.*,U.realname,U.mobile',
            'join'  => 'LEFT JOIN __USERS__ U ON U.id = O.userID',
            'limit' => $page->firstRow.','.$page->listRows
        ]);
        $this->assign('db',$list);
        $this->assign('page',$page->show());
        $this->display();
    }
}
