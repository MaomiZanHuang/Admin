<?php
	/****公共函数****/
	// 应用id
	function public_charge_prefix() {
		return 'jy';
	}
	function public_charge_notifyurl() {
		return 'http://47.95.121.210/qwt/Home/WxPay/notifyurl';
	}
	//获取用户登录类型
	function public_user_type(){
		return session('telanx.type');
	}
	// 登录用户的id
	function public_user_uid(){
		return session('telanx.uid');
	}
	//登录用户名
	function public_user_id(){
		return session('telanx.user');
	}
	//创建时间
	function public_user_ctime(){
		return session('telanx.ctime');
	}
	
	// 部分业务黑名单列表
	function public_black_list() {
		return '';
	}
?>
