<?php
namespace Admin\Controller;
use Think\Controller;
class CopyController extends CommonController {
	public function index()
	{
		A('Database')->index('import');

		//$this->display();
	}
}