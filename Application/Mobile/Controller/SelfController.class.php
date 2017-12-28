<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\D;
class SelfController extends MobileCommonController {
    public static $open = false;//控制器不开放,需登录
    public $model = 'Hotels';
    public function _map(&$data)
    {
        switch (ACTION_NAME) {
            case 'collection':
                $hotels = D::lists('Collection','hotel',['user' => session('user.id')]);
                $data = ['where' =>['id' => ['in',$hotels]]];
                break;
            case 'balance' :
                $sql = D::get(['Order','O'],[
                    'group' => 'O.room,O.userId',
                    'join'  => 'LEFT JOIN zc_hotel_rooms R ON R.id = O.room',
                    'field' => 'O.userId,O.hotel,R.room,sum(O.duration) duration,sum(O.used) used',
                    'where' => ['O.status' => '0'],
                    ],false);
                $data = [
                    'table' => $sql.' O',
                    'where' => ['O.userId' => session('user.id')],
                    'join' => ['LEFT JOIN zc_rooms R ON R.id = O.room',
                    'LEFT JOIN zc_hotels H ON H.id = O.hotel'],
                    'field' => 'O.*,R.roomName,H.hotelName,H.mobile',
                ];
        }
    }
    /**
     * [index 个人首页]
     * @Author   尹新斌
     * @DateTime 2017-07-14
     * @Function []
     * @return   [type]     [description]
     */
    public function index()
    {
    	$db = D::find('Users',session('user.id'));
    	$this->assign('db',$db);
    	$this->display();
    }
    /**
     * [collection description]
     * @Author   尹新斌
     * @DateTime 2017-07-18
     * @Function []
     * @return   [type]     [description]
     */
    public function collection()
    {
        if (IS_AJAX) {
            parent::index(function($data){
                $data['head'] = getSrc($data['head']);
                $data['id64'] = base64_encode($data['id']);
                if (in_array($data['id'], $myHotel)) {
                    $data['img'] = 'sc2';
                }else{
                    $data['img'] = 'sc';
                }
                return $data;
            });
        }else{
            $this->display();
        }
    }
    /*
     *  房间查看
     * */
    public function look(){
        $id = base64_decode(I('hotel'));
        $db = D::get(['HotelRooms','A'],[
            'where' => ['A.hotel'=>$id,'A.status' =>'0'],
            'join' => 'LEFT JOIN __ROOMS__ B ON B.id = A.room',
            'field' => 'A.*,B.roomName'
        ]);
        foreach ($db as $key=>$value){
            $map['id'] = array('in',$db[$key]['imgs_ids']);
            $db[$key]['file'] = M('Files')->where($map)->field('savepath,savename')->select();
        }
        $data = array_map(function($data)use($db){
            $data['hotel'] = base64_encode($data['hotel']);
            foreach($data['file'] as $key=>$val){
                $data['file'][$key]['path'] = '/Uploads'.$val['savepath'].$val['savename'];
            }
            return $data;
        },$db);
        $this->assign('data',$data);
        $this->display();
    }
    /*
     *  我的余额
     * */
    public function balance(){
        // echo  parent::index('sql');die;
        if(IS_AJAX){
            parent::index(function($data){
                $data['have'] = $data['duration'] - $data['used'];
                return $data;
            });
        }else{
            $this->display();
        }
    }
}
