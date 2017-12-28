<?php
namespace Admin\Controller;
use Think\Controller;
class PasteController extends CommonController {
	public function index()
	{
		A('Database')->index('import');
	}
}