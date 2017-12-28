<?php
namespace Admin\Controller;
use Think\Controller;
use Think\D;
//系统管理
class DBController extends CommonController {

        public function index()
        {
            A('Database')->index('export');

            $this->display();
        }
}
