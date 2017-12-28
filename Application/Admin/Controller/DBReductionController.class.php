<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Faster;
//系统管理
class DBReductionController extends CommonController {
    //还原数据库
    public function index()
    {       
        A('Database')->index('import');

        $this->display();
    }
}
