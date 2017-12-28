<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;
class AjaxPostController extends CommonController {
	/**
	 * [uploadOne 上传单一文件]
	 * @Author   尹新斌
	 * @DateTime 2017-07-12
	 * @Function []
	 * @return   [type]     [description]
	 */
	public function uploadOne()
	{
		$this->ajaxReturn(uploadOne([
			'savePath' => '/hands/',
			'saveName' => array('uniqid',''),
			'exts'     => array('jpg', 'gif', 'png', 'jpeg'),
			'autoSub'  => true,
			'subName'  => array('date','Ymd'),
			]));
	}
    /**
     * [uploadEdit layui富文本上传图片专用]
     * @Author   股没动
     * @DateTime 2017-10-30
     * @Function []
     * @return   [type]     [description]
     */
    public function uploadEdit()
    {
      $file =  uploadOne([
            'savePath' => '/hands/',
            'saveName' => array('uniqid',''),
            'exts'     => array('jpg', 'gif', 'png', 'jpeg'),
            'autoSub'  => true,
            'subName'  => array('date','Ymd'),
            ]);

      if($file){
         $file['code'] = 0;
         $file['data'] = [
            'src' => '/Uploads/'.$file['savepath'].$file['savename'],
            'title' => '',
         ];
      }else{
         $file['code'] = 1;   
         $file['msg'] = '未知错误，请重新上传';     

      }

      $this->ajaxReturn($file);
    }
	/*
		批量上传
	 */
	public function zyupload()
    {
        $config = C('UPLOAD_CONFIG')?C('UPLOAD_CONFIG')
                  :array(
                    'maxSize'  => 5242880,
                    'rootPath' => './Uploads/',
                    'savePath' => '/Uploads/',
                    'saveName' => array('uniqid',''),
                    'exts'     => array('jpg', 'gif', 'png', 'jpeg'),
                    'autoSub'  => true,
                    'subName'  => array('date','Ymd'),
                    );//默认上传配置
        $upload = new \Think\Upload($config);
        $info   = $upload->uploadOne($_FILES['file']);
        if(!$info) {// 上传错误提示错误信息
            $info['status'] = 'false';
            $info['error'] = $upload->getError();
        }else{// 上传成功 获取上传文件信息\
        	// dump($info);die;
    		$id = D::add('Files',$info);
			$info['id'] = $id;
            $info['status'] = 'true';
        }
        $this->ajaxReturn($info);
    }
}