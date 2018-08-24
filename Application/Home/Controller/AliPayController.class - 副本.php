<?php
namespace Home\Controller;
use Think\Controller;
use Org\Pay\Alipay;
class PayController extends Controller {
		
	public function index(){
		$trano = I('get.t');
		$days = $this->getDaysByTrano($trano);
		$uid = $this->getUserByTrano($trano);
		echo '充值天数'.$days.'充值用户'.$uid;
		//插入到account表中
		$this->accountUpdate($uid,$days);
	}
	//notify_url
	public function notifyurl(){
		$alipay = new Alipay();
		$verify_result = $alipay->Alipay_notify();
		if($verify_result) {//验证成功
			//商户订单号
      $out_trade_no = $_POST['out_trade_no'];
      //支付宝交易号
      $trade_no = $_POST['trade_no'];
      //交易状态
			$trade_status = $_POST['trade_status'];
      if($_POST['trade_status'] == 'TRADE_FINISHED') {
				//交易彻底结束，不会主动请求
			}else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				//后续还会发送TRADE_FINISHED状态过来
      }
			$this->updatePayStatus($out_trade_no,$trade_no,$trade_status);
			$this->updateAccount($out_trade_no);
			echo 'success';        //请不要修改或删除
    }else {
			//验证失败
      echo 'fail';
 
    }
	}
	
	//return_url
	public function returnurl(){
		$alipay = new Alipay();
		$alipay->Alipay_return();
		echo '充值成功!';
	}
	
	//更新交易状态
	public function updatePayStatus($out_trade_no,$trade_no,$trade_status){
			$data = array(
				'trano'=>$trade_no,
				'trastatus'=>$trade_status,
				'status'=>1
			);
			$model = M('orderlist');
			$model->data($data)->where('oid=\''.$out_trade_no.'\'')->save();
	}
	
	//更细用户账号
	public function updateAccount($out_trade_no){
		//现在account表中查找有无用户，有则新增，无则创建
		//$trano = I('get.t');
		$trano = $out_trade_no;
		$days = $this->getDaysByTrano($trano);
		$uid = $this->getUserByTrano($trano);
		//插入到account表中
		$this->accountUpdate($uid,$days);
	}
	
	//更新用户账号
	public function accountUpdate($uid,$days){
		//先查找是否存在
		$model = M('account');
		$orginDate = $model->where('uid=\''.$uid.'\'')->select();
		if(!$orginDate){		//不存在则新加入
		$uidModel = M('user_user');
		$pwd = $uidModel->field('pwd')->where('user=\''.$uid.'\'')->select();
			$data = array(
				'id'=>null,
				'uid'=>$uid,
				'pwd'=>$pwd[0]['pwd'],
				'expiredate'=> date('Y-m-d',strtotime(date('Y-m-d')." +".$days.'day')),
				'lastpaytime'=>date('Y-m-d H:i:s'),
				'remark'=>'新入会'
			);
			$model->data($data)->add();
		}else{
			$odate = $orginDate[0]['expiredate'];
			if(strtotime($odate)<strtotime(date('Y-m-d'))){		//已经过期的
				$ordate = date('Y-m-d');		//今天
			}else{
				$ordate = $odate;
			}
			$data = array(
				'expiredate'=> date('Y-m-d',strtotime($ordate." +".$days.'day')),
				'lastpaytime'=>date('Y-m-d H:i:s'),
				'remark'=>'充值续费'.$days.'天'
			);
			$model->data($data)->where('uid=\''.$uid.'\'')->save();
		}
	}
	
	//根据账单号查询用户信息
	public function getUserByTrano($trano){
		$model = M('orderlist');
		$uid = $model->field('uid')->where('oid=\''.$trano.'\'')->select();
		if(count($uid))return $uid[0]['uid'];
		else return '';
	}
	
	//根据交易号查询充值天数
	public function getDaysByTrano($trano){
		$model = new \Think\Model();
		$sql = 'select days from xftype where id=(select pid from orderlist where oid=\''.$trano.'\')';
		$rs = $model->query($sql);
		return $rs[0]['days'];
	}
	
	
	//测试用
	public function test(){
//		$this->updateAccount('20151120171156196');
		
	}
}
?>
