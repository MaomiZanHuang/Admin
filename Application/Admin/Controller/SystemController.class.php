<?php
namespace Admin\Controller;
use Think\Controller;
//Admin模块>>System控制层
class SystemController extends Controller {
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
		$user = array(
			'user'=>public_user_id()
		);
		$model_config = M("config");
		$crxUpdate = I('post.');
		if(count($crxUpdate)>0){
			$crxData = array(
				'k'=>'crx',
				'v'=>json_encode($crxUpdate)
			);
			if($model_config->where("k='crx'")->save($crxData)){
				$this->assign("msg","保存成功！");
			}else{
				$this->assign("msg","保存失败！");
			}
		}
		
		$crxD = $model_config->where("k='crx'")->select();
		$crx = json_decode($crxD[0]['v'],true);
		$this->assign("crx",$crx);
		$this->assign('user', $user);
		$this->display();
	}
	
	//配置服务器，客服qq，会员类型
	public function server(){
		$model = M('config');
		$kfm = $model->where("k='kf'")->select();
		if(count($kfm)>0)$kfs = json_decode($kfm[0]['v'],true);
		$this->assign('kfs',$kfs);
		$this->display();
	}
	
	//更新客服信息
	public function kf(){
		$this->loginCheck(2);
		$v = I('post.data');
		$model_config = M('config');
		$data = array(
			'v'=>json_encode($v)
		);
		if($model_config->where("k='kf'")->save($data)){
			$rs = array(
				'status'=>1,
				'msg'=>'更新成功！'
			);
		}else{
			$rs = array(
				'status'=>0,
				'msg'=>'更新失败！'
			);
		}
		$this->ajaxReturn($rs);
	}
	
}