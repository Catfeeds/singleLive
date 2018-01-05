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
                    'paper' => $data['paper'],
                    'total_num' => $data['total_num'],
                    'status' => 1,
                    'add_time' => time(),
                    'update_time' => time()
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
        $count = $package_set->where("pid=".$pid)->count();
        $page = new \Org\Util\Page($count,C('PAGE_NUMBER'));
        $list = $package_set->field("*,(money*attr) total")
            ->where("pid=".$pid)
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
}
