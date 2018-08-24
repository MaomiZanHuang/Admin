<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
	//身份验证
	public function loginCheck($t) {
		if(public_user_type()!='admin'){
			if($t==1){
				//直接跳转
				$this->error('请先登录后操作！',U('Admin/Login/index'),3);
			}
			else if($t==2){
				//ajax返回
				$msg=array(
					'status'=>0,
					'msg'=>'未登录，无法操作！'
				);
				$this->ajaxReturn($msg);
			}
		}
	}
	
	//开始，获取需要执行的同步的任务
  public function index() {
		$this->loginCheck(1);
		$this->display();
  }
}