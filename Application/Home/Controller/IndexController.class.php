<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller{
	public function index(){
		$this->display();
	}
	
	//清除sess，避免登录出现账号已经存在的问题
	public function sess() {
		$uid = I('post.user');
		$token = I('post.token');
		if($uid!=null){
			if($token=='telanx'){
				$model = M('user_user');
				$data = array(
					'sess'=>null
				);
				$model->where("user='".$uid."'")->save($data);
				$this->assign("msg","操作成功！");
			}else{
				$this->assign("msg","口令不正确！");
			}
		}
		$this->display();
	}
	
}

