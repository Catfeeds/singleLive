<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;
//系统管理
class SystemNewsController extends CommonController {
	public $model = 'News';
	public function _map(&$data)
	{
		$Ary = [
			'status' => 1
		];
		$ary = [
			'status' => 2
		];

		D::save('News','startTime<='.time().'and status=0',$Ary);
		D::save('News','endTime<='.time().'and status=1',$ary);

		$map["_string"] = 'status!=9';

        if(I('startTime')){
            $map["_string"] .= ' And startTime >= '.strtotime(I('startTime')).'';
        }
        if(I('endTime')){
            $map["_string"] .= ' And startTime <= '.strtotime(I('endTime')).'';          
        }
        if(I('title')){
            $where["title"] = ['like','%'.I('title').'%'];
            $map['_complex'] = $where;
        }

        $data = [
            'where' => $map
        ];
	}
	public function index()
	{
		$db = parent::index(function($data){

			$data['startTime'] = date('Y-m-d',$data['startTime']);
			$data['endTime'] = $data['endTime'] ? date('Y-m-d',$data['endTime']) : '长期';

			$data['scope'] = "";
			$data['scope'] .= $data['hotel'] ? '' : '酒店端、';
			$data['scope'] .= $data['mobile'] ? '' : '用户端、';

			switch ($data['status'] ) {
				case 0:$data['status'] = '等待生效';break;
				case 1:$data['status'] = '已生效';break;
				default:$data['status']  = '已过期';break;

			}
			return $data;
		});
	}
	//添加系统消息界面
	public function newsInsert()
	{

		$date = date('Y-m-d');

		$this->assign('date',$date);
		$this->display();
	}

	//添加系统消息执行
	public function insertDo()
	{

		$_POST['createTime'] = time();
		$_POST['status'] = 0;

		if(strtotime(I('startTime'))<time()){
			$_POST['status'] = 1;
		}
		if(!I('hotel')&&!I('mobile')){
			$this->error('请选择发布范围');
		}else if(I('endTime')){
			if(strtotime(I('startTime'))>=strtotime(I('endTime'))){
				$this->error('截止日期不能小于开始日期');
			}			
		}else if(!I('title')||!I('body')){
			$this->error('请输入消息标题和内容');
		}
		$Ary = [
			'hotel'  => I('hotel') ? 0 : 1,
			'mobile' => I('mobile') ? 0 : 1,
			'startTime' => strtotime(I('startTime')),
			'endTime' => strtotime(I('endTime')),
			'title' => I('title'),
			'status' => I('status'),
			'createTime' => time(),
			'body' => I('body')
		];

		$news_id = D::add('news',$Ary);

		if($Ary['hotel']==0){

			$hotel_id = D::get('hotels',[
							'field' => 'id',
							'where' => 'status!=9'
						]);
			foreach ($hotel_id as $key => $row) {
				$Array = [
					'news' => $news_id,
					'hotel' => $row['id'],
					'status' => 0,
				];
				D::add('news_hotel',$Array);				
			}
		}
		if($Ary['mobile']==0){
			$users_id = D::get('users',[
					'field' => 'id',
					'where' => 'status!=9'
				]);

			foreach ($users_id as $key => $usersRow) {
				$Array = [
					'news' => $news_id,
					'users' => $usersRow['id'],
					'status' => 0,
				];
				D::add('news_user',$Array);				
			}

		}

		$this->success('发布成功！',U('SystemNews/index'));
	}

	//立即生效
	public function takeEffect()
	{
		$Ary = [
			'startTime' => strtotime(date('Y-m-d')),
			'status' => 1
		];

		if(D::save('News',I('id'),$Ary)){
			$this->success('操作成功！',U('SystemNews/index'));
		}else{
			$this->error('操作失败，请重试！');
		}
	}

	//查看消息详情
	public function newsinfo()
	{
		$data = D::find('News',I('id'));

		$data['startTime'] = date('Y-m-d',$data['startTime']);
		$data['endTime'] = $data['endTime'] ? date('Y-m-d',$data['endTime']) : '长期';

		$data['scope'] = "";
		$data['scope'] .= $data['hotel'] ? '' : '酒店端、';
		$data['scope'] .= $data['mobile'] ? '' : '用户端、';

		switch ($data['status'] ) {
			case 0:$data['status'] = '等待生效';break;
			case 1:$data['status'] = '已生效';break;
			default:$data['status']  = '已过期';break;

		}	

		$this->assign('data',$data);
		$this->display();			
	}
	//删除系统消息
	public function delNews()
	{
		$Ary = [
			'status' => 9
		];

		D::delete('news_hotel','news='.I('id').'');
		
		if(D::save('News',I('id'),$Ary)){
			$this->success('删除成功！',U('SystemNews/index'));
		}else{
			$this->error('删除失败');
		}
	}
	//修改系统消息
	public function updateNews()
	{
		$data = D::find('News',I('id'));

		$data['startTime'] = date('Y-m-d',$data['startTime']);
		$data['endTime'] = $data['endTime'] ? date('Y-m-d',$data['endTime']) : '长期';

		$data['scope'] = "";
		$data['scope'] .= $data['hotel'] ? '' : '酒店端、';
		$data['scope'] .= $data['mobile'] ? '' : '用户端、';

		switch ($data['status'] ) {
			case 0:$data['status'] = '等待生效';break;
			case 1:$data['status'] = '已生效';break;
			default:$data['status']  = '已过期';break;

		}		

		$this->assign('data',$data);
		$this->display();
	}
	//修改系统信息执行
	public function updateDo()
	{
		$_POST['status'] = 0;

		if(strtotime(I('startTime'))<time()){
			$_POST['status'] = 1;
		}
		if(!I('hotel')&&!I('mobile')){
			$this->error('请选择发布范围');
		}else if(I('endTime')){
			if(strtotime(I('startTime'))>=strtotime(I('endTime'))){
				$this->error('截止日期不能小于开始日期');
			}			
		}else if(!I('title')||!I('body')){
			$this->error('请输入消息标题和内容');
		}

		$Ary = [
			'hotel'  => I('hotel') ? 0 : 1,
			'mobile' => I('mobile') ? 0 : 1,
			'startTime' => strtotime(I('startTime')),
			'endTime' => strtotime(I('endTime')),
			'title' => I('title'),
			'status' => I('status'),
			'body' => I('body')
		];	

		if(D::save('News',I('id'),$Ary)!==false){
			$this->success('更新成功！',U('SystemNews/index'));
		}else{
			$this->error('更新失败，请重试');
		}

	}
	//导出系统信息
	public function export()
	{
		$db = parent::index(true);

		foreach ($db as $key => $data) {

			$db[$key]['startTime'] = date('Y-m-d',$data['startTime']);
			$db[$key]['endTime'] = $data['endTime'] ? date('Y-m-d',$data['endTime']) : '长期';

			$db[$key]['scope'] = "";
			$db[$key]['scope'] .= $data['hotel'] ? '' : '酒店端、';
			$db[$key]['scope'] .= $data['mobile'] ? '' : '用户端、';

			switch ($data['status'] ) {
				case 0:$db[$key]['status'] = '等待生效';break;
				case 1:$db[$key]['status'] = '已生效';break;
				default:$db[$key]['status']  = '已过期';break;

			}	
		}

		$dbName = array(
			array('title','标题'),
			array('startTime','生效时间'),
			array('endTime','截止时间'),
			array('scope','发布范围'),
			array('body','消息内容'),
			array('status','状态')
			);

		$excelName = '系统消息列表_'.date('Ymd').'';

		export_Excel($excelName,$dbName,$db);
	}
}
