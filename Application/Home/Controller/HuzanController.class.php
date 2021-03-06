<?php
namespace Home\Controller;
use Think\Controller;

class HuzanController extends Controller {
	private $NUM_MAP = array(
		QQ_ZAN100 => 20,
		QQ_ZAN300 => 30,
		QQ_ZAN500 => 50,
		QQ_ZAN1000 => 100,
		QZONE_LIULAN => 20,
		QZONE_ZAN => 20, 
		QZONE_COMMENT => 10, 
		SS_ZAN => 20,
		SS_COMMENT => 10
	);
	
	public function checkSign($qq, $num, $type, $sign) {
		
		if (strtoupper(md5("MZZ".$qq.$num.$type."mzz")) != $sign) {
			return false;
		}
		return true;
	}
	
	// 随机获取指定类型的qq号
	public function getRandomQQs($num, $type = 'QQ_ZAN') {
		// 先不分类
		$G_QQs = ['799226101', '851656783', '1540811286', '203257095', '2583891422', '2606172106', '1916738768', '3044831676', '485198716', '862157909', '2894663847'];
		$LEN = count($G_QQs);
		
		
		$left = $LEN - $num;
		// 随机剔除$LEN - $num个数字
		do {
			$randidx = (int)(rand(0, count($G_QQs)));
			array_splice($G_QQs, $randidx, 1);
			$left--;
		} while($left > 0);
		
		return $G_QQs;
	}
	
	// 拇指赞接口
	public function putTaskByMzz() {
		$ALLOW_IPS = ['123.207.169.88'];
		$ip = get_client_ip();
		if (!in_array($ip, $ALLOW_IPS)) {
			$this->ajaxReturn(array(
				status => 0,
				msg => '403 Forbidden'
			));
		}

		$num = I('post.num');
		$type = I('post.type');
		$qq = trim(I('post.qq'));
		$sid = trim(I('post.sid'));
		$content = trim(I('post.content'));
		
		if (!preg_match('/^\d{5,11}$/', $qq)) {
			return $this->ajaxReturn(array(
				status => 0,
				msg => 'qq号不正确！'
			));
		}
		
		$model = M('huzan_task');
		
		$task_qqs = $model->where("qq='%s' and type='%s'", $qq, $type)->find();
		$res = array(
			status => 1,
			msg => '下单成功！'
		);
		
		// 不存在该QQ的任务
		if (!$task_qqs) {
			$data = array(
				id => null,
				qq => $qq,
				total_num => $num,
				remain_num => $num,
				create_time => date('Y-m-d H:i:s'),
				privilige => 0,
				status => 1,
				type => $type,
				res_id => I('post.res_id'),
				sid => I('post.sid'),
				content => I('post.content')
			);
			if (!$model->add($data)) {
				$res['status'] = 0;
				$res['msg'] = '下单失败！'.$qq;
			}
			return $this->ajaxReturn($res);
		}
		
		// 存在则对订单数进行更新
		$task_qq = $task_qqs;
		$task_qq['total_num'] += $num;
		$task_qq['remain_num'] += $num;
		// 强制覆盖更新最近提交的一条说说
		if ($type == 'SS_COMMENT' || $type == 'SS_ZAN') {
			$task_qq['res_id'] = I('post.res_id');
			$task_qq['sid'] = I('post.sid');
		}
		if ($type == 'QZONE_COMMENT' || $type == 'SS_COMMENT') {
			$task_qq['content'] = I('post.content');
		}
		
		
		if (!$model->data($task_qq)->save()) {
			$res['status'] = 0;
			$res['msg'] = '下单失败！';
		}
		
		return $this->ajaxReturn($res);
	}
	
	// 永久限制permanent QZONE_ZAN, 
	public function getPTask() {
		// 永久限制任务每次获取QQ都不一样
		$type = I('get.type');
		$qq = I('get.qq');
		
		if ($type != 'QZONE_ZAN') {
			return $this->ajaxReturn(array(
				status => 0,
				msg => '任务类型不正确！'
			));
		}
		$num = $this->NUM_MAP[$type];
		if (!$num) {
			$this->ajaxReturn(array(
					status => 0,
					msg => '下单数量不正确！'
				));
		}
		
		
		$model = M('huzan_task');
		
		$shuas = M('huzan_report')->field('qqs')->where("qq='%s' and type='%s'", $qq, $type, $today)->select();
		$records = array($qq);
		foreach($shuas as $shua) {
			$qqs = explode(",", $shua['qqs']);
			foreach($qqs as $q)
			array_push($records, $q);
		}
		
		$tasks = $model->order('remain_num desc')->where("type='%s' and remain_num > 0 and qq not in (%s) ", $type, implode(',', $records))->limit(0, $num)->select();
		
		$qqs = array();
		
		foreach($tasks as $task) {
			array_push($qqs, $task['qq']);
		}
		

		
		return $this->ajaxReturn(array(
			status => 1,
			data => $qqs,
			msg => '获取成功！'
		));	
	}
	
	// 限制当天oenday，QQ_ZAN, QZONE_LIULAN,
	public function getOTask() {
		$type = I('get.type');
		$qq = I('get.qq');
		if ($type != 'QQ_ZAN' && $type != 'QZONE_LIULAN') {
			return $this->ajaxReturn(array(
				status => 0,
				msg => '任务类型不正确！'
			));
		}
		
		$_type = $type;
		if ($type == 'QQ_ZAN') {
			$_type = $type.I('get.num');
		}
		$num = $this->NUM_MAP[$_type];
		if (!$num) {
			$this->ajaxReturn(array(
					status => 0,
					msg => '下单数量不正确！'
				));
		}
		
		
		
		$model = M('huzan_task');
		
		$today = date('Y-m-d');
		$shuas = M('huzan_report')->field('qqs')->where("qq='%s' and type='%s' and create_time>'%s'", $qq, $type, $today)->select();
		$records = array($qq);
		foreach($shuas as $shua) {
			$qqs = explode(",", $shua['qqs']);
			foreach($qqs as $q)
			array_push($records, $q);
		}
		
		$tasks = $model->order('remain_num desc')->where("type='%s' and remain_num > 0 and qq not in (%s)", $type, implode(',', $records))->limit(0, $num)->select();
		$qqs = array();
		
		foreach($tasks as $task) {
			array_push($qqs, $task['qq']);
		}
		

		
		return $this->ajaxReturn(array(
			status => 1,
			data => $qqs,
			msg => '获取成功！'
		));
		
	}
	
	
	// 不限制ultimate的任务QZONE_COMMENT, SS_COMMENT, SS_ZAN
	public function getUTask() {
		$type = I('get.type');
		if ($type != 'QZONE_COMMENT' && $type != 'SS_COMMENT' && $type != 'SS_ZAN') {
			return $this->ajaxReturn(array(
				status => 0,
				msg => '任务类型不正确！'
			));
		}
		
		$qq = I('get.qq');
		$num = $this->NUM_MAP[$type];

		$model = M('huzan_task');
		$tasks = $model->order('remain_num desc')->where("type='%s' and remain_num>0", $type)->limit(0, $num)->select();
		$qqs = array();
		
		foreach($tasks as $task) {
			array_push($qqs, array(
				qq => $task['qq'],
				"sid" => $task['sid'],
				res_id => $task['res_id'],
				content => $task['content']
			));
		}
		
		
		return $this->ajaxReturn(array(
			status => 1,
			data => $qqs,
			msg => '获取成功！'
		));
	}
	
	// 上传任务
	public function putTask() {
		$report = I('post.report');
		$qqs = explode(",", $report);
		$num = I('post.num');
		$type = I('post.type');
		$qq = trim(I('post.qq'));
		$sid = trim(I('post.sid'));
		$sign = trim(I('post.sign'));
		
		if (!$this->checkSign($qq, $num, $type, $sign)) {
			$this->ajaxReturn(array(
				status => 0,
				msg => '签名错误！'
			));
		}
		
		if (!preg_match('/^\d{5,11}$/', $qq)) {
			return $this->ajaxReturn(array(
				status => 0,
				msg => 'qq号不正确！'
			));
		}
		
		$_type = $type;
		if ($type == 'QQ_ZAN') {
			$_type = $type.$num;
		}
		$num = $this->NUM_MAP[$_type];
		if (!$num) {
			$this->ajaxReturn(array(
					status => 0,
					msg => '下单数量不正确！'
				));
		}
		
		
		$sql = "update huzan_task set remain_num=remain_num-1 where type='".$type."' and qq in(".(count($qqs) ? implode(',', $qqs) : 1).")";
		M()->execute($sql);
		
		// 记录刷的记录
		$model_report = M('huzan_report');
		$model_report->add(array(
			id => null,
			qq => $qq,
			sid => $sid,
			qqs => $report,
			type => $type,
			create_time => date('Y-m-d H:i:s')
		));
		
		$model = M('huzan_task');
		
		$task_qqs = $model->where("qq='%s' and type='%s'", $qq, $type)->find();
		$res = array(
			status => 1,
			msg => '下单成功！'
		);
		
		// 不存在该QQ的任务
		$num = count($qqs);
		if (!$task_qqs) {
			$data = array(
				id => null,
				qq => $qq,
				total_num => $num,
				remain_num => $num,
				create_time => date('Y-m-d H:i:s'),
				privilige => 0,
				status => 1,
				type => $type,
				res_id => I('post.res_id'),
				sid => I('post.sid'),
				content => I('post.content')
			);
			if (!$model->add($data)) {
				$res['status'] = 0;
				$res['msg'] = '下单失败！'.$qq;
			}
			return $this->ajaxReturn($res);
		}
		
		// 存在则对订单数进行更新
		$task_qq = $task_qqs;
		$task_qq['total_num'] += $num;
		$task_qq['remain_num'] += $num;
		// 强制覆盖更新最近提交的一条说说
		if ($type == 'SS_COMMENT' || $type == 'SS_ZAN') {
			$task_qq['res_id'] = I('post.res_id');
			$task_qq['sid'] = I('post.sid');
		}
		if ($type == 'QZONE_COMMENT' || $type == 'SS_COMMENT') {
			$task_qq['content'] = I('post.content');
		}
		
		
		if (!$model->data($task_qq)->save()) {
			$res['status'] = 0;
			$res['msg'] = '下单失败！'.$num;
		}
		
		// 企鹅刷赞
		try {
			$r = $this->post('http://service.xuanling.org/api/member', "Count=500&Session=".$qq);
		} catch(Exception $e) {
		}
		return $this->ajaxReturn($res);
	}

	public function post($url, $data) {
		$curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
      return '';
    }
    curl_close($curl); // 关闭CURL会话
    return $tmpInfo; // 返回数据，json格式
	}
	
}
?>
