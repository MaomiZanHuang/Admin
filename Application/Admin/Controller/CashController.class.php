<?php
namespace Admin\Controller;
use Think\Controller;
//Admin模块>>Cash控制层
class CashController extends Controller {
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

		$chargeOptions = M('goods_spec')->where("goods_id='0'")->select();

		$this->assign('user', $user);
		$this->assign('chargeOptions', $chargeOptions);
		$this->display();
	}
	
	public function recharge(){
		$this->loginCheck(2);
		$user = I('post.user');						// 充值用户
		$rechargeAmt = (int)I('post.points');		// 充值数量
		$rechargeCard = I('post.card');	// 卡密充值的
		$charge_model = D('Balance');


		if (!$user) {
			return $this->ajaxReturn(array(
				status => 0,
				msg => '用户不能为空！'
			));
		}
		// 优先根据卡密充值
		if ($rechargeCard) {
			$card_model = M('card');
			$match_card = $card_model->where("card_no='%s' and charge_user is NULL", $rechargeCard)->find();
			if (!$match_card) {
				return $this->ajaxReturn(array(
					status => 0,
					msg => '卡密不存在或已充值！'
				));
			}
			$points = $match_card['points'];
			
			if (!$charge_model->chargeUser($user, $points, '卡密充值+'.$points.'积分')) {
				$this->ajaxReturn(array(
					status => 0,
					msg => '卡密绑定充值失败！'
				));
			}
			// 激活卡号
			$card_model->where("card_no='%s'", $rechargeCard)->setField(array(
				charge_user => $user,
				activate_time => date('Y-m-d H:i:s')
			));

			$this->ajaxReturn(array(
				status => 1,
				msg => '用户'.$user.'积分+'.$points
			));
		}

		// 手动充值
		$rs = array(
			status => 0,
			msg => '充值失败！'
		);
		if ($charge_model->chargeUser($user, $rechargeAmt, '客服充值+'.$rechargeAmt.'积分')) {
			$rs = array(
				status => 1,
				msg => $user.'充值+'.$rechargeAmt.'积分成功！'
			);
		}
		$this->ajaxReturn($rs);
	}
}