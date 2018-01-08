<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;

//电子券列表
class CouponController extends CommonController {
    public $model = 'Coupon';
    public function _map(&$data)
    {
        $map["status"] = ['neq','3'];
        if(I('startTime') || I('endTime')){
            $map["add_time"] = get_selectTime(I('startTime'),I('endTime'));
        }
        if(I('title')){
            $map['title'] = ['like','%'.I('title').'%'];
        }
        $data = [
            'where' => $map,
        ];
    }

    public function add()
    {
        $package = D::get('houseCate',[
            'where' => "`status`=1 AND `type`='t'"
        ]);
        $house = D::get('houseCate',[
            'where' => "`status`=1 AND `type`='h'"
        ]);
        $this->assign('house',$house);
        $this->assign('package',$package);
        $this->display();
    }
    /*
     *  此处的修改  并非实际意义上的修改而是新增
     * */
    public function edit(){
        $db = D::find('Coupon',I('id'));
        $db['hcate'] = explode(',',$db['hcate']);
        $db['tcate'] = explode(',',$db['tcate']);
        $package = D::get('houseCate',[
            'where' => "`status`=1 AND `type`='t'"
        ]);
        $house = D::get('houseCate',[
            'where' => "`status`=1 AND `type`='h'"
        ]);
        if(IS_POST){
            $Coupon = D('Coupon');
            if($data = $Coupon->create()){
                $arr = [
                    'title' => $data['title'],
                    'money' => $data['money'],
                    'exprie_start' => $data['exprie_start'],
                    'exprie_end' => $data['exprie_end'],
                    'num' => $data['num'],
                    'mark' => $data['mark'],
                    'pic' => $data['pic'],
                    'hcate' => $data['hcate'],
                    'tcate' => $data['tcate'],
                    'sorce' => $data['sorce'],
                    'status' => 1,
                    'year' => date('Y'),
                    'notDate' => $data['notDate'],
                    'add_time' => time(),
                    'update_time' => time()
                ];
                $Coupon->add($arr);
                //修改之前 套餐状态
                D::set('Coupon.status',$data['id'],'3');
                $this->success('修改成功',U('Coupon/index'));
            }else{
                $this->error($Coupon->getError());
            }
        }
        $this->assign('house',$house);
        $this->assign('package',$package);
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
}
