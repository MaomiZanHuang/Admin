<?php
$APP = '/mzz';
return array(
	//'配置项'=>'配置值'
	//防止sql注入
	'DEFAULT_FILTER'        =>  'strip_tags',
	//模板替换
	'TMPL_PARSE_STRING'=>array(
		'__APPNAME__'=>'云梯',
		'__LOGO__'=>$APP.'/Public/img/logo.png',
		'__YEAR__'=>'2018',
		'__FOOTER__'=>'&#169;云梯 2018',
		'__MENU_TIPS_ADMIN__'=>'云梯后台管理',
		'__MENU_TIPS_USER__'=>'充值请联系客服',
		'__TEST_URL__'=>'/telanx/test.php',
		
		'__PUBLIC__'=>$APP.'/Public',
		'__LIB__'=>$APP.'/Public/lib',
		'__JS__'=>$APP.'/Public/js',
		'__CSS__'=>$APP.'/Public/css',
		'__IMG__'=>$APP.'/Public/img'
	)
);
