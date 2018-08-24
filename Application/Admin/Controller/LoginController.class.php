<?php
namespace Admin\Controller;
use Think\Controller;
//管理员模块>>登录控制器
class LoginController extends Controller {
	//登录
  public function index(){
		if (urlencode(cookie('key')) != urlencode('降龙十八掌')) {
			return $this->display('404');
		}
		if(I('post.user')){
				$model_login = D('Login');
				$rs = $model_login->login(I('post.'));
				if($rs['status']){
					if (public_user_type() == 'admin') {
						return $this->success('登录成功！',U('Admin/Index/index'));
					} else if (public_user_type() == 'cashier') {
						return $this->success('登录成功！', U('Cashier/Index/index'));
					}
				}
				$this->assign('error',$rs['msg']);
			}
    $this->display();    
  }
	public function hacked () {
		cookie('key', '降龙十八掌',3600*24*30);
		return $this->display('404');
	}
	//退出登录
	public function logout() {
		session('[destroy]'); 
		$this->success('登出成功！',U('Admin/Login/index'));
	}
}