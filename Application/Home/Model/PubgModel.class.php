<?php
	namespace Home\Model;
	use Think\Model;
	class PubgModel extends Model{
		protected $tableName = 'chiji_account'; 
		public function login($steam_id, $email){
			return $this->where("steam_id='%s' and email='%s'", $steam_id, $email)->select();
		}
		
		public function reg($steam_id, $email) {
			$account = array(
				'id' => null,
				'steam_id' => $steam_id,
				'email' => $email,
				'remain' => 0,
				'ip' => $this->getIP(),
				'pay_num' => 0
			);
			return $this->add($account);
		}
		public function checkUser($steam_id) {
			return $this->where("steam_id='%s'", $steam_id)->count() > 0
				? True
				: false;
		}
		
		public function getIP(){ 
			global $ip;
			if (getenv("HTTP_CLIENT_IP")) 
			$ip = getenv("HTTP_CLIENT_IP"); 
			else if(getenv("HTTP_X_FORWARDED_FOR")) 
			$ip = getenv("HTTP_X_FORWARDED_FOR"); 
			else if(getenv("REMOTE_ADDR")) 
			$ip = getenv("REMOTE_ADDR"); 
			else 
			$ip = "Unknow"; 
			return $ip; 
		} 
	}
?>
