<?php
namespace Cashier\Controller;
use Think\Controller;
//Admin模块>>Cash控制层
class CashController extends Controller {
	//用户登录状态校验
	public function loginCheck($t) {
		if(public_user_type()!='cashier'){
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
	
	public function recharge(){
		$this->loginCheck(2);
		$uid = I('post.uid');						//充值用户
		$rechargeAmt = I('post.amt');		//充值数量
		$rechargeUnit = I('post.unit');	//单位h,d,m
		$cash = I('post.cash');
		if($uid!=null && $rechargeAmt!=null && $rechargeUnit!=null){
			$model_account = D('account');
			if($model_account->rechargeAccount($uid,$rechargeAmt,$rechargeUnit)){
				$rs = array(
					'status'=>1,
					'msg'=>'成功为'.$uid.'充值成功'.$rechargeAmt.$rechargeUnit,
				);
				
				$model = D('task');
				
				//订单记录插入订单表中
				$model_order = M('orderlist');
				$order = array(
					'id'=>null,
					'uid'=>$uid,
					'otime'=>date('Y-m-d H:i:s'),
					'pid'=>'0',
					'cash'=>(float)$cash,
					'trano'=>null,
					'tradesatus'=>'success',
					'remark'=>'客服充值'.$rechargeAmt.$rechargeUnit,
					'status'=>1,
					'operator'=>public_user_id()
				);
				$model_order->add($order);
			}else{
				$rs = array(
					'status'=>0,
					'msg'=>'为'.$uid.'充值失败！'
				);
			}
		}else{
			$rs = array(
				'status'=>0,
				'msg'=>'非法参数！'
			);
		}
		$this->ajaxReturn($rs);
	}
	
	function test(){
		header("Content-type: text/html; charset=utf-8"); 
		$model = D('task');
		try{
			$model->execAllTask();
		}catch(Exception $e){
			echo $e;
		}
	}
}