<?php
namespace Home\Controller;
use Think\Controller;
use Org\Pay\Alipay;
class UserController extends Controller {
		
    public function index(){
		//检测是否登录
		$code = 'aavb';
		if(public_user_id()==null || public_user_type()!='user'){
			return $this->error("请先登录",U('Home/User/login'));
		}
		$user = array(
			'user'=>public_user_id(),
			'status'=>1,
			'expire'=>'1970-01-01'
		);
		//从account中查询用户状态
		$accountInfo = $this->getAccountInfo(public_user_id());
		
		$user['expiredate'] = $accountInfo['expireDate'];
		$user['status'] = $accountInfo['status'];
		//用户基本信息
		$this->assign('user',$user);
		
		
		// 看用户是否有资格
		$model = new \Think\Model();
		$rs = $model->query("select * from orderlist where uid='%s' and cash>20 and status=1 limit 0,1", $user);
		$have_access = false;
		if (count($rs)) {
			$have_access = true;
		}
		$this->assign('getcode', $have_access ? $code : '您当前会员等级不满足条件，请先升级为季度或年费会员！');
		
		$this->display();
    }
		
		//查询用户过期时间
		public function getAccountInfo($uid){
			$model = D('Crx');
			$expireDate = $model->getExpiredate($uid);
			if(count($expireDate)>0){
				if(strtotime($expireDate[0]['expiredate'])<strtotime(date('Y-m-d H:i:s'))){
					$rs['status'] = 2;
					$rs['expireDate']='已过期';
				}else{
					$rs['status'] = 1;
					$rs['expireDate'] = $expireDate[0]['expiredate'];
				}
			}else{
				$rs['status'] = 0;
				$rs['expireDate']='未激活';
			}
			return $rs;
		}
		
		//用户信息页面
		public function user(){
			if(public_user_id()==null|| public_user_type()!='user'){
				return $this->error("请先登录",U('Home/User/login'));
			}
			//获取用户名，邮件，上次登录时间，状态
			$user = public_user_id();
			$model = new \Think\Model();
			$rs = $model->query("select user,email,status,time from user_user where user='%s'",$user);
			$accountInfo = $this->getAccountInfo($user);
			$rs[0]['status'] = $accountInfo['status'];
			$this->assign('user',$rs[0]);
			$this->display();
		}
		
		//充值续费
		public function pay(){
			$user = public_user_id();
			if(public_user_id()==null|| public_user_type()!='user'){
				return $this->error("请先登录",U('Home/User/login'));
			}
			$model_config = M('config');
			$kfs = $model_config->field('v')->where("k='crx'")->select();
			//var_dump($kfs);
			$this->assign('kfs',$kfs[0]['v']);
			$this->assign('user',$user);
			$this->display('cardrecharge');
			
		}
		
		//充值续费接口
		public function alipayapi(){
			header("Content-type: text/html; charset=utf-8"); 
			$data = I('post.');
			$alipay = new Alipay();
			echo $alipay->pay($data);
		}
		
		//充值查询接口
		public function payStatus(){
			$model = M('orderlist');
			$uid = public_user_id();
			$oid = I('get.id');
			$query = $model->field('status')->where(array('uid'=>$uid,'oid'=>$oid))->find();
			if($query){
				$s = $query['status'];
				$msg = ($s==1?'已完成支付！':'已创建订单，未完成支付！');
				$rs = array(
					'status'=>$s,
					'msg'=>$msg
				);
			}else{
				$rs = array(
					'status'=>-1,
					'msg'=>'无该订单信息！如有疑问联系客服'
				);
			}
			$this->ajaxReturn($rs);
		}
		
		//用户充值记录，仅列出充值成功的
		public function payhistory(){
			$model = M('orderlist');
			$uid = public_user_id();
			$orderRS = $model->field(array('otime','oid','cash','pid','remark','trano'))->where("uid='%s' and status=1", $uid)->order('otime desc')->select();
			$this->assign('user',$uid);
			$this->assign('order',$orderRS);
			$this->display();
		}
		
		
		
		
		//检查是否存在用户
		public function userExists(){
			$user = I('get.user');
			if($user==null){
				$rs = array(
					'msg'=>'请先输入用户名！'
				);
				return $this->ajaxReturn($rs);
			}
			$model = M('user_user');
			if($model->where("user='%s'", $user)->select()==null){
				$rs = array(
					'status'=>0,
					'msg'=>'用户不存在'
				);
			}else{
				$rs = array(
					'status'=>1,
					'msg'=>'用户已经存在'
				);
			}
			$this->ajaxReturn($rs);
		}
		//用户登录
		public function login(){
			if (strstr(public_black_list(), get_client_ip()) != false) {
				$this->assign('error', '整顿调整中，暂停注册！');
				return $this->display();
			}
			if(I('post.user')){
				$model_login = D('Login');
				$rs = $model_login->login(I('post.'));
				if($rs['status']){
					//把新的sessid写入
					cookie('PHPSESSID',cookie("PHPSESSID"),30*24*3600);
					$model_login->update_sess(I('post.user'));
					return $this->success('登录成功！',U('Home/User/index'));
				}
				$this->assign('error',$rs['msg']);
			}
			$this->display();
		}
		
		//用户登录RS
		public function loginRS(){
			$rs = array(
				'status'=>0,
				'msg'=>''
			);
			if (strstr(public_black_list(), get_client_ip()) != false) {
				return $this->ajaxReturn(
					array(
						'status' => 0,
						'msg' => '整顿调整中，暂停登录！'
					)
				);
			}
			
			if(I('post.user')){
				$model_login = D('Login');
				$r = $model_login->login(I('post.'));
				if($r['status']){
					cookie('PHPSESSID',cookie("PHPSESSID"),30*24*3600);
					$model_login->update_sess(I('post.user'));
				}
				$rs['status'] = $r['status'];
				$rs['msg'] = $r['msg'];
			}else{
				$rs['msg'] = "非法操作！";
			}
			$this->ajaxReturn($rs);
		}
		
		//用户登出
		public function logout(){
			//销毁session
			session('[destroy]'); 
			/**
			当初这样设计是不是为了考虑关闭浏览器带来的问题呢？？
			$sessid = I('cookie.PHPSESSID');
			$sess_path = session_save_path();
			$sess_file = $sess_path."/sess_".$sess;
			if(file_exists($sess_file)){
				@unlink($sess_file);
			}**/
			//为防止在不同浏览器里异常操作而导致的问题
			$sess_id = I('get.sess_id');
			if($sess_id != null){
				$model_user = M('user_user');
				$users = $model_user->where("sess='%s'", $sess_id)->select();
				if (!count($users)) {
					return $this->ajaxReturn(array(
						'status' => 0,
						'msg' => '退出失败！'
					));
				}
				$model_user->where("id='%s'", $users[0]['id'])->setField('sess', NULL);
				return $this->ajaxReturn(array(
					'status' => 1,
					'msg' => '退出成功！'
				));
			} else {
				$this->success('登出成功！',U('Home/User/login'));
			}
		}
		
		//用户注册
		public function reg(){
			if (strstr(public_black_list(), get_client_ip()) != false) {
				$this->assign('error', '整顿调整中，暂停注册！');
				return $this->display();
			}
			if(I('post.user')){
				$model_reg = D('Reg');
				$rs = $model_reg->reg(I('post.'));
				if($rs['status']){
					cookie('PHPSESSID',cookie("PHPSESSID"),30*24*3600);
					return $this->success('注册成功！',U('Home/User/index'));
				}
				$this->assign('error',$rs['msg']);
			}
			$this->display();
		}
		
		//用户注册for CRX
		/*和登录一致**/
		public function regRS(){
			$rs = array(
				'status'=>0,
				'msg'=>''
			);
			if (strstr(public_black_list(), get_client_ip()) != false) {
				return $this->ajaxReturn(
					array(
						'status' => 0,
						'msg' => '整顿调整中，暂停注册！'
					)
				);
			}
			if(I('post.user')){
				$user = I('post.user');
				$pwd = I('post.pwd');
				$vcode = I('post.verifycode');
				if (!preg_match('/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/', $user)) {
					return $this->ajaxReturn(array(
						'status' => 0,
						'msg' => '邮箱格式不正确！'
					));
				}
				
				$model_login = D('Reg');
				$r = $model_login->regCRX($user,$pwd,$vcode, $user);
				
				cookie('PHPSESSID',cookie("PHPSESSID"),30*24*3600);
				$rs['status'] = $r['status'];
				$rs['msg'] = $r['msg'];
			}else{
				$rs['msg'] = "非法操作！";
			}
			$this->ajaxReturn($rs);
			
		}
		
		public function verifycode(){
			ob_clean();
			$config =    array(
					'fontSize'    =>    50,    // 验证码字体大小
					'length'      =>    4,     // 验证码位数
					'useNoise'    =>    false, // 关闭验证码杂点
					'fontttf'	=>'4.ttf'
				);
			$Verify = new \Think\Verify($config);
			$Verify->entry();
		}
		
		//修改用户资料
		public function user_handler(){
			$user = public_user_id();
			$email = I('post.email');
			$pwd = I('post.pwd');
			$pwd2 = I('post.pwd2');
			$model_user = M('user_user');
			$rs = array(
				'status'=>0,
				'msg'=>'密码错误！'
			);
			if($pwd!=''){
				$model_account = M('account');
				if($model_user->where("user='%s' and pwd = '%s'", $user, $pwd)->select()!=null){
					$data = array(
						'user' => $user,
						'email'=>$email,
						'pwd'=>$pwd2
					);
					$data2 =array(
						'uid' => $user,
						'pwd'=>$pwd2
					);
					$model_user->where("user='%s'", $user)->save($data);
					$model_account->where("uid='%s'", $user)->save($data2);
					$rs = array(
						'status'=>1,
						'msg'=>'修改成功！'
					);
				}
				
			}else{
				$data = array(
					'user' => $user,
					'email'=>$email
				);
				$kk = $model_user->where("user='%s'", $user)->save($data);
				$rs = array(
					'kk' => $kk,
					'user' => $user,
					'data' => $data,
					'status'=>1,
					'msg'=>'修改成功！'
				);
			}
			$this->ajaxReturn($rs);
		}
	
	
	// 发送邮件
	public function sendMail($email='1241818518@qq.com', $content) {
		$mail = new \Org\Mail\PHPMailer();
		$mail->IsSMTP(); // 启用SMTP
		$mail->Host= 'smtp.aliyun.com'; //smtp服务器的名称（这里以QQ邮箱为例）
		$mail->SMTPAuth = True; //启用smtp认证
		$mail->Username = 'telanx1993@aliyun.com'; //你的邮箱名
		$mail->Password = 'telanx1993' ; //邮箱密码
		$mail->From = 'telanx1993@aliyun.com'; //发件人地址（也就是你的邮箱地址）
		$mail->FromName = 'telanx1993'; //发件人姓名
		$mail->AddAddress($email,"蔷薇用户");
		$mail->WordWrap = 50; //设置每行字符长度
		$mail->IsHTML(True); // 是否HTML格式邮件
		$mail->CharSet= 'utf-8'; //设置邮件编码
		$mail->Subject = '【蔷薇】找回密码'; //邮件主题
		$mail->Body = $content; //邮件内容
		$mail->AltBody = ""; //邮件正文不支持HTML的备用显示
		return $mail->send();
	}
	// 找回密码
	public function findPwd() {
		$vcode = I('post.vcode');
		$email = I('post.email');
		$LoginModel = D('Login');
		if (!$LoginModel ->check_verify($vcode)) {
			return $this->ajaxReturn(array(
				'code' => 0,
				'msg' => '验证码不正确！'.$vcode
			));
		}
		if (!preg_match('/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/', $email)) {
			return $this->ajaxReturn(array(
				'code' => 0,
				'msg' => '邮箱不正确！'
			));
		}
		// 进而查找邮箱数据
		$UserModel = M('user_user');
		$users = $UserModel->where("email = '%s'", $email)->select();
		if (!count($users)) {
			return $this->ajaxReturn(array(
				'code' => 0,
				'msg' => '该邮箱未绑定任何账号！'
			));
		}
		$content = '请牢记您的账号密码，可以在个人中心页面修改密码。<br/>';
		foreach($users as $user) {
			$content = $content. '账号:<b>'.$user['user'].'</b>   密码:<b>'.$user['pwd'].'</b><br/>';
		}
		try {
			$this->sendMail($email, $content);
			$this->ajaxReturn(array(
				'code' => 1,
				'msg' => '邮件发送成功！'
			));
		} catch(Exception $err) {
				$this->ajaxReturn(array(
					'code' => 0,
					'msg' => '邮件发送失败！'
				));
		}
	}

	// 通过卡密充值
	public function charge() {
		// 通过卡密来充值
		$vcode = I('post.vcode');
		$code = I('post.code');
		$user = public_user_id() ? public_user_id() : I('post.user');
		
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
		// 对用户进行充值绑定
		if ($user) {
			$card_model->bindCard($code, $user);
			// 充值记录记录到order里
			$uid = $user;
			switch($valid_card['type']) {
				case 'yf':
					$rechargeAmt = 1;
					$rechargeUnit = 'm';
					$cash = 15;
					break;
				case 'jf':
					$rechargeAmt = 3;
					$rechargeUnit = 'm';
					$cash = 40;
					break;
				case 'nf':
					$rechargeAmt = 12;
					$rechargeUnit = 'm';
					$cash = 115;
					break;

			}
			$model_account = D('Admin/account');
			
			if($model_account->rechargeAccount($uid,$rechargeAmt,$rechargeUnit)){
				//订单记录插入订单表中
				$model_order = M('orderlist');
				$order = array(
					'id'=>null,
					'uid'=> $uid,
					'otime'=> date('Y-m-d H:i:s'),
					'pid'=>'0',
					'cash'=>(float)$cash,
					'trano'=> $code,
					'tradesatus'=>'success',
					'remark'=>'卡密充值'.$rechargeAmt.$rechargeUnit,
					'operator'=> 'CARD',
					'status'=>1
				);
				$model_order->add($order);
			}else{
				return $this->ajaxReturn(array(
					'code'=>0,
					'msg'=>'为'.$uid.'充值失败！'
				));
			}

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
}