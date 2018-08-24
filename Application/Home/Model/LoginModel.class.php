<?php
	namespace Home\Model;
	use Think\Model;
	class LoginModel extends Model{
		protected $tableName = 'user_user'; 
		public function login($params){
			//优先验证验证码，其次账号密码
			$vcode = $params['verifycode'];
			$user = $params['user'];
			$pwd = $params['pwd'];
			if($vcode == null) {
				if(!$this->check_sameip($user)){
					return array(
						'status'=>0,
						'msg'=>'2次登录IP不一致！'.$this->getIP()
					);
				}
			}else {
				if(!$this->check_verify($vcode)){
					return array(
						'status'=>0,
						'msg'=>'验证码错误'
					);
				}
			}
			if(!($uid = $this->check_userpwd($user,$pwd))){
				return array(
					'status'=>0,
					'msg'=>'账号或密码错误'
				);
			}
			
			//判断是否已经登录过
			if($this->check_isOnline($user)){
				return array(
					'status'=>0,
					'msg'=>'您的账号已在线，请先退出后再重试！'
				);
			}
			//更改逻辑为后登入会踢掉先前登录用户
			/**$this->singleLogin($user);
			$this->update_sess($user);**/
			//注册成功写入session保存登录状态
			$session=array(
				'user'=>$user,
				'type'=>'user',
				'uid'=>$uid,
				'ctime'=>NOW_TIME	//创建时间
			);
			session('telanx',$session);
			//更新sess
			//记录登录IP和时间
			$this->user_login_time($user); 
			return array(
				'status'=>1,
				'msg'=>'登录成功！'
			);
			
		}
		
		//单用户登录
		public function singleLogin($user){
			$model = M('user_user');
			$sess_path = session_save_path();
			$sessid = $model->field('sess')->where("user='$user'")->select();
			if(count($sessid)){
				$sess = $sessid[0]['sess'];
				$sess_file = $sess_path."/sess_".$sess;
				if(file_exists($sess_file)){		//找到就删除，相当于踢人
					@unlink ($sess_file);
				}
			}
		}
		public function check_isOnline($user){
			//从表中查找session文件，找到了就返回false
			//没找到就更新session文件名
			$model = M('user_user');
			$sess_path = session_save_path();
			$sessid = $model->field('sess')->where("user='$user'")->select();
			if(count($sessid)){
				$sess = $sessid[0]['sess'];
				$sess_file = $sess_path."/sess_".$sess;
				if(!file_exists($sess_file)){
					return false;
				}
				//手动判断是否超时未操作
				$a=filemtime($sess_file);
				if(!$a){
					return false;
				}
				if((time()-$a)>1800){
					//30分钟不操作，系统默认退出
					return false;
				}
				//尝试读取文件
				//copy($sess_file,$sess_file.".txt");
				$fstr = substr((string) @file_get_contents($sess_file),0,6);
				//$fstr = substr((string) @file_get_contents($sess_file.".txt"),0,6);
				//unlink($sess_file.".txt");		//使用后删除
				if($fstr==='telanx'){
					return true;			
				}
				return false;
				
			}
			else return false;
		}
		
		//更新sess
		public function update_sess($user){
			$model = M('user_user');
			$data = array(
				'sess'=>I('cookie.PHPSESSID')
			);
			$model->data($data)->where("user='$user'")->save();
		}
		
		public function check_userpwd($user,$pwd){
			$model = M('user_user');
			$r = $model->where("user='%s' and pwd='%s'", $user,$pwd)->select();
			if (count($r)) {
				return $r[0]['id'];
			} else return false;
		}
		public function check_verify($code, $id = ''){
			$verify = new \Think\Verify();
			return $verify->check($code, $id);
		}
		
		public function check_sameip($user) {
			$ip = $this->getIP();
			$model = M('user_user');
			$rs = $model->where("user='%s' and ip='%s'", $user, $ip)->select();
			if($rs){
				return True;
			}else{
				return False;
			}
		}
		//记录用户登录时间
		public function user_login_time($user){
			$model_user = M('user_user');
			$data=array(
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
