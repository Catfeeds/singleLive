<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\D;
class CollectionController extends MobileCommonController {
	public $model = 'Hotels';
	public function _map(&$data)
	{
		/*8.23增加*/
		$ids = D::lists('Order','hotel',['userId' =>session('user.id')]);
		$ids = array_unique($ids);
		/*8.23增加*/
		$data = [
			'where' => ['status'=>0 /*8.23增加*/, 'id' => ['in',$ids]/*8.23增加*/],
		];
	}
	/**
	 * [index description]
	 * @Author   尹新斌
	 * @DateTime 2017-07-18
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function index()
	{
		if (IS_AJAX) {
			$myHotel = D::lists('Collection','hotel',['user'=>session('user.id')]);
			parent::index(function($data)use($myHotel){
				$data['head'] = getSrc($data['head']);
				$data['id64'] = base64_encode($data['id']);
				if (in_array($data['id'], $myHotel)) {
					$data['img'] = 'sc2';
				}else{
					$data['img'] = 'sc';
				}
				return $data;
			});
		}else{
			$this->display();
		}
	}
	/**
	 * [like 收藏判断]
	 * @Author   尹新斌
	 * @DateTime 2017-07-18
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function like()
	{
		$myHotel = D::find('Collection',[
			'user'=>session('user.id'),
			'hotel' => I('id'),
			]);
		if ($myHotel) {
			//已收藏
			D::delete('Collection',$myHotel['id']);//删除收藏
			$data = ['img' => 'sc','msg'=>'已取消收藏'];
		}else{
			//未收藏
			D::add('Collection',[
			'user'=>session('user.id'),
			'hotel' => I('id'),
			]);
			$data = ['img' => 'sc2','msg'=>'已收藏'];
		}
		$this->ajaxReturn($data);
	}
}