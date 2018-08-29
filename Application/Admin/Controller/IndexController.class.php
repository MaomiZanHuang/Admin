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
		$this->loginCheck(1);
		$user = array(
			'user'=>public_user_id()
		);

		// 当日收入分类
		$todayIncome = D('Orderlist')->getTodayIncome();

		$this->assign("user",$user);
		$userCount = D('User')->countUser();
		$incomeList = D('Orderlist')->getIncomeList();

		// 用户报表
		$this->assign('userCount', json_encode($userCount));

		$this->assign('todayIncome', json_encode($todayIncome));
		// 收入报表
		$this->assign('incomeList', json_encode($incomeList));
		
		$this->assign('totalIncome', D('Orderlist')->getTotalIncome());

		$this->display();
  }
}