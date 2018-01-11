<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;

/*
 *  Banner管理
 * */
class BannerController extends CommonController
{
    public $model = 'Banner';
    public function _map(&$data)
    {
        if(I('type')){
            $map['type'] = I('type');
        }
        $data = [
            'where' => $map,
            'order' => 'add_time desc'
        ];
    }
    public function index(){
        //header('Content-type:text/html;charset=UTF-8');
        $db = parent::index(false);
        foreach ($db['db'] as $key=>$val){
            $db['db'][$key]['imgs'] = explode(',',$db['db'][$key]['imgs']);
            $db['db'][$key]['imgShow'] = getSrc($db['db'][$key]['imgs'][0]);
            switch($db['db'][$key]['type']){
                case 'f':
                    $db['db'][$key]['typeName'] = '餐饮';
                    break;
                case 'e':
                    $db['db'][$key]['typeName'] = '环境';
                    break;
                case 'a':
                    $db['db'][$key]['typeName'] = '体验活动';
                    break;
                case 'h':
                    $db['db'][$key]['typeName'] = '客房';
                    break;
                case 't':
                    $db['db'][$key]['typeName'] = '套餐';
                    break;
                case 'm':
                    $db['db'][$key]['typeName'] = '会员俱乐部';
                    break;
                case 'b':
                    $db['db'][$key]['typeName'] = '首页banner';
                    break;
            }
        }
        $this->assign('db',$db['db']);
        $this->assign('page',$db['page']);
        $this->display();
    }
    /*
     *  添加处理
     * */
    public function addCheck(){
       $obj = M('Banner');
       if($data = $obj->create()){
            $data['imgs'] = implode(',',$data['imgs']);
            $data['add_time'] = time();
            if($data['imgs']){
                $obj->add($data);
                $this->success('添加成功',U('Banner/index'));
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
        $db  = D::find('Banner',$id);
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
        $obj = M('Banner');
        if($data = $obj->create()){
            $data['update_time'] = time();
            $is_banner = $obj->where("id=".$data['id'])->find();
            if($is_banner && $data['imgs']!=null){
                $str = implode(',',$data['imgs']);
                $data['imgs'] = $is_banner['imgs'].','.$str;
                $obj->where("id=".$data['id'])->save($data);
            }else{
                $data['imgs'] = implode(',',$data['imgs']);
                $obj->where("id=".$data['id'])->save($data);
            }
            $this->success('修改成功',U('Banner/index'));
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
