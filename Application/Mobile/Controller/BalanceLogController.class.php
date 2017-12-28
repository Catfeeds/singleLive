<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\D;
class BalanceLogController extends MobileCommonController {
    public static $open = false;//控制器不开放,需登录
    public $model = ['Order','O'];
    public function _map(&$data)
    {
        $map = [
                'O.userId' => session('user.id'),
                'O.status' => ['in',['0', '1', '2', '3', '7']],
                ]; 
        $sql = D::get(['OrderMoney','OM'],[
            'where' => $map,
            'join' => [
                    'LEFT JOIN __ORDER__ O ON O.id=OM.orderId',
                    'LEFT JOIN  __HOTELS__ H ON H.id = O.hotel',        
                ],
            'field' => 'OM.`money` allAmount,OM.type,O.createTime,H.hotelName,H.head',
            ],false);
        $lastSql = D::get(['Order','O'], [
                'where' => ['O.status' => 3, 'O.userId' => session('user.id')],
                'join' => [
                    'LEFT JOIN __ORDER_MONEY__ OM ON OM.orderId=O.id',
                    'LEFT JOIN __HOTELS__ H ON H.id=O.hotel',
                    'LEFT JOIN __ROOMS__ R ON R.id=O.room',
                ],
                'field' => "OM.`money` allAmount,'退款' as type,O.updateTime createTime,H.hotelName,H.head",
            ],false);
        $SQL = '('.$sql.') UNION ALL ('.$lastSql.')';

        $data = [
            'table' => '( '.$SQL.' ) ',
            'order' => 'O.createTime DESC',
        ];
    }
    public function index()
    {
        if (IS_AJAX) {
            parent::index(function($data){
                $data['img'] = getSrc($data['head']);
                $data['createTime'] = date('Y-m-d H:i:s',$data['createTime']);
                switch ($data['type']) {
                    case 'change':
                        $data['type'] = '换房';
                        break;
                    case 'buy':
                        $data['type'] = '正常购买';
                        break;
                    case 'continue':
                        $data['type'] = '续时';
                        break;
                    
                    default:
                        # code...
                        break;
                }

                return $data;
            });
       }else{
           $this->display();
        }
    }
}

