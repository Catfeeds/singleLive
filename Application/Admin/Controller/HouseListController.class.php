<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;
//客房列表
class HouseListController extends CommonController {
    public $model = ['House','H'];
    public function _map(&$data)
    {
        $map["H.status"] = ['neq','3'];
        $map["C.type"] = 'h';
        if(I('startTime') || I('endTime')){
            $map["H.add_time"] = get_selectTime(I('startTime'),I('endTime'));
        }
        if(I('title')){
            $map['H.name'] = ['like','%'.I('title').'%'];
        }
        if(I('category')){
            $map['H.category'] = I('category');
        }
        $data = [
            'where' => $map,
            'join'  => 'LEFT JOIN __HOUSE_CATE__ C ON C.id = H.category',
            'field' =>  'H.*,C.title cateName'
        ];
    }
    public function index()
    {
        $houseCate = D::get('houseCate',[
            'where' => "`status`=1 AND `type`='h'"
        ]);
        $this->assign('cate',$houseCate);
        parent::index(function($data){
            $data['add_time'] = date('Y-m-d H:i:s',$data['add_time']);
            return $data;
        });
    }

    public function add()
    {
        $houseCate = D::get('houseCate',[
            'where' => "`status`=1 AND `type`='h'"
        ]);
        if(IS_POST){
            $house = D('House');
            if($data = $house->create()){
                $house->add($data);
                $this->success('添加成功',U('HouseList/index'));
            }else{
                $this->error($house->getError());
            }
        }
        $this->assign('cate',$houseCate);
        $this->display();
    }
    /*
     *  此处的修改  并非实际意义上的修改而是新增
     *  因为涉及到支付：
     *          如果有客户买了A客房价格是500元，并且已经支付成功，产生了订单。
     *          而之后管理员修改了，该客房的价格，就会出现订单的钱对应不上的情况。
     *          所以为了避免此类情况的发生，我们不论它是改了表单的任何数据，我们就将原来的数据逻辑删除，并新增一条数据
     * */
    public function edit(){
        $db = D::find('House',I('id'));
        $houseCate = D::get('houseCate',[
            'where' => "`status`=1 AND `type`='h'"
        ]);
        if(IS_POST){
            $house = D('House');
            if($data = $house->create()){
                $imgs = D::field('House.imgs',$data['id']);
                $arr = [
                    'category' => $data['category'],
                    'name' => $data['name'],
                    'equipment' => $data['equipment'],
                    'money' => $data['money'],
                    'mark' => $data['mark'],
                    'back' => $data['back'],
                    'come' => $data['come'],
                    'change' => $data['change'],
                    'sorce' => $data['sorce'],
                    //'paper' => $data['paper'],
                    'total_num' => $data['total_num'],
                    'pic' => $data['pic'],
                    'word' => $data['word'],
                    'push' => $data['push'],
                    'imgs' => $imgs,
                    'status' => 1,
                    'add_time' => time(),
                    'update_time' => time()
                ];
                $house->add($arr);
                D::set('House.status',$data['id'],'3');
                $this->success('修改成功',U('HouseList/index'));
            }else{
                $this->error($house->getError());
            }
        }
        $this->assign('cate',$houseCate);
        $this->assign('db',$db);
        $this->display();
    }
    /*
        房间列表删除
     */
    public function house_del(){
        M($this->model)->where("id=".I('id'))->setField('status',3);
        $this->success('删除成功');
    }
    /*
     *  房间轮播图
     * */
    public function banner(){
        $id = I('id');
        $banner = D('House');
        $imgs = $banner->where("id=".$id)->find();
        //获取图片列表
        $map['id'] = array('in',$imgs['imgs']);
        $src = M('Files')->where($map)->field('id,savepath')->select();
        if(IS_POST){
            if($data = $banner->create()){
                //判断是否已经存在该酒店的id  存在-修改 不存在-新增
                $is_banner = $banner->where("id=".$data['id'])->field('imgs')->find();
                if(!empty($is_banner)){
                    if($is_banner && $data['imgs']!=null){
                        $str = implode(',',$data['imgs']);
                        $data['imgs'] = $is_banner['imgs'].','.$str;
                        $banner->where("id=".$data['id'])->save($data);
                    }else{
                        $banner->where("id=".$data['id'])->setField('update_time',time());
                    }
                    array_map('imgs',D::get('Files',['id' => ['in',D::field('House.imgs',['id' => $data['id']])]]));
                    $this->success('修改成功',U('HouseList/index'));
                }else{
                    $data['imgs'] = implode(',',$data['imgs']);
                    $banner->add($data);
                    array_map('imgs',D::get('Files',['id' => ['in',D::field('House.imgs',['id' => $data['id']])]]));
                    $this->success('新增成功',U('HouseList/index'));
                }
            }else{
                $this->error($banner->getError());
            }
        }
        $this->assign('src',$src);
        $this->assign('id',$id);
        $this->display();
    }

    //添加客房订单
    public function OrderHouse(){
        $info = D::find('House',I('id'));
        $order = D('Order');
        if(IS_POST){
            if($data = $order->create()){
                $parameter = push_select_time($data['inTime'],$data['outTime']);
                $arr = [
                    'roomID' => $data['roomID'],
                    'type' => $data['type']
                ];
                $bool = is_house_all($parameter,$arr);
                if($bool === true){
                    $data['orderNo'] = set_orderNo($data['type']);
                    $order->add($data);
                    checkTable($data['orderNo']);
                    $this->success('添加订单成功,可到订单列表查看',U('HouseList/index'));
                }else{
                    $this->error('所选日期存在满房的情况');
                }
            }else{
                $this->error($order->getError());
            }
        }
        $myDate = get_minDate_maxDate();
        $this->assign('myDate',$myDate);
        $this->assign('info',$info);
        $this->display();
    }
    /*
     *  现在客户提的新需求
     *      对于客房要每天设置的价格
     *      思路：
     *          1、首先添加时间区段在这个时间区段内的价格都一致
     *          2、在当前时间区段内，添加特殊时间设置
     *              (周一 ~ 周五 ) 设定一个价格
     *              周六日 设定一个价格
     *              特殊时间  无限添加设置一个价格
     * */
    public function templete(){
        $roomID = I('id');
        if(I('startTime') || I('endTime')){
            $map['createTime'] = get_selectTime(I('startTime'),I('endTime'));
        }
        $map['status'] = array('neq','3');
        $map['roomID'] = $roomID;
        $count = D::count('Templete',['where'=>$map]);
        $page = new \Org\Util\Page($count,C('PAGE_NUMBER'));
        $db = D::get('Templete',[
            'where' => $map
        ]);
        $this->assign('page',$page->show());
        $this->assign('db',$db);
        $this->assign('roomID',$roomID);
        $this->display();
    }
    /*
     *  价格模板 区段添加
     *      每次添加成功后，再次添加时去查一下该房间的最大的结束时间
     *      并赋值给日历插件，防止重复选择日期
     * */
    public function addTemplete(){
        $roomID = I('roomID');
        $myDate = [];
        $time = D::find('Templete',[
            'where'=>['roomID'=>$roomID],
            'field'=>'MAX(end) endTime'
        ]);
        $showTime = date('Y-m-d',strtotime("{$time['endTime']} +1 day"));
        $now = date('Y-m-d');
        $myDate['min'] = $time['endTime'] ? $showTime : $now;
        $myDate['max'] = $time['endTime'] ? date('Y-m-d',strtotime("$showTime +3 month")) : date('Y-m-d',strtotime("$now +3 month"));
        $tpl = D('Templete');
        if(IS_POST){
            if($data = $tpl->create()){
                $tpl->add($data);
                $this->success('添加成功',U('HouseList/templete?id='.$data['roomID']));
            }else{
                $this->error($tpl->getError());
            }
        }
        $this->assign('roomID',$roomID);
        $this->assign('myDate',$myDate);
        $this->display();
    }
    /*
     *  设置价格
     * */
    public function price(){
        $tID = I('id');
        $tpl = D::find('Templete',$tID);
        $arr = [
            'MF_day' => $this->select_price($tID,$tpl['start'],$tpl['end'],'1'),
            'SS_day' => $this->select_price($tID,$tpl['start'],$tpl['end'],'2'),
            'special_day' => $this->select_price($tID,$tpl['start'],$tpl['end'],'3')
        ];
        $result = D::get('TempletePrice',['where'=>['tID'=>$tID]]);
        if(!$result){
            $class = 'yes';
        }elseif($result && $arr['special_day']){
            $class = 'ok';
        }else{
            $class = 'no';
        }
        $this->assign('class',$class);
        $this->assign('arr',$arr);
        $this->assign('tpl',$tpl);
        $this->display();
    }
    //删除特殊价格设置
    public function del_price(){
        $get = I('get.');
        $tpl = D::find('Templete',$get['tID']);
        $date = D::field('TempletePrice.day',$get['id']);
        $week = date('w',strtotime($date));
        if($week == '0' || $week == '5' || $week == '6'){
            $type = 2;
        }else{
            $type = 1;
        }
        $arr = $this->select_price($get['tID'],$tpl['start'],$tpl['end'],$type);
        D::save('TempletePrice',$get['id'],[
            'price' => $arr['price'],
            'type'  => $type
        ]);
        $this->success('删除成功',U('HouseList/templete?id='.$tpl['roomID']));
    }
    //设置价格逻辑
    public function setPrice(){
        $price = D('TempletePrice');
        if($data = $price->create()){
            $post = I('post.');
            $tpl = D::find('Templete',$post['tID']);
            $arr = get_start_end_week($tpl['start'],$tpl['end']);
            //组装要插入的数组
            $arrAll = array_map(function($data)use($post){
                if($data['type'] == 1){
                    $data['price'] = $post['price1'];
                }else{
                    $data['price'] = $post['price2'];
                }
                $data['roomID'] = $post['roomID'];
                $data['tID'] = $post['tID'];
                return $data;
            },$arr);
            /*
             *  判断是否已经存在了价格模板
             *      若存在,则先删除在插入(这里不去看到底是改了哪个值不去做循环,直接删除，重新插入)
             *      若不存在直接插入
             * */
            $is = D::find('TempletePrice',['where'=>['tID'=>$post['tID']]]);
            if($is){
                M('TempletePrice')->where("tID=".$post['tID'])->delete();
            }
            M('TempletePrice')->addAll($arrAll);
            //判断是否设置了特殊日期
            if($post['choose'] == 1){
                //两个数组合并 参数1：作为键的数组，参数2：作为值的数组
                $combine = array_combine($post['day'],$post['price3']);
                //循环更新特殊日期的价格
                foreach ($combine as $key => $val){
                    $map['tID'] = $post['tID'];
                    $map['day'] = $key;
                    D::save('TempletePrice',['where'=>$map],[
                        'price' => $val,
                        'type'  => 3
                    ]);
                }
            }
            $this->success('设置成功',U('HouseList/templete?id='.$post['roomID']));
        }else{
            $this->error($price->getError());
        }
    }
    //模板id  开始 结束  类型
    public function select_price($tID,$start,$end,$type){
        $map['tID'] = $tID;
        $map['day'] = array('between',[$start,$end]);
        $map['type'] = $type;
        if($type == 1 || $type == 2){
            $arr = D::find('TempletePrice',['where'=>$map]);
        }else{
            $arr = D::get('TempletePrice',[
                'where'=>$map
            ]);
        }
        return $arr;
    }
}
