<?php
namespace Admin\Controller;
use Think\Controller;
//Admin模块>>User控制层
class UserController extends Controller {
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
	
	public function index() {
		$this->loginCheck(1);
		
		$user = array(
			'user'=>public_user_id()
		);
		$this->assign("user",$user);
		
		$this->display();
	}
	
	//添加用户界面
	public function add(){
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
	
	//批量删除用户
	public function del_ajax(){
		$this->loginCheck(2);
		if(count(I('post.'))>0) {
			$user = I('post.user');
			$model_user_user = M('user_user');
			if($model_user_user->where("user='".$user."'")->delete()) {
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
	
	//修改用户
	public function modify() {
		$this->loginCheck(1);
		
		$user = I('get.user');
		$model_user_user = M('user_user');
		if(count(I('post.'))>0) {
			$data = I('post.');
			if($model_user_user->where("user='".$data['user']."'")->save($data)){
				$msg = "修改成功！";
			} else {
				$msg = "修改失败！";
			}
			
			$this->assign("msg",$msg);
			
		}
		$userData = $model_user_user->where("user='".$user."'")->select();
		$this->assign("user",$userData[0]);
		$this->display();
	}
	
	public function getUser(){
		$this->loginCheck(2);
		$user = I('get.user');
		$error = array(
			"result"=>0,
			"msg"=>"非法操作"
		);
		$model = new \Think\Model();
		$sql = "select A.user, A.qq, B.points from user_user A left join user_balance B on A.user=B.user where A.user='%s'";
		$this->ajaxReturn($model->query($sql, $user));
	}
}