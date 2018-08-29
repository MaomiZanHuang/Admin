<?php
	namespace Admin\Model;
	use Think\Model;
	class BalanceModel extends Model{
		protected $tableName = 'user_balance'; 
		
		public function chargeUser($user, $points, $remark) {
			$log_model = M('balance_changelog');
			$balance_model = M('user_balance');
			
			$points = (int)$points;
			
			$_r = $this->where("user='%s'", $user)->find();
			$before_balance = $_r['points'];

			$r = $this->where("user='%s'", $user)->setInc('points', $points);
			
			// 更新积分
			if (!$r) {
				return False;
			}

			// 写入balance_changelog
			$log_model->data(array(
				user => $user,
				type => 'points',
				change_amt => $points,
				balance => $before_balance + $points,
				before_balance => $before_balance,
				time => date('Y-m-d H:i:s'),
				remark => $remark
				))->add();
			return True;
		}
	}
?>