<?php
	namespace Home\Model;
	use Think\Model;
	class CardModel extends Model{
		protected $tableName = 'card'; 
		public function checkCard($card) {
			$n = $this->where("card_no='%s' and activate_time is null", $card)->select();
			return count($n) > 0 ? $n[0] : False;
		}
		
		public function bindCard($card, $account) {
			$data = array(
				'activate_time' => date('Y-m-d H:i:s'),
				'charge_account' => $account
			);
			return $this->where("card_no='%s'", $card)->save($data);
		}
	}
?>
