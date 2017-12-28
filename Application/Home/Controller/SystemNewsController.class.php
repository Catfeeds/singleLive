<?php
namespace Home\Controller;
use Think\Controller;
use Think\D;
//系统管理
class SystemNewsController extends CommonController {
	public $model = ['NewsHotel','NH'];

    public function _map(&$data)
    {
        if(I('title')){
            $map['title'] = ['like','%'.I('title').'%'];
        }

        $SQL = D::get(['News','N'],[
            'where' => 'N.status!=9',
        ],false);

        $data = [
            'join' => $SQL.' N on N.id=NH.news',
            'where' => 'NH.hotel='.session('hotel_user.hotel').'',
            'field' =>'N.*,NH.status STA,NH.id NHID',
            'order' => 'NH.status'
        ];
    }

    public function index()
    {
        $db = parent::index(function($data){

            $data['startTime'] = date('Y-m-d',$data['startTime']);
            $data['endTime'] = $data['endTime'] ? date('Y-m-d',$data['endTime']) : '长期';

            switch ($data['STA'] ) {
                case 0:$data['STA'] = '未读';break;
                case 1:$data['STA'] = '已读';break;
            }
            return $data;
        });
    }

    public function newsInfo()
    {
        $Ary = [
            'status' => 1
        ];

        D::save('news_hotel',I('NHID'),$Ary);

        $data = D::find('news',I('id'));

        $data['startTime'] = date('Y-m-d',$data['startTime']);
        $data['endTime'] = $data['endTime'] ? date('Y-m-d',$data['endTime']) : '长期';
        $this->assign('data',$data);
        $this->display();
    }
}
