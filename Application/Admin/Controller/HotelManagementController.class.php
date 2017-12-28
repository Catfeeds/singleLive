<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;
//酒店管理模块
class HotelManagementController extends CommonController {
    public $model = 'Hotels';
    public function _map(&$data)
    {
        $map["_string"] = 'status!=9';
        if(I('startTime')){
            $map["_string"] .= ' And createTime >= '.strtotime(I('startTime')).'';
        }
        if(I('endTime')){
            $map["_string"] .= ' And createTime <= '.strtotime(I('endTime')).'';
        }
        if(I('title')){
            $where["hotelName"] = ['like','%'.I('title').'%'];
            $where["mobile"]  = ['like','%'.I('title').'%'];
            $where["_logic"] = 'or';
            $map['_complex'] = $where;
        }

        $data = [
            'where' => $map
        ];
    }
    public function index()
    {
        parent::index(function($data){
            $data['address'] = $data['province']." ".$data['city']." ".$data['area']." ".$data['address'];
            $data['createTime'] = date('Y-m-d H:i:s',$data['createTime']);
            return $data;
        });
    }
    public function export()
    {
        //获取提交数据
        $db = parent::index(true);

        foreach($db as $Key => $row){

            $db[$Key]['address'] = $row['province']." ".$row['city']." ".$row['area']." ".$row['address'];
            $db[$Key]['createTime'] = date('Y-m-d H:i:s',$row['createTime']);
            $db[$Key]['status'] = $row['status'] == 1 ? $row['status'] = "禁用" : $row['status'] = "启用";

        }

        //获取数据库字段名
        $dbName  = array(
            array('hotelName','酒店名称'),
            array('mobile','联系方式'),
            array('address','酒店地址'),
            array('createTime','创建时间'),
            array('status','酒店状态')
        );
        //文件名
        $fileName = date("Ymd_His")."_酒店信息";

        export_Excel($fileName,$dbName,$db);

    }
    /**
     * [add 新增页面]
     * @Author   尹新斌
     * @DateTime 2017-07-11
     * @Function []
     */
    public function add()
    {
        $this->display();
    }

    /**
     * [insert 新增酒店]
     * @Author   尹新斌
     * @DateTime 2017-07-12
     * @Function []
     * @return   [type]     [description]
     */
    public function insert()
    {
        $item=parent::insert(function($id){
            $data = [
                'username' => I('username'),
                'nickname' => I('username'),
                'hotel'    => $id,
                'password' => md5(I('password')),
                'root'     => 0,
                'group'    => 0,
                // 'head'     => I('head')
            ];
            if(D::add('HotelAdmins',$data)){
                return true;
            }else{
                return false;
            }
        });
        if($item){
            $this->success('添加成功！',U('HotelManagement/index'));
        }else{
            $this->error('添加失败，请刷新页面后重试！');
        }
    }
    /**
     * [getQrcode 生成二维码]
     * @Author   尹新斌
     * @DateTime 2017-07-14
     * @Function []
     * @return   [type]     [description]
     */
    public function getQrcode()
    {
        $id = I('id');
        $size = I('size');
        $url = 'http://'.$_SERVER['SERVER_NAME'].'/Index/index/hotel/'.base64_encode($id);
        ob_end_clean();
        vendor("phpqrcode.phpqrcode");
        $QRcode = new \QRcode();
        $QRcode->png($url,false,'H',$size);
    }


       //酒店类型修改
    public function hotel_list_edit()
    {

        $SQL = D::get(['hotels','H'],[
                'where' => 'H.id='.I('get.id').'',
                'join'  => 'left join __HOTEL_ADMINS__ A on H.id=A.hotel',
                'field' => 'H.*,A.username,A.password,A.hotel'
            ],false);

        $data = D::get('hotels',[
                'table' => $SQL.'R',
                'join'  => 'left join __FILES__ F on R.head=F.id',
                'field' => 'R.*,R.id RID,F.*'
            ]);

        $url = 'http://'.$_SERVER['SERVER_NAME'].'/Index/index/hotel/'.base64_encode(I('get.id'));
        ob_end_clean();
        vendor("phpqrcode.phpqrcode");

        $path = "Public/Admin/images/";
        if(!file_exists($path))
        {
            mkdir($path, 0700);
        }

        // 生成的文件名
        $fileName = $path.date('Ymd').I('get.id').'.png';

        $QRcode = new \QRcode();
        $QRcode->png($url, $fileName, 'H', '3');

        $this->assign('fileName',$fileName);
        $this->assign('data',$data);
        $this->display();
    }

    //酒店类型修改执行
    public function hotelUpdate()
    {
        $Ary = [
            'hotelName' => I('hotelName'),
            'province'  => I('province'),
            'city' => I('city'),
            'area' => I('area'),
            'address'  => I('address'),
            'mobile' => I('mobile'),
            'head' => I('head')            
        ];

        $row = D::save('hotels','id='.I('post.id').'',$Ary);

        $i = 0;
        if(!I('pwd1')&&!I('pwd2')){
            $i = 1;
        }else if(I('pwd1')!=I('pwd2')){
            $this->error('两次输入的密码不一致！');
        }

        if($i==0){
            $_POST['password'] = md5(I('pwd1'));
        }

        unset($_POST['pwd1']);
        unset($_POST['pwd2']);

        if(!I('head')){
            $_POST['head'] = I('realHead');
        }

        $admin = [
            'username' => I('username'),
            'password' => I('password')
        ];

        $hotelRow = D::save('hotel_admins','hotel='.I('post.id').'',$admin);

        if($row===0&&$hotelRow===0){
            $this->success('更新成功,数据无变化！',U('HotelManagement/index'));
        }else if($row===false||$hotelRow===false){
            $this->error('更新失败，请刷新页面后重试！');
        }else{
            $this->success('更新成功！',U('HotelManagement/index'));
        }

    }

    /*
        酒店列表：启用-禁用
     */
    public function set_status(){
        $id = I('id');
        switch(I('set')){
            case 1:$set = 0;break;
            case 0:$set = 1;break;
        }
        M($this->model)->where("id=".$id)->setField('status',$set);
        $this->success('操作成功');
    }
    /*
        酒店列表删除
     */
    public function hotel_del(){
        M($this->model)->where("id=".I('id'))->setField('status',9);
        $this->success('删除成功');
    }
    /*
     *  酒店轮播图
     * */
    public function hotel_banner(){
        $hid = I('id');
        $banner = D('HotelBanner');
        $imgs = $banner->where("hotel=".$hid)->find();
        //获取图片列表
        $map['id'] = array('in',$imgs['imgs']);
        $src = M('Files')->where($map)->field('id,savepath')->select();
        if(IS_POST){
            if($data = $banner->create()){
                //判断是否已经存在该酒店的id  存在-修改 不存在-新增
                $is_banner = $banner->where("hotel=".$data['hotel'])->find();
                if($is_banner){
                    if($is_banner && $data['imgs']!=null){
                        $str = implode(',',$data['imgs']);
                        $data['imgs'] = $is_banner['imgs'].','.$str;
                        $banner->where("hotel=".$data['hotel'])->save($data);
                    }else{
                        $banner->where("hotel=".$data['hotel'])->setField('update_time',time());
                    }
                    array_map('bannerImgs',D::get('Files',['id' => ['in',D::field('HotelBanner.imgs',['hotel' => $data['hotel']])]]));

                    $this->success('修改成功',U('HotelManagement/index'));
                }else{
                    $data['imgs'] = implode(',',$data['imgs']);
                    $banner->add($data);
                    array_map('bannerImgs',D::get('Files',['id' => ['in',D::field('HotelBanner.imgs',['hotel' => $data['hotel']])]]));

                    $this->success('新增成功',U('HotelManagement/index'));
                }
            }else{
                $this->error($banner->getError());
            }
        }
        $this->assign('src',$src);
        $this->assign('hid',$hid);
        $this->display();
    }
    /*
     *  删除酒店轮播图
     * */
    public function hotel_imgDel(){
        $banner = M('HotelBanner');
        $img = $banner->where("hotel=".I('hotel'))->getField('imgs');
        $arr = explode(',',$img);
        unset($arr[array_search(I('fileid'),$arr)]);
        $str = implode(',',$arr);
        $num = $banner->where("hotel=".I('hotel'))->setField('imgs',$str);
        if($num>0){
            $info['status'] = 'yes';
        }else{
            $info['status'] = 'no';
        }
        $this->ajaxReturn($info);
    }
    /*
    *  房间类型列表
    * */
    public function roomtype(){
        //酒店id
        $id = I('id');
        $hotelroom = D('HotelRooms');
        //查询房间类型  状态为正常的数据
        $room = D::get(['HotelRooms','a'],[
            'where' => [
                'a.hotel'=>$id,
                'a.status'=>0
            ],
            'join'  => 'LEFT JOIN __ROOMS__ b ON b.id = a.room',
            'field' => 'a.id,b.roomName'
        ]);
        if(I('room')){
            $map['a.room'] = I('room');
        }
        if(I('title')){
            $map['a.amount|a.minimum'] = array('like','%'.I('title').'%');
        }
        $info = http_build_query(I('param.'));
        $map['a.hotel'] = $id;
        $map['a.status'] = 0;
        $count = $hotelroom->alias('a')->where($map)->count();
        $page = new \Org\Util\Page($count,C('PAGE_NUMBER'));
        //列表数据
        $db = $hotelroom->alias('a')
        ->where($map)
        ->field('a.*,b.roomName')
        ->join('left join __ROOMS__ b on b.id = a.room')
        ->limit($page->firstRow.','.$page->listRows)
        ->select();
        foreach($db as $key=>$val){
            $db[$key]['img_ids'] = explode(',', $db[$key]['imgs_ids']);
            $db[$key]['src'] = getSrc($db[$key]['img_ids'][0]);
        }
        $this->assign('info',$info);
        $this->assign('page',$page->show());
        $this->assign('db',$db);
        $this->assign('hid',$id);
        $this->assign('room',$room);
        $this->display();
    }
    /*
     *  新增房间类型
     * */
    public function roomtype_add(){
        //酒店id
        $hid = I('hid');
        $hotelroom = D('HotelRooms');
        $room = D::get('Rooms',[
            'where' => ['status'=>0],
            'field' => 'id,roomName'
        ]);
        if(IS_POST){
            if($data = $hotelroom->create()){
                $data['imgs_ids'] = implode(',', $data['imgs_ids']);
                $hotelroom->add($data);
                $this->success('新增房间类型成功',U('HotelManagement/roomtype?id='.I('hotel')));
            }else{
               $this->error($hotelroom->getError());
            }
        }
        $this->assign('hid',$hid);
        $this->assign('room',$room);
        $this->display();
    }
    /*
     *  新增房间类型
     * */
    public function roomtype_edit(){
        $id = I('id');
        $hotelroom = D('HotelRooms');
        $info = $hotelroom->field('a.*,b.roomName')->alias('a')->where('a.id='.$id)
            ->join('inner join zc_rooms b on b.id = a.room')
            ->find();
        //获取文件路径列表
        $file['id'] = array('in',$info['imgs_ids']);
        $src = M('Files')->where($file)->field('id,savepath')->select();
        //更新操作
        if(IS_POST){
            if($data = $hotelroom->create()){
                //上传了图片
                $tupian = implode(',',I('imgs_ids'));
                //因为删除图片时采用了ajax,故此处在从数据库查一次图片字段
                $img = $hotelroom->where("id=".I('id'))->getField('imgs_ids');
                if($tupian!='' && $img!=''){
                    $data['imgs_ids'] = $img.','.$tupian;
                }elseif($tupian && $img==''){
                    $data['imgs_ids'] = $tupian;
                }else{
                    $data['imgs_ids'] = $img;
                }
                $hotelroom->where("id=".I('id'))->setField('status',9);
                $info = array(
                    'price' => $data['price'],
                    'minimum' => $data['minimum'],
                    'minute' => $data['minute'],
                    'hotel' => $data['hotel'],
                    'amount' => $data['amount'],
                    'imgs_ids' => $data['imgs_ids'],
                    'room' => $data['room']
                );
                $hotelroom->add($info);
                $this->success('更新成功',U('HotelManagement/roomtype?id='.I('hotel')));
            }else{
                $this->error($hotelroom->getError());
            }
        }
        $this->assign('src',$src);
        $this->assign('info',$info);
        $this->display();
    }
    /*
        删除房间类型里面的图片
     */
    public function pic_del(){
        $hotel = M('HotelRooms');
        $img = $hotel->where("id=".I('id'))->getField('imgs_ids');
        $arr = explode(',',$img);
        unset($arr[array_search(I('fileid'),$arr)]);
        $str = implode(',',$arr);
        $num = $hotel->where("id=".I('id'))->setField('imgs_ids',$str);
        if($num>0){
            $info['status'] = 'yes';
        }else{
            $info['status'] = 'no';
        }
        $this->ajaxReturn($info);
    }
    /*
        房间类型删除
    */
    public function roomtype_del(){
        M('HotelRooms')->where("id=".I('id'))->setField('status',9);
        $this->success('删除成功',U('HotelManagement/index'));
    }
    /*
        房间类型导出
    */
    public function export_roomtype(){
        if(I('room')){
            $map['a.room'] = I('room');
        }
        if(I('title')){
            $map['a.amount|a.minimum'] = array('like','%'.I('title').'%');
        }
        $map['a.hotel'] = I('id');
        $map['a.status'] = 0;
        //列表数据
        $db = D('HotelRooms')->alias('a')
        ->where($map)
        ->field('a.*,b.roomName,c.hotelName')
        ->join('left join zc_rooms b on b.id = a.room')
        ->join('left join zc_hotels c on c.id = a.hotel')
        ->select();
        foreach ($db as $key => $value) {
            $db[$key]['createTime'] = date_out($db[$key]['createTime']);
        }
        $xlsName  = date('Y-m-d_H:i:s',time()).'酒店房间类型';
        $xlsCell  = array(
            array('hotelName','酒店名称'),
            array('roomName','房间类型'),
            array('amount','价格'),
            array('minimum','最低入住时长'),
            array('createTime','创建时间'),
        );
        export_Excel($xlsName,$xlsCell,$db);
    }

}
