<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;
//系统消息
class ActiveNewsController extends CommonController {
	public $model = 'News';
	public function _map(&$data)
	{
		$map["status"] = array('neq','3');
		$map['type'] = "act";
        if(I('startTime') || I('endTime')){
            $map['createTime'] = get_selectTime(I('startTime'),I('endTime'));
        }
        if(I('title')){
			$map["title"] = ['like','%'.I('title').'%'];
        }
        $data = [
            'where' => $map,
			'order' => 'createTime desc'
        ];
	}

	//添加系统消息界面
	public function newsInsert()
	{
		$news = D('News');
		if(IS_POST){
			if($data = $news->create()){
				$news->add($data);
				$this->success('发送成功',U('ActiveNews/index'));
			}else{
				$this->error($news->getError());
			}
		}
		$this->display();
	}
	//修改系统消息
	public function updateNews()
	{
		$db = D::find('News',I('id'));
		$db['obj'] = $db['obj'] == 'single' ? '指定用户' : '全部用户';
		$news = D('News');
		if(IS_POST){
			if($data = $news->create()){
				$news->where("id=".$data['id'])->save($data);
				$this->success('修改成功',U('ActiveNews/index'));
			}else{
				$this->error($news->getError());
			}
		}
		$this->assign('db',$db);
		$this->display();
	}

	//查看消息详情
	public function newsinfo()
	{
		$data = D::find('News',I('id'));
		$this->assign('data',$data);
		$this->display();			
	}
	//删除系统消息
	public function delNews()
	{
		$Ary = [
			'status' => 3
		];
		if(D::save('News',I('id'),$Ary)){
			$this->success('删除成功！',U('ActiveNews/index'));
		}else{
			$this->error('删除失败');
		}
	}


}
