<?php
return array(
	'TMPL_PARSE_STRING' => array(
		'__CSS__'    => '/Public/Admin/css',
		'__JS__'     => '/Public/Admin/js',
		'__IMAGES__' => '/Public/Admin/images',
		'__ADMIN__'  => '/Public/Admin',
	),
    'LAYOUT_ON'=>true,
	'LAYOUT_NAME'=>'/Public/layout',

	'PAGE_NUMBER' => 10,
	/// 'URL_ROUTE_RULES' => array('cate/:d\d'=>'Index/index');
	'DB_PATH_NAME'=> 'db',        //备份目录名称,主要是为了创建备份目录
	'DB_PATH'     => './db/',     //数据库备份路径必须以 / 结尾；
	'DB_PART'     => '20971520',  //该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M
	'DB_COMPRESS' => '1',         //压缩备份文件需要PHP环境支持gzopen,gzwrite函数        0:不压缩 1:启用压缩
	'DB_LEVEL'    => '4',         //压缩级别   1:普通   4:一般   9:最高
		//默认错误跳转对应的模板文件
	'TMPL_ACTION_ERROR'   => 'Public:error',
	// //默认成功跳转对应的模板文件
	'TMPL_ACTION_SUCCESS' => 'Public:success',
//=================
	'PAGE_CONFIG'  => array(
            'prev'   => '<',
            'next'   => '>',
            'last'   => '尾页',
            'first'  => '首页',
            'theme'  => '%UP_PAGE% %FIRST% %LINK_PAGE% %END% %DOWN_PAGE% %JUMP%',
            ),
//>>>>>>> 18503d51998a4cc6f7adc9192b5ecde1825ad11f
);