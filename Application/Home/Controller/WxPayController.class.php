<?php
namespace Home\Controller;
use Think\Controller;

Vendor('Pay.WxPay');
Vendor('Pay.lib.WxPay#Notify');


class WxPayController extends Controller {
	const goods = '[{"id":"qw001","name":"\u6708\u8d39\u4f1a\u5458\u0028\u0031\u0035\u5929\u0029","price":1500},{"id":"qw002","name":"\u5b63\u5ea6\u4f1a\u5458\u0028\u0033\u4e2a\u6708\u0029","price":4000},{"id":"qw003","name":"\u5e74\u8d39\u4f1a\u5458\u0028\u0031\u5e74\u0029","price":11500}]';
	public static function notify ($callback, &$msg)
	{	
		//获取通知的数据
		$xml = file_get_contents('php://input');
		//如果返回成功则验证签名
		try {
			if(!$xml){
				throw new WxPayException("xml数据异常！");
			}
			//将XML转为array
			//禁止引用外部xml实体
			libxml_disable_entity_loader(true);
			$this->values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
			return $this->values;
			//$WxPayResults = new WxPayResults();
			//$result = WxPayResults::Init($xml);
		} catch (WxPayException $e){
			$msg = $e->errorMessage();
			return false;
		}
		return call_user_func($callback, $result);
	}
	 //扫码支付 模式一 模式二 V3版本微信支付
    public function index(){
    	//模式一
    	/**
    	 * 流程：
    	 * 1、组装包含支付信息的url，生成二维码
    	 * 2、用户扫描二维码，进行支付
    	 * 3、确定支付之后，微信服务器会回调预先配置的回调地址，在【微信开放平台-微信支付-支付配置】中进行配置
    	 * 4、在接到回调通知之后，用户进行统一下单支付，并返回支付信息以完成支付（见：native_notify.php）
    	 * 5、支付完成之后，微信服务器会通知支付成功
    	 * 6、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
    	 */
		$type = I('get.type');
		if(!in_array($type, array('0', '1', '2'))) {
			$type = '0';
		}
    	$notify = new \NativePay();
    	// $url1 = $notify->GetPrePayUrl("123456789");
    	
    	//模式二
    	/**
    	 * 流程：
    	 * 1、调用统一下单，取得code_url，生成二维码
    	 * 2、用户扫描二维码，进行支付
    	 * 3、支付完成之后，微信服务器会通知支付成功
    	 * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
    	*/
		$uid = public_user_id();
		$select_good = json_decode(self::goods, True)[$type];

		$out_trade_no = public_charge_prefix().public_user_uid().'_'.time();
    	$input = new \WxPayUnifiedOrder();
    	$input->SetBody($select_good['name']);
    	$input->SetAttach("微信支付");
    	$input->SetOut_trade_no($out_trade_no);
    	$input->SetTotal_fee($select_good['price']);
    	$input->SetTime_start(date("YmdHis"));
    	$input->SetTime_expire(date("YmdHis", time() + 2*3600));
    	$input->SetGoods_tag($select_good['name']);
    	$input->SetNotify_url(public_charge_notifyurl());
		$input->SetSpbill_create_ip();
    	$input->SetTrade_type("NATIVE");
    	$input->SetProduct_id($select_good['id']);

    	$result = $notify->GetPayUrl($input);
		//var_dump($result);
		if ($uid && $result['code_url']) {
			$model_order = D('Order');
			$model_order->preOrder($uid, array(
				oid => $out_trade_no,
				pid => $type,
				cash => (int)($select_good['price'])/100,
				pay_type => 'WX_PAY'
			));
		}
		echo json_encode(array(
			uid => $uid,
			url_data => 'http://paysdk.weixin.qq.com/example/qrcode.php?data='.$result['code_url'],
			out_trade_no => $out_trade_no,
			expire => date("Y-m-d H:i:s", time() + 2*3600)
		));
    }
	
	// 查询订单状态
	public function queryOrder() {
		$oid = I('get.order');
		$uid = public_user_id();
		if (empty($uid) or empty($oid)) {
			$result = array(
				code => 0,
				msg => '非法参数'
			);
		} else {
			$result = M('orderlist')->where('uid="'.$uid.'" and oid="'.$oid.'"')->select();
			$result = count($result)
				? array(
					code=> 1, 
					data => $result[0]
				 )
				: array(
					code => 0,
					msg => '该订单不存在！'
				);
		}
		echo json_encode($result);
	}
	
	// 成功通知回调
	public function notifyurl() {
		$notify = new \WxPayNotify();
		$notify2 = new \NativePay();
		$result = $notify2->notify(array($this, 'handleNotify'),$msg);
		// 此处获取数据并校验
		if($result == false){
			$notify->SetReturn_code("FAIL");
			$notify->SetReturn_msg($msg);
			$notify->ReplyNotify(false);
			return;
		} else {
			//该分支在成功回调到NotifyCallBack方法，处理完成之后流程
			$notify->SetReturn_code("SUCCESS");
			$notify->SetReturn_msg("OK");
		}
		$notify->ReplyNotify(false);
	}
	
	public function handleNotify($data) {
		$notify = new \WxPayNotify();
		$msg = "OK";
		$result = $this->NotifyProcess($data, $msg);
		
		if($result == true){
			$notify->SetReturn_code("SUCCESS");
			$notify->SetReturn_msg("OK");
		} else {
			$notify->SetReturn_code("FAIL");
			$notify->SetReturn_msg($msg);
		}
		return $result;
		/**
		$return_data = new \WxPayNotifyReply();
		if ($result && $result['return_code'] == 'SUCCESS') {
			if ($result['result_code'] == 'FAIL') {
				$return_data->SetReturn_code('FAIL');
				$return_data->SetReturn_msg($return_data['err_code'].$return_data['err_code_des']);
			} else {
				$return_data->SetReturn_code('SUCCESS');
				$return_data->SetReturn_msg('OK');
				
				// 处理订单刷新的事情
				$model_order = D('Order');
				$order = array(
					out_trade_no => $result['out_trade_no'],
					trade_no => $result['transaction_id'],
					trastatus => $result['result_code']
				);
				
				// 预先判断该订单状态，如果已处理则不管
				if ($model_order->queryOrderStatus($order['out_trade_no']) !== 1) {
					$model_order->activeOrder($order);
				}
			}
		} else {
			$return_data->SetReturn_code('FALSE');
			$return_data->SetReturn_msg('支付失败');
		}
		// 保存数据到log里
		M('log')->data(array(
			id=>NULL,
			content => json_encode($result)
		))->add();
		echo $return_data->ToXml();
		exit;
		**/
	}
	
	public function NotifyProcess($result, &$msg) {
		M('log')->data(array(
			id=>NULL,
			time=>date('Y-m-d H:i:s'),
			content => json_encode($result)
		))->add();
		if ($result['return_code'] !== 'SUCCESS') {
			$msg = $result['err_code'].$result['err_code_des'];
			return false;
		}
		// 处理订单刷新的事情
		$model_order = D('Order');
		$order = array(
			out_trade_no => $result['out_trade_no'],
			trade_no => $result['transaction_id'],
			trastatus => $result['result_code']
		);
		
		// 预先判断该订单状态，如果已处理则不管
		if ($model_order->queryOrderStatus($order['out_trade_no']) !== 1) {
			$model_order->activeOrder($order, '微信充值');
		}
		return true;
		
	}
}