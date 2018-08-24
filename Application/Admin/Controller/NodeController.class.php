<?php
namespace Admin\Controller;
use Think\Controller;
//Admin模块>>Node控制层
class NodeController extends Controller {
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
		$this->assign('user', $user);
		$this->display();
	}
	
	
	
	//节点管理
	public function manage() {
		$this->loginCheck(1);
		$user = array(
			'user'=>public_user_id()
		);
		$this->assign('user',$user);
		$this->display();
	}
	/**
	* 获取节点信息
	* @params $id  节点id，为0表示获取所有
	* @return 节点信息
	*/
	public function getProxyServer() {
		$this->loginCheck(2);
		$id = I('get.id');
		$proxyserver_model = M('proxyserver');
		$rs = null;
		if(is_numeric($id)) {
			$rs = $proxyserver_model->where("id=".$id)->select();
		}else {
			$rs = $proxyserver_model->select();
		}
		$this->ajaxReturn($rs);
	}
	
	//节点设置
	public function settings() {
		$this->loginCheck(1);
		$user = array(
			'user'=>public_user_id()
		);
		$this->assign('user', $user);
		$this->display();
		
	}
	
	//添加节点
	public function add() {
		$this->loginCheck(1);
		$user = array(
			'user'=>public_user_id()
		);
		$this->assign('user', $user);
		if(count(I("post."))>0) {
			$auth = array(
				"user" => I('post.user'),
				"pwd" => I('post.pwd')
			);
			$uplen = mb_strlen($auth['user'], 'UTF8');
			$up = $auth['user'].$auth['pwd'].($uplen > 9 ? $uplen : '0'.$uplen);
			if (mb_strlen($auth['user'], 'UTF8') < 1) $up = "";
			$proxyServer = array(
				"id"=>null,
				"name"=>I("post.name"),
				"type" => I('post.type'),
				"ip" => I('post.ip'),
				"port" => I('post.port'),
				"spcode"=> I('post.type')." ".I('post.ip').":".I('post.port'),
				"country"=>I("post.country"),
				"remark"=>I('post.remark'),
				"up"=> $up,
				"status"=>"ZC"
			);
			$proxyserver_model = M("proxyserver");
			if($proxyserver_model->add($proxyServer)){
				$this->assign("msg","添加成功！");
			}else{
				$this->assign("msg","添加失败！");
			}
					
		}
		
		$this->display();
	}

	// 保存节点
	public function save() {
		$this->loginCheck(1);
		$id = I('get.id');
		if(count(I("post."))>0) {
			$auth = array(
				"user" => I('post.user'),
				"pwd" => I('post.pwd')
			);
			$uplen = mb_strlen($auth['user'], 'UTF8');
			$up = $auth['user'].$auth['pwd'].($uplen > 9 ? $uplen : '0'.$uplen);
			if (mb_strlen($auth['user'], 'UTF8') < 1) $up = "";
			$proxyServer = array(
				"id"=> $id,
				"name"=>I("post.name"),
				"type" => I('post.type'),
				"ip" => I('post.ip'),
				"port" => I('post.port'),
				"spcode"=> I('post.type')." ".I('post.ip').":".I('post.port'),
				"country"=>I("post.country"),
				"remark"=>I('post.remark'),
				"up"=> $up,
				"status"=>I('post.status')
			);
			$proxyserver_model = M("proxyserver");
			if($proxyserver_model->where('id='.$id)->save($proxyServer)){
				$this->assign("msg","保存成功！");
			}else{
				$this->assign("msg","保存失败！");
			}		
		}

		$proxyserver = M('proxyserver')->where('id='.$id)->select();
		if (count($proxyserver) < 1) {
			$this->error('非法操作，该节点不存在！', U('Admin/Node/index'));
		}
		$this->assign('server', json_encode($proxyserver[0]));
		$this->display();
	}
	
	//删除节点RS
	public function delRS(){
		$this->loginCheck(2);
		$id = I('get.id');
		$proxyserver_model = M('proxyserver');
		$result = 0;
		$msg = "参数错误！";
		if($id){
			if($proxyserver_model->where("id='".$id."'")->delete()){
				$result = 1;
				$msg = "成功！";
			}else{
				$result = 0;
				$msg = "失败！";
			}
		}
		$rs = array(
			"result"=>$result,
			"msg"=>$msg
		);
		$this->ajaxReturn($rs);
	}
	
}