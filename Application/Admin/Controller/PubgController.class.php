<?php
namespace Admin\Controller;
use Think\Controller;
//Admin模块>>User控制层
class PubgController extends Controller {
	//用户登录状态校验
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
	
	// 吃鸡申诉管理
	// 首页是查看吃鸡账号情况
	public function index() {
		$this->loginCheck(1);
		
		$user = array(
			'user'=>public_user_id()
		);
		$this->assign("user",$user);
		
		$this->display();
	}
	
	
	// 批量生成导入卡密
	public function add() {
		$this->loginCheck(1);
		if(count(I('post.')) > 0) {
			$user = array(
				'id'=>null,
				'user'=>I('post.user'),
				'email'=>I('post.email'),
				'pwd'=>I('post.pwd'),
				'ip'=>'',
				'time'=>time(),
				'status'=>0,
				'sess'=>''
			);
			$model_user_user = M('user_user');
			$vd = $model_user_user->where("user='".$user['user']."'")->count();
			if((int)$vd>0){
				$this->show("用户名".$user['user']."已存在！无法添加！");
				return;
			}
			if($model_user_user->add($user)){
				$this->show("<h1>添加成功！</h1>");
			}else {
				$this->show("<h1>添加失败！</h1>");
			}
			return false;
		}
		$this->display();
	}
	
	//批量删除卡密
	public function del_ajax(){
		$this->loginCheck(2);
		if(count(I('post.'))>0) {
			$user = I('post.user');
			$model_user_user = M('chiji_account');
			if($model_user_user->where("steam_id='".$user."'")->delete()) {
				$rs = array(
					'status'=>1,
					'msg'=>'用户'.$user.'已删除'
				);
			} else {
				$rs = array(
					'status'=>0,
					'msg'=>'用户'.$user.'删除失败！'
				);
			}
		} else {
			$rs = array(
				'status'=>0,
				'msg'=>'非法操作！'
			);
		}
		$this->ajaxReturn($rs);
		
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
		
		$model = D('Pubg');
		
		$rs = array(
			'status'=>1,
			'cp'=>$page,
			'tp'=>$model->searchCount($kw,$filter),
			'rs'=>$model->search($kw,$filter,$page)
			);
		$this->ajaxReturn($rs);
	}
}