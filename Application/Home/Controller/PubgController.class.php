<?php
namespace Home\Controller;
use Think\Controller;
class PubgController extends Controller {
		// 注册or登录
		public function login(){
			$steamId = I('post.steam_id');
			$email = I('post.email');
			if(!preg_match('/^\d{17}$/', $steamId)) {
				$this->ajaxReturn(array(
					'code' => 0,
					'msg' => 'steam账号不正确！'
				));
				return false;
			}
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$this->ajaxReturn(array(
					'code' => 0,
					'msg' => '邮箱不正确！'
				));
				return false;
			}
			// 注册或者登录
			$model_pubg = D('Pubg');
			if (!$model_pubg->checkUser($steamId)) {
				// 进行账号注册
				if ($model_pubg->reg($steamId, $email)) {
					$rs = array(
						'code' => 1,
						'msg' => '注册成功！',
						'remain' => 0
					);
				} else {
					$rs = array(
						'code' => 0,
						'msg' => '注册失败！',
						'remain' => 0
					);
				}
			} else {
				// 验证账号密码
				$r = $model_pubg ->login($steamId, $email);
				if ($r) {
					$rs = array(
						'code' => 1,
						'msg' => '登录成功！',
						'remain' => $r[0]['remain']
					);
				} else {
					$rs = array(
						'code' => 0,
						'msg' => '您的信息填写不匹配！'
					);
				}
			}
			
			$this->ajaxReturn($rs);
		}
		
		public function charge() {
			// 通过卡密来充值
			$vcode = I('post.vcode');
			$code = I('post.code');
			$steamId = I('post.steam_id');
			
			$verify = new \Think\Verify();
			if (!$verify->check($vcode, '')) {
				$this->ajaxReturn(array(
					'code' => 0,
					'msg' => '验证码不正确！'
				));
				return false;
			}
			// 验证激活码是否存在，不存在就提示不存在，否则提示绑卡成功
			$card_model = D('Card');
			$valid_card = $card_model->checkCard($code);
			if (!$valid_card) {
				$this->ajaxReturn(array(
					'code' => 0,
					'msg' => '无效或已失效的卡密！',
					'r' => $valid_card
				));
				return false;
			}
			$charge_num = $valid_card['charge_num'];
			// 对用户进行充值绑定
			$model_user = M('chiji_account');
			$user = $model_user->where("steam_id='%s'", $steamId)->setInc('remain',$charge_num);
			if ($user) {
				$card_model->bindCard($code, $steamId);
				$model_user->where("steam_id='%s'", $steamId)->setInc('pay_num', 1);
				$this->ajaxReturn(array(
					'code' => 1,
					'msg' => '充值成功!',
					'remain' => $user
				));
			} else {
				$this->ajaxReturn(array(
					'code' => 0,
					'msg' => '充值失败！'
				));
			}
		}
		
		// 次数减1，同步到服务器,无需通知客户端到账
		public function decRemain() {
			$user = I('post.steam_id');
			$model_user = M('chiji_account');
			$model_user->where("steam_id='%s' and remain>0", $user)->setDec('remain',1);
			$this->show('OK');
		}
}
