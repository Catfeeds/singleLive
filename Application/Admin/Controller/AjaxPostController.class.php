<?php
namespace Admin\Controller;

use Think\Controller;
use Think\D;

class AjaxPostController extends CommonController
{

    //上传图片
    public function uploadOne()
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
            }else {
                $data['info'] = "上传失败，请重新上传";
            }
            $data['id'] = $doc;
            $data['code'] = 0;
            $data['data'] = [
                'src' => '/Uploads/'.$info['file']['savepath'].$info['file']['savename'],
                'title' => $info['file']['name'],
            ];
        }
        $this->ajaxReturn($data,'json');
    }

    //富文本编辑器-上传接口
    public function uploadEdit()
    {
        $file = uploadOne([
            'savePath' => '/hands/',
            'saveName' => array('uniqid', ''),
            'exts' => array('jpg', 'gif', 'png', 'jpeg'),
            'autoSub' => true,
            'subName' => array('date', 'Ymd'),
        ]);
        if ($file) {
            $file['code'] = 0;
            $file['data'] = [
                'src' => '/Uploads/' . $file['savepath'] . $file['savename'],
                'title' => '',
            ];
        } else {
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
        $config = C('UPLOAD_CONFIG') ? C('UPLOAD_CONFIG')
            : array(
                'maxSize' => 5242880,
                'rootPath' => './Uploads/',
                'savePath' => '/Uploads/',
                'saveName' => array('uniqid', ''),
                'exts' => array('jpg', 'gif', 'png', 'jpeg'),
                'autoSub' => true,
                'subName' => array('date', 'Ymd'),
            );//默认上传配置
        $upload = new \Think\Upload($config);
        $info = $upload->uploadOne($_FILES['file']);
        if (!$info) {// 上传错误提示错误信息
            $info['status'] = 'false';
            $info['error'] = $upload->getError();
        } else {// 上传成功 获取上传文件信息\
            $id = D::add('Files', $info);
            $info['id'] = $id;
            $info['status'] = 'true';
        }
        $this->ajaxReturn($info);
    }
    /*
     *   删除图片---多图上传
     * */
    public function imgDel(){
        $table = I('table');
        $banner = M("$table");
        $img = $banner->where("id=".I('id'))->getField('imgs');
        $arr = explode(',',$img);
        unset($arr[array_search(I('fileId'),$arr)]);
        $str = implode(',',$arr);
        $num = $banner->where("id=".I('id'))->setField('imgs',$str);
        if($num>0){
            $info['status'] = 'yes';
        }else{
            $info['status'] = 'no';
        }
        $this->ajaxReturn($info);
    }
}