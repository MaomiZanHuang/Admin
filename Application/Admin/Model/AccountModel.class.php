<?php
	namespace Admin\Model;
	use Think\Model;
	class AccountModel extends Model{
		protected $tableName = 'account'; 
		
		/***增加时间
		*$unit = enum{"h","d","m"}
		**/
		//用户信息更新充值
		public function rechargeAccount($uid,$amt,$unit){
			$model_account = M('account');
			$addTimeMap = array(
				'h'=>'hour',
				'd'=>'day',
				'm'=>'month'
			);
			$orginDate = $model_account->where("uid='%s'", $uid)->select();
			if(!$orginDate){		//不存在则新加入
				$uidModel = M('user_user');
				$pwd = $uidModel->field('pwd')->where("user='%s'", $uid)->select();
				$data = array(
					'id'=>null,
					'uid'=>$uid,
					'pwd'=>$pwd[0]['pwd'],
					'expiredate'=> date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')." +".$amt.$addTimeMap[$unit])),
					'lastpaytime'=>date('Y-m-d H:i:s'),
					'remark'=>'新入会'
				);
				if(!($model_account->data($data)->add())){
					return false;
				}
			}else{
				$odate = $orginDate[0]['expiredate'];
				if(strtotime($odate)<strtotime(date('Y-m-d H:i:s'))){		//已经过期的
					$ordate = date('Y-m-d H:i:s');		//今天
				}else{
					$ordate = $odate;
				}
				$data = array(
					'expiredate'=> date('Y-m-d H:i:s',strtotime($ordate." +".$amt.$addTimeMap[$unit])),
					'lastpaytime'=>date('Y-m-d H:i:s'),
					'remark'=>'充值续费'.$amt.$addTimeMap[$unit]
				);
				if(!($model_account->where("uid='%s'", $uid)->save($data))){
					return false;
				}
			}
			return true;
		}
	}
?>