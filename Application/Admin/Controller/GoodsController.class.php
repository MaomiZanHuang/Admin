<?php
namespace Admin\Controller;
use Think\Controller;
//Admin模块>>User控制层
class GoodsController extends Controller {
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
			$pics = I('post.pics');
			$specs = I('post.specs');
			if (strcmp(trim($pics,'\r\n'), '') !== 0) {
				$pics = explode(PHP_EOL, $pics);
				$pics = json_encode($pics);
			} else {
				$pics = null;
			}
			$specs = explode(PHP_EOL, $specs);
			$goods = array(
				'id' => null,
				'cata_id'=>(int)I('post.cata_id'),
				'goods_id'=>I('post.goods_id'),
				'logo'=>I('post.logo'),
				'title'=>I('post.title'),
				'pics'=> $pics,
				'detail'=> I('post.detail'),
				'api_method'=> I('post.api_method'),
				'api_host'=> I('post.api_host'),
				'sort_index' => ((int)I('post.goods_id')) % 100,
				'api_fixed_params' => I('post.api_fixed_params'),
				'api_extra_params' => I('post.api_extra_params'),
				'business_cata' => I('post.business_cata'),
				'online' => 1
			);

			// 解析规格
			$specs_new = array();
			foreach($specs as $spec) {
				$r = explode('/', $spec);
				$spec = array(
					'id' => null,
					'goods_id' => I('post.goods_id'),
					'title' => $r[0],
					'amt' => $r[1],
					'rmb' => $r[2],
					'points' => $r[3]
				);
				array_push($specs_new, $spec);
			}

			$a = M('goods_spec')->addAll($specs_new);
			$b = M('goods_item')->add($goods);
			
			if($a && $b){
				$this->show("<h1>添加成功！</h1>");
			}else {
				$error = "<h1>".($b ? "商品已添加！" : "商品添加失败！")."</h1>";
				$error = $error. "<h1>".($a ? "规格已添加！" : "规格添加失败！")."</h1>";
				$this->show($error);
			}
		}
		$model = M('goods_cata');
		$catas = $model->select();
		$this->assign('catas', $catas);
		$this->display();
	}
	
	//批量删除用户
	public function del_ajax(){
		$this->loginCheck(2);
		if(count(I('post.'))>0) {
			$user = I('post.user');
			$model_user_user = M('goods_item');
			if($model_user_user->where("id='".$user."'")->delete()) {
				$rs = array(
					'status'=>1,
					'msg'=>'商品'.$user.'已删除'
				);
			} else {
				$rs = array(
					'status'=>0,
					'msg'=>'商品'.$user.'删除失败！'
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
		
		$user = I('get.id');
		$model = M('goods_item');
		if(count(I('post.'))>0) {
			$data = I('post.');
			if($model_user_user->where("user='".$data['user']."'")->save($data)){
				$msg = "修改成功！";
			} else {
				$msg = "修改失败！";
			}
			
			$this->assign("msg",$msg);
			
		}
		$userData = $model->where("id='".$user."'")->find();

		$model_catas = M('goods_cata');
		$catas = $model_catas->select();
		$this->assign('catas', $catas);
		$this->assign("goods", json_encode($userData));
		$this->display();
	}
	
	public function getUser(){
		$this->loginCheck(2);
		$user = I('get.user');
		$error = array(
			"result"=>0,
			"msg"=>"非法操作"
		);
		$model_user_user = M("user_user");
		$model_account = M("account");
		if($user!=null){
			$userD = $model_user_user->where("user='".$user."'")->select();
			$userA = $model_account->where("uid='".$user."'")->select();
			if(count($userA)>0){
				
				$userD[0]['expiredate'] = $userA[0]['expiredate'];
			}else if($userD){
				$userD[0]['expiredate'] = '未充值';
			}
			$this->ajaxReturn($userD);
		}else{
			$this->ajaxReturn($error);
		}
	}

	public function specs() {
		$goods_id = I('get.goods_id');
		if ($goods_id) {
			$model = M('goods_spec');
			$specs = $model->where("goods_id = '%s'", $goods_id) ->select();
			$this->assign('specs', $specs);
		}
		$this->assign('goods_id', $goods_id);
		$this->display();
	}

	// 删除指定的spec
	public function delSpec() {
		$id = I('post.id');
		$spec_model = D('Specs');
		if ($spec_model->delSpec($id)) {
			$msg = array(
				'status' => 1,
				'msg' => '删除成功！'
			);
		} else {
			$msg = array(
				'status' => 0,
				'msg' => '删除失败！'
			);
		}
		$this->ajaxReturn($msg);
	}

	// 新加spec
	public function addSpec() {
		$goods_id = I('post.goods_id');
		$data = I('post.spec');
		$spec_items = explode('/', $data);
		if (count($spec_items) !== 4) {
			return $this->ajaxReturn(array(
				status => 0,
				msg => '数据格式不正确！'
			));
		}
		$title = $spec_items[0];
		$amt = (int) $spec_items[1];
		$rmb = (float) $spec_items[2];
		$points = (float) $spec_items[3];
		$spec_new = array(
			goods_id => $goods_id,
			title => $title,
			amt => $amt,
			rmb => $rmb,
			points => $points
		);
		if (D('Specs')->addSpec($spec_new)) {
			$msg = array(
				status => 1,
				msg => '添加成功！'
			);
		} else {
			$msg = array(
				status => 0,
				msg => '添加失败！'
			);
		}
		$this->ajaxReturn($msg);
	}

	// 更新Spec
	public function updateSpec() {
		$spec = I('post.');
		$id = I('post.id');
		if (D('Specs')->updateSpec($id, $spec)) {
			$msg = array(
				status => 1,
				msg => '更新成功！'
			);
		} else {
			$msg = array(
				status => 0,
				msg => '更新失败！'
			);
		}
		$this->ajaxReturn($msg);
	}
}