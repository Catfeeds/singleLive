<?php
namespace Mobile\Controller;
use Think\Controller;
use Think\D;
class UploadPhotoController extends CommonController{
	public static $login = false;
	//上传头像   没有提交按钮所以要在这里去直接更新数据库了
	public function upload_Photo()
	{
		$upload = new \Think\Upload();								// 实例化上传类
		$upload->maxSize  = 9437184;		// 设置附件上传大小(6M)
		$upload->exts     =  array('jpg', 'gif', 'png', 'jpeg');	    // 设置附件上传类型
		$upload->rootPath = './Uploads/';							// 设置附件上传根目录
		$upload->savePath = '/Uploads/';										// 设置附件上传（子）目录
		// 上传文件
		$info = $upload->upload();
		if(!$info) {
			// 上传错误提示错误信息
			$this->ajaxReturn($upload->getError(),'json');die;
		}else{
			// 上传成功并保存到数据库中
			$doc = D::add(('files'),$info['file']);
			if ($doc) {
				$data['info'] = "上传成功";
				D::set('Users.headImg',I('uid'),$doc);
			}else {
				$data['info'] = "上传失败，请重新上传";
			}
			$data['id'] = $doc;
			$data['code'] = 0;
			$data['data'] = [
				'src' => '/Uploads'.$info['file']['savepath'].$info['file']['savename'],
				'title' => $info['file']['name'],
			];
		}
		$this->ajaxReturn($data,'json');
	}
}