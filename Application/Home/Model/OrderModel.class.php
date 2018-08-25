<?php
/**
-下订单
*/
namespace Home\Model;
use Think\Model;

class OrderModel extends Model {
	protected $tableName = 'orderlist'; 
	// 预下订单，不实际充值
	public function preOrder($uid, $good) {
		$model_orderlist = M('orderlist');
		$order = array(
			id => NULL,
			uid => $uid,
			oid => $good['oid'],
			otime => date('Y-m-d H:i:s'),
			pid => $good['pid'],
			cash => $good['cash'],
			status => 0,
			operator => $good['pay_type']
		);
		$model_orderlist->data($order)->add();
	}
	
	// 激活订单充值，实际充值
	public function activeOrder($order, $remark) {
		$out_trade_no = $order['out_trade_no'];
		
		// 更新订单状态
		$trade_no = $order['trade_no'];
		$trade_status = $order['trastatus'];
		$this->updatePayStatus($out_trade_no, $trade_no,$trade_status, $remark);
		// 更新账号
		$this->updateAccount($out_trade_no);
	}
	
	// 查询订单状态
	public function queryOrderStatus($out_trade_no) {
		$model_orderlist = M('orderlist');
		$order = $model_orderlist->where('oid="'.$out_trade_no.'"')->select();
		if (count($order) > 0) {
			$order = $order[0];
			return $order['status'];
		} else {
			return 0;
		}
		
	}
		
	
	//更新交易状态
	public function updatePayStatus($out_trade_no,$trade_no,$trade_status,$remark){
			$data = array(
				'trano'=>$trade_no,
				'trastatus'=>$trade_status,
				'status'=>1,
				'remark' => $remark
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
		$model_account = M('account');
		$orginDate = $model_account->where('uid=\''.$uid.'\'')->select();
		if(!$orginDate){		//不存在则新加入
				$uidModel = M('user_user');
				$pwd = $uidModel->field('pwd')->where("user='".$uid."'")->select();
				$data = array(
					'id'=>null,
					'uid'=>$uid,
					'pwd'=>$pwd[0]['pwd'],
					'expiredate'=> date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')." +".$days.'day')),
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
					'expiredate'=> date('Y-m-d H:i:s',strtotime($ordate." +".$days.'day')),
					'lastpaytime'=>date('Y-m-d H:i:s'),
					'remark'=>'充值续费'.$days.'天'
				);
				if(!($model_account->where("uid='".$uid."'")->save($data))){
					return false;
				}
			}
			return true;
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
