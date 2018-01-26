<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;

//套餐列表
class PackageListController extends CommonController {
    public $model = ['Package','P'];
    public function _map(&$data)
    {
        $map["P.status"] = ['neq','3'];
        $map["C.type"] = 't';
        if(I('startTime') || I('endTime')){
            $map["P.add_time"] = get_selectTime(I('startTime'),I('endTime'));
        }
        if(I('title')){
            $map['P.title'] = ['like','%'.I('title').'%'];
        }
        if(I('category')){
            $map['P.category'] = I('category');
        }
        $data = [
            'where' => $map,
            'join'  => 'LEFT JOIN __HOUSE_CATE__ C ON C.id = P.category',
            'field' =>  'P.*,C.title cateName'
        ];
    }
    public function index()
    {
        $houseCate = D::get('houseCate',[
            'where' => "`status`=1 AND `type`='t'"
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
            'where' => "`status`=1 AND `type`='t'"
        ]);
        if(IS_POST){
            $house = D('Package');
            if($data = $house->create()){
                $house->add($data);
                $this->success('添加成功',U('PackageList/index'));
            }else{
                $this->error($house->getError());
            }
        }
        $myDate = get_minDate_maxDate();
        $this->assign('myDate',$myDate);
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
        $db = D::find('Package',I('id'));
        $houseCate = D::get('houseCate',[
            'where' => "`status`=1 AND `type`='t'"
        ]);
        if(IS_POST){
            $house = D('Package');
            if($data = $house->create()){
                $arr = [
                    'category' => $data['category'],
                    'title' => $data['title'],
                    'limit' => $data['limit'],
                    'packMoney' => $data['packMoney'],
                    'mark' => $data['mark'],
                    'content' => $data['content'],
                    'sorce' => $data['sorce'],
                    'pic' => $data['pic'],
                    //'paper' => $data['paper'],
                    'word' => $data['word'],
                    'total_num' => $data['total_num'],
                    'push' => $data['push'],
                    'status' => 1,
                    'add_time' => time(),
                    'update_time' => time(),
                    'allowIn' =>$data['allowIn'],
                    'allowOut' =>$data['allowOut']
                ];
                $newId = $house->add($arr);
                //修改之前 套餐状态
                D::set('Package.status',$data['id'],'3');
                //修改套餐内容的pid--因为修改即新增,pid会对应不了
                M('PackageSet')->where("pid=".$data['id'])->setField('pid',$newId);
                $this->success('修改成功',U('PackageList/index'));
            }else{
                $this->error($house->getError());
            }
        }
        $myDate = get_minDate_maxDate();
        $this->assign('myDate',$myDate);
        $this->assign('cate',$houseCate);
        $this->assign('db',$db);
        $this->display();
    }
    /*
        列表删除
     */
    public function package_del(){
        M($this->model)->where("id=".I('id'))->setField('status',3);
        $this->success('删除成功');
    }
    //套餐内容列表
    public function content(){
        $pid = I('pid');
        $package_set = M('PackageSet');
        if(I('title')){
            $map['title'] = array('like','%'.I('title').'%');
        }
        $map['pid'] = $pid;
        $count = $package_set->where($map)->count();
        $page = new \Org\Util\Page($count,C('PAGE_NUMBER'));
        $list = $package_set->field("*,(money*attr) total")
            ->where($map)
            ->limit($page->firstRow.','.$page->listRows)
            ->select();
        $this->assign('list',$list);
        $this->assign('page',$page->show());
        $this->assign('pid',$pid);
        $this->display();
    }
    //套餐内容列表-新增
    public function contentAdd(){
        $pid = I('pid');
        $package_set = D('PackageSet');
        if(IS_POST){
            if($data = $package_set->create()){
                $package_set->add($data);
                $this->success('添加成功',U('PackageList/content?pid='.$data['pid']));
            }else{
                $this->error($package_set->getError());
            }
        }
        $this->assign('pid',$pid);
        $this->display();
    }
    //套餐内容列表-修改
    public function contentEdit(){
        $id = I('id');
        $db = D::find('PackageSet',$id);
        $set = D('PackageSet');
        if(IS_POST){
            if($data = $set->create()){
                $set->save($data);
                $this->success('修改成功',U('PackageList/content?pid='.$data['pid']));
            }else{
                $this->error($set->getError());
            }
        }
        $this->assign('db',$db);
        $this->display();
    }
    //套餐内容列表-删除
    public function contentDel(){
        $id = I('id');
        M('PackageSet')->where("id=".$id)->delete();
        $this->success('删除成功');
    }
    //添加套餐订单
    public function OrderPackage(){
        $info = D::find('Package',I('id'));
        $order = D('Order');
        if(IS_POST){
            if($data = $order->create()){
                $parameter = $data['inTime'];
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
        /*
         *  判断
         *      当前日期是不是比设定的允许开始时间大 ？ 当前时间 : 设定时间
         * */
        if(date('Y-m-d') > $info['allowIn']){
            $date = date('Y-m-d');
        }else{
            $date = $info['allowIn'];
        }
        $myDate = [
            'min' => $date,
            'max' => $info['allowOut']
        ];
        $this->assign('myDate',$myDate);
        $this->assign('info',$info);
        $this->display();
    }
}
