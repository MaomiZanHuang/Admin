<?php
	namespace Home\Model;
	use Think\Model;
	class CrxModel extends Model{
		protected $tableName = 'proxyserver'; 
		public function getProxyList(){
			$rs = $this->select();
			// 简单加密port
			$key = 1291;
			$rs2 = array();
			foreach($rs as $p) {
				$p['port'] = $p['port'] ^ $key;
				$p['spcode'] = $p['type'].' '.$p['ip'].':'.$p['port'];
				array_push($rs2, $p);
			}
			return $rs2;
		}
		
		public function getExpiredate($user){
			$model = M('account');
			$expireDate = $model->field('expiredate')->where("uid='".$user."'")->select();
			return $expireDate;
		}
		
		public function getCrxInfo(){
			$model = M('config');
			$crxinfo = $model->field('v')->where("k='crx'")->select();
			return json_decode($crxinfo[0]['v'],true);
		}
	}
?>
