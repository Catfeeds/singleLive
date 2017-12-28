<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\D;
class SystemController extends MobileCommonController {
    public static $open = false;//控制器不开放,需登录
    public $model = ['NewsUser','NU'];

    public function _map(&$data)
    {

        $SQL = D::get(['News','N'],[
            'where' => 'N.status!=9',
        ],false);

        $data = [
            'join' => $SQL.' N on N.id=NU.news',
            'where' => 'NU.users='.session('user.id').'',
            'field' =>'N.*,NU.status STA,NU.id NHID',
            'order' =>'N.createTime desc'
        ];
    }
    public function index(){
        $db = parent::index();
    }

    public function index_edit()
    {
        $Ary = [
            'status' => 1,
            ];
        D::save(['NewsUser','NU'],'news='.I('id').' and users='.session('user.id').'',$Ary);

        $news = D::find('news',I('id'));

        $this->assign('news',$news);
        $this->display();
    }


}