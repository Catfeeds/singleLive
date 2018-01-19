<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Faster;
use Think\D;

/*
 *  会员级别
 * 
 * */
class GradesController extends CommonController
{
    public $model = 'Grades';
    public function _map(&$data)
    {
        $map['status'] = array('neq','9');
        $data = [
            'where' => $map,
            'order' => 'sort asc'
        ];
    }
    /*
     *  修改-逻辑(判断所有人，有存在，在这个会员等级不，若在修改即新增；若不在则新增)
     * */
    public function edit(){
        $id = I('id');
        $db  = D::find('Grades',$id);
        if(IS_POST){
            $map['nowLevel'] = I('id');
            $num = M('Users')->where($map)->count();
            if($info  = D('Grades')->create()){
                if($num>0){
                    //新增
                    $data = array(
                        'title' => I('title'),
                        'sorce' => I('sorce'),
                        'pic' => I('pic'),
                        'content' => I('content')
                    );
                    M($this->model)->add($data);
                    D::set('Grades.status',I('id'),'9');
                }else{
                    //修改
                    M('Grades')->save($info);
                }
                $this->success('更新成功',U('Grades/index'));
            }else{
                $this->error(D('Grades')->getError());
            }
        }
        $this->assign('db',$db);
        $this->display();
    }
    /*
     *  逻辑删除 因为若有会员，已经买过这个会员卡的话，
     *  直接删除会找不到，对应的级别id和级别名称
     * */
    public function del(){
        //判断该会员卡下  有没有对应级别的人
        $map['nowLevel'] = I('id');
        $num = M('Users')->where($map)->count();
        if($num){
            $this->error('该级别会员卡，已有会员无法删除');
        }else{
            D::set('Grades.status',I('id'),'9');
            $this->success('删除成功');
        }
    }

}
