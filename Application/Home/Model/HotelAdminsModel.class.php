<?php
namespace Home\Model;
use Think\Model;

class HotelAdminsModel extends Model {
    protected $_validate = [
        ['oldpwd','require','原密码不能为空'],
        ['oldpwd','check_old','原密码错误',0,'callback',2],
        ['password','require','请填写新密码'],
        ['confirm_password','password','密码两次输入不一致',0,'confirm'],
    ];
    function check_old(){
        $data = array();
        $data['id']= I('id');
        $data['password'] = md5(I('oldpwd'));
        $is = M('HotelAdmins')->where($data)->find();
        if($is){
            return true;
        }else{
            return false;
        }
    }
}