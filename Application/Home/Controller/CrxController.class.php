<?php
namespace Home\Controller;
use Think\Controller;

class CrxController extends Controller {
    //获取基本信息
		/**
		*插件版本号crxversion,是否强制升级forceUpdate
		*用户名
		*过期时间
		*代理地址列表
		*邀请奖励成功数
		*代理列表
		**/
		public function getInfo(){
			
			$rs = array(
				'user'=>null,
				'crxName'=>'全网通',
				'crxVersion'=>'1.0',
				'forceUpdate'=>true,
				'updateInfo'=>'插件有新版本啦！请先升级',
				'updateUrl'=>'http://oo22.taobao.com',
				'isExpired'=>false,
				'expireDate'=>'1970-07-01',
				'proxyList'=>null,
				'xftypes' => null
			);
			$model = D('Crx');
			$crxInfo = $model->getCrxInfo();
			if($crxInfo){
				$rs['kfInfo'] = $crxInfo['kfInfo'];
				$rs['crxVersion'] = $crxInfo['version'];
				$rs['forceUpdate'] = $crxInfo['forceUpdate'];
				$rs['updateInfo'] = $crxInfo['updateInfo'];
				$rs['updateUrl'] = $crxInfo['updateUrl'];
				$rs['qqGroup'] = $crxInfo['qqGroup'];
				$rs['officalSite'] = $crxInfo['officalSite'];

			}
			
			$user = public_user_id();
			// $xftypes = M('xftype')->select();
			// $rs['xftypes'] = $xftypes;
			if($user==null){
				$this->ajaxReturn($rs);
				return;
			}
			//根据user获取上述内容
			$rs['user']= $user;
			
			$expireDate = $model->getExpiredate($user);
			if(count($expireDate)>0){
				if(strtotime($expireDate[0]['expiredate'])<strtotime(date('Y-m-d H:i:s'))){
					$rs['isExpired'] = true;
					$rs['expireDate']=$expireDate[0]['expiredate'];
				}else {
					$rs['isExpired'] = false;
					$rs['proxyList'] = $this->getProxyList(0);
					$rs['expireDate'] = $expireDate[0]['expiredate'];
				}
			}else{
				$rs['isExpired'] = null;
				$rs['expireDate']='未激活';
			}
			$this->ajaxReturn($rs);
		}
		
		// 获取试用
		public function getTrial() {
			$ip = D('Reg')->getIp();
			$model = M('trial');
			$hasTried = $model->where("ip = '".$ip."'")->select();
			$res = array(
				trial => count($hasTried) ? 0 : 1,
				ip=>$hasTried[0],
				sec => 'adsfhj'.date('m-d').'2M2k',
				trialTime => 15*60
			);
			if (!count($hasTried)) {
				$model->data(array(
					id=>null,
					ip=>$ip,
					create_time=>date('Y-m-d H:i:s')
				))->add();
				
				$model_proxyserver = D('Crx');
				$proxyList = $model_proxyserver->getProxyList(0);
				$res['proxyList'] = $proxyList;
			}
			$this->ajaxReturn($res);
		}
		
		//获取代理服务器的状态
		public function getProxyList($i=1){
			$model_proxyserver = D('Crx');
			$rs = $model_proxyserver->getProxyList();
			if($i==0)return $rs;
			else
			$this->ajaxReturn($rs);
		}
		
		//获取订单信息
		public function orderList(){
			$model = M('orderlist');
			$uid = public_user_id();
			$orderRS = $model->field(array('otime','oid','cash','pid','remark','trano'))->where("uid='".$uid."' and status=1")->order('otime desc')->select();
			$this->ajaxReturn($orderRS);
		}
}
