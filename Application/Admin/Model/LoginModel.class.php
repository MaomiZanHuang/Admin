<?php
	namespace Admin\Model;
	use Think\Model;
	class LoginModel extends Model{
		protected $tableName = 'user_admin'; 
		public function login($params){
			//优先验证验证码，其次账号密码
			$vcode = $params['verifycode'];
			$user = $params['user'];
			$pwd = $params['pwd'];
			if(!$this->check_verify($vcode)){
				return array(
					'status'=>0,
					'msg'=>'验证码错误'
				);
			}
			$res = $this->check_userpwd($user,$pwd); 
			if(!$res){
				return array(
					'status'=>0,
					'msg'=>'账号或密码错误'
				);
			}
			// 根据登录类型记录是admin还是二级分销收银员cashier
			//注册成功写入session保存登录状态
			$session=array(
				'user'=>$user,
				'type'=> $res,
				'ctime'=>NOW_TIME	//创建时间
			);
			session('telanx',$session);
			return array(
				'status'=>1,
				'msg'=>'登录成功！'
			);
			
		}
		public function check_userpwd($user,$pwd){
			$model = M('user_admin');
			$r = $model->where("user='%s' and pwd='%s'",$user,$pwd)->select();
			if(count($r)==1) {
				if ($r[0]['type'] == 1) return 'admin';
				if ($r[0]['type'] == 2) return 'cashier';
				else return false;
			}
			else return false;
		}
		public function check_verify($code, $id = ''){
			$verify = new \Think\Verify();
			return $verify->check($code, $id);
		}
		//记录用户登录时间
		public function user_login_time($user){
			$model_user = M('user_admin');
			$data=array(
				'lastlogin'=>date('Y-m-d H:i:s'),
				'ip'=>$this->getIP()
			);
			$model_user->where("user='$user'")->save($data);
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