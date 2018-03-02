<?php
return array(
 	'LAYOUT_ON'   => true,
 	'LAYOUT_NAME' => 'Public/layout',
	'PAGE_NUMBER' => 9,
	'PAGE_CONFIG'  => array(
        'prev'   => '<',
        'next'   => '>',
        'last'   => '尾页',
        'first'  => '首页',
        'theme'  => '%UP_PAGE% %FIRST% %LINK_PAGE% %END% %DOWN_PAGE% %JUMP%',
	),
	'TMPL_PARSE_STRING' => array(
		'__IMAGES__' => '/Public/Home/img',
		'__CSS__' => '/Public/Home/css',
		'__JS__' => '/Public/Home/js'
	),

);