<?php
	/****��������****/
	// Ӧ��id
	function public_charge_prefix() {
		return 'jy';
	}
	function public_charge_notifyurl() {
		return 'http://47.95.121.210/qwt/Home/WxPay/notifyurl';
	}
	//��ȡ�û���¼����
	function public_user_type(){
		return session('telanx.type');
	}
	// ��¼�û���id
	function public_user_uid(){
		return session('telanx.uid');
	}
	//��¼�û���
	function public_user_id(){
		return session('telanx.user');
	}
	//����ʱ��
	function public_user_ctime(){
		return session('telanx.ctime');
	}
	
	// ����ҵ��������б�
	function public_black_list() {
		return '';
	}
?>
