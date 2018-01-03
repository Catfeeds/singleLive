<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;

/*
 *  会员级别
 * 
 * */
class UserClubController extends CommonController
{
    public $model = 'Environment';
    public function _map(&$data)
    {
        $data = [
            'order' => 'add_time desc'
        ];
    }
    public function index(){
        $db = parent::index(false);
        foreach ($db['db'] as $key=>$val){
            $db['db'][$key]['imgs'] = explode(',',$db['db'][$key]['imgs']);
            $db['db'][$key]['imgShow'] = getSrc($db['db'][$key]['imgs'][0]);
        }
        //dump($db);die;
        $this->assign('db',$db['db']);
        $this->assign('page',$db['page']);
        $this->display();
    }
    /*
     *  添加处理
     * */
    public function addCheck(){
       $obj = D($this->model);
       if($data = $obj->create()){
            $data['imgs'] = implode(',',$data['imgs']);
            if($data['imgs']){
                M('Environment')->add($data);
                $this->success('添加成功',U('UserClub/index'));
            }else{
                $this->error('请上传图片');
            }
       }else{
           $this->error($obj->getError());
       }
    }

    /*
     *  修改
     * */
    public function edit(){
        $id = I('id');
        $db  = D::find('Environment',$id);
        $src = explode(',',$db['imgs']);
        foreach ($src as $key => $item){
            if($item == ''){
                unset($src[$key]);
            }
        }
        $this->assign('src',$src);
        $this->assign('db',$db);
        $this->display();
    }
    /*
     *  修改处理
     * */
    public function updateCheck(){
        $obj = D('Environment');
        if($data = $obj->create()){
            //判断是否已经存在该酒店的id  存在-修改 不存在-新增
            $is_banner = $obj->where("id=".$data['id'])->find();
            if($is_banner && $data['imgs']!=null){
                $str = implode(',',$data['imgs']);
                $data['imgs'] = $is_banner['imgs'].','.$str;
                $obj->where("id=".$data['id'])->save($data);
            }else{
                $data['imgs'] = implode(',',$data['imgs']);
                $obj->where("id=".$data['id'])->save($data);
            }
            $this->success('修改成功',U('UserClub/index'));
        }else{
            $this->error($obj->getError());
        }
    }
    /*
    *   删除
    * */
    public function del(){
        $id = I('id');
        M($this->model)->where("id=".$id)->delete();
        $this->success('删除成功');
    }

}
