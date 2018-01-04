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
                    'paper' => $data['paper'],
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
        酒店列表删除
     */
    public function house_del(){
        M($this->model)->where("id=".I('id'))->setField('status',3);
        $this->success('删除成功');
    }
    /*
     *  酒店轮播图
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
}
