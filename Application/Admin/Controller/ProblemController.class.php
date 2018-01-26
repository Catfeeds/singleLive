<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;

/*
 *  常见问题列表
 * */
class ProblemController extends CommonController
{
    public $model = 'Problem';
    public function _map(&$data)
    {
        if(I('title')){
            $map['title'] = array('like','%'.I('title').'%');
        }
        $data = [
            'where' => $map,
            'order' => 'add_time desc'
        ];
    }

    /*
     *  修改
     * */
    public function edit(){
        $id = I('id');
        $db  = D::find('Problem',$id);
        $this->assign('db',$db);
        $this->display();
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
