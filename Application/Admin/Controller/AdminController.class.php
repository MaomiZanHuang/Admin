<?php
namespace Admin\Controller;
use Think\Controller;
class AdminController extends Controller {
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
	//用户管理
	public function user(){
		if(public_user_type()!='admin'){
			return $this->error("请先登录",U('Admin/Index/index'));
		}
		$user = array(
			'user'=>public_user_id()
		);
		$this->assign('user',$user);
		$this->display();
		
	}
	
	/*搜索用户返回结果
	*请求方式：POST
	*传入参数keywords,过滤类别
	*返回用户基本信息
	*/
	public function search_handler(){
		if(public_user_type()!='admin'){
			$rs = array(
				'status'=>0,
				'msg'=>'未登陆，无法请求！'
			);
			return $this->ajaxReturn($rs);
		}
		$kw = I('post.keywords');
		$filter = I('post.filter');
		$page = I('post.page');		//当前页
		
		$model = D('Goods');
		
		$rs = array(
			'status'=>1,
			'cp'=>$page,
			'tp'=>$model->searchCount($kw,$filter),
			'rs'=>$model->search($kw,$filter,$page)
			);
		$this->ajaxReturn($rs);
	}
}