<?php
namespace Admin\Controller;
use Think\Controller;
//Admin模块>>User控制层
class CardController extends Controller {
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
	
	// 查看卡密
	public function index() {
		$this->loginCheck(1);
		
		$user = array(
			'user'=>public_user_id()
		);
		$this->assign("user",$user);
		
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
		
		$model = D('Card');
		
		$rs = array(
			'status'=>1,
			'cp'=>$page,
			'tp'=>$model->searchCount($kw,$filter),
			'rs'=>$model->search($kw,$filter,$page)
			);
		$this->ajaxReturn($rs);
	}
	
	// 批量生成导入卡密
	public function add() {
		$this->loginCheck(1);
		
		// 获取积分分类
		$model = M('goods_spec');
		$charge_options = $model -> where('goods_id = 0')->select();
		$this->assign('charge_options', $charge_options);
		$this->display();
	}
	
	public function add_ajax() {
		$this->loginCheck(2);
		if(count(I('post.')) > 0) {
			$post_codes = I('post.codes');
			$codes = explode(';', $post_codes);
			$fee_type = I('post.fee_type');

			foreach ($codes as $code) {
				$data[] = array(
					'id' => null,
					'card_no' => $code,
					'charge_user' => null,
					'price' => 1.0 * $fee_type / 100,
					'gen_time' => date('Y-m-d H:i:s'),
					'activate_time' => null,
					'points' => $fee_type
				);
			}
			
			$model_card = M('card');
			$vd = $model_card->addAll($data);
			
			if($vd){
				$this->ajaxReturn(array(
					'code' => 1,
					'msg' => '成功添加'.count($data),
					'data' => $data
				));
			}else {
				$this->ajaxReturn(array(
					'code' => 0,
					'msg' => '添加失败！',
					'data' => $data
				));
			}
			return false;
		}
	}
	
	//批量删除卡密
	public function del_ajax(){
		$this->loginCheck(2);
		if(count(I('post.'))>0) {
			$user = I('post.user');
			$model_user_user = M('card');
			if($model_user_user->where("card_no='".$user."'")->delete()) {
				$rs = array(
					'status'=>1,
					'msg'=>'卡号'.$user.'已删除'
				);
			} else {
				$rs = array(
					'status'=>0,
					'msg'=>'卡号'.$user.'删除失败！'
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
}