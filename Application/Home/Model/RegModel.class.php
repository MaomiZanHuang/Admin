<?php
	namespace Home\Model;
	use Think\Model;
	class RegModel extends Model{
		protected $tableName = 'user_user'; 
		public function reg($params){
			//优先验证验证码，其次验证2次密码，然后是用户名，然后是邮箱
			$vcode = $params['verifycode'];
			$pwd = $params['pwd'];
			$pwd2 = $params['pwd2'];
			$user = $params['user'];
			$email = $params['email'];
			
			if(!$this->check_verify($vcode)){
				$rs = array(
					'status'=>0,
					'msg'=>'验证码错误'
				);
				return $rs;
			}
			
			if(!($pwd==$pwd2 && $pwd) ){
				$rs = array(
					'status'=>0,
					'msg'=>'2次密码输入不一致'
				);
				return $rs;
			}
			
			//验证用户是否存在
			if($this->check_userExists($user)){
				$rs = array(
					'status'=>0,
					'msg'=>'用户已经存在！'
				);
				return $rs;
			}
			
			//插入数据库
			$model_user = M('user_user');
			$data = array(
				'id'=>0,
				'user'=>$user,
				'pwd'=>$pwd,
				'email'=>$email,
				'ip'=>$this->getIP(),
				'time'=>date('Y-m-d H:i:s'),
				'status'=>0,
				'sess'=>I('cookie.PHPSESSID')
			);
			
			$r = $model_user->data($data)->add();
			if($r){
				$rs = array(
					'status'=>1,
					'msg'=>'注册成功！'
				);
				//注册成功写入session保存登录状态
				$session=array(
					'user'=>$user,
					'type'=>'user',
					'uid' => $r,
					'ctime'=>NOW_TIME	//创建时间
				);
				session('telanx',$session);
				
				
			}else{
				$rs = array(
					'status'=>0,
					'msg'=>'注册失败！',
					'data'=>$data,
					'rs'=>$this->check_userExists($user)
				);
			}
			return $rs;
		}
		
		//注册for CRX
		public function regCRX($user, $pwd, $vcode, $email = ''){
			//优先验证验证码，其次验证2次密码，然后是用户名，然后是邮箱			
			if(!$this->check_verify($vcode)){
				$rs = array(
					'status'=>0,
					'msg'=>'验证码错误'
				);
				return $rs;
			}
			
			//验证用户是否存在
			if($this->check_userExists($user)){
				$rs = array(
					'status'=>0,
					'msg'=>'用户已经存在，请更换用户名！'
				);
				return $rs;
			}
			
			//插入数据库
			$model_user = M('user_user');
			$data = array(
				'id'=>0,
				'user'=>$user,
				'pwd'=>$pwd,
				'email'=> $email,
				'ip'=>$this->getIP(),
				'time'=>date('Y-m-d H:i:s'),
				'status'=>0,
				'sess'=>I('cookie.PHPSESSID')
			);
			
			$r = $model_user->data($data)->add();
			if($r){
				$rs = array(
					'status'=>1,
					'msg'=>'注册成功！'
				);
				//注册成功写入session保存登录状态
				$session=array(
					'user'=>$user,
					'uid' => $r,
					'type'=>'user',
					'ctime'=>NOW_TIME	//创建时间
				);
				session('telanx',$session);
				
				
			}else{
				$rs = array(
					'status'=>0,
					'msg'=>'注册失败！',
					'data'=>$data,
					'rs'=>$this->check_userExists($user)
				);
			}
			return $rs;
		}
		
		
		public function check_verify($code, $id = ''){
			$verify = new \Think\Verify();
			return $verify->check($code, $id);
		}
		
		//验证用户是否已存在
		public function check_userExists($user){
			$model = M('user_user');
			$rs = $model->where("user='%s'",$user)->count();
			if($rs=='1')return true;
			else return false;
			
		}
		//记录用户登录时间
		public function admin_login_time($user){
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