<?php
	namespace Admin\Model;
	use Think\Model;
	class TaskModel extends Model{
		protected $tableName = 'task'; 
		/***条件成熟可考虑percona自动同步数据库***/
		//任务执行队列
		/*$data = {
			ip:
			type
			uid
			data
			time
		}**/
		public function getIpList(){
			$model_proxy = M('proxyserver');
			$ipList = $model_proxy->field('ip')->select();
			$arr = array();
			$len = count($ipList);
			for($i=0;$i<$len;$i++){
				array_push($arr,$ipList[$i]['ip']);
			}
			return $arr;
		}
		function post($url = '', $param = '') {
        if (empty($url)) {
            return false;
        }
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        return $data;
    }
		public function addTask($data){
			//先查找uid是否存在
			$uid = $data['uid'];
			$userTask = $this->where("uid='$uid'")->select();
			if(count($userTask)){
				//更新
				$data['ip']='';
				$data['time'] = date('Y-m-d H::s');
				$this->where("uid='$uid'")->save($data);
			}else{
				$data2 = array(
					'id'=>null,
					'ip'=>'',
					'uid'=>$uid,
					'time'=>date('Y-m-d H::s')
				);
				$this->add($data2);
			}
			
		}
		
		public function delTask($id){
			$this->where("id=".$id)->delete();
		}
		
		public function updateTask($id,$data){
			$this->where("id=".$id)->save($data);
		}
		
		//执行某条任务
		public function execTask($id){
			$taskInfo = $this->where("id=".$id)->select();
			if(!count($taskInfo)){
				throw_exception('任务'.$id.'不存在！','Exception');
			}
			$uid = $taskInfo[0]['uid'];
			$model = new \Think\Model();
			$account = $model->query("select pwd,expiredate from account where uid='$uid'");
			if(count($account)==0){
				throw_exception('用户'.$uid.'不存在！');
			}
			$pwd = $account[0]['pwd'];
			$expiredate = $account[0]['expiredate'];
			
			$ip = $taskInfo[0]['ip'];
			$ipList = array();
			if(strlen($ip)>0){
				$ipList = explode(';',$ip);
			}else{
				$ipList = $this->getIpList();
			}
			$i=0;
			$ip = $ipList;
			$failIp = array();
			while($ipList[$i]!=null){
				
				$param->uid = $uid;
				$param->pwd = $pwd;
				$param->expiredate = $expiredate;
				$type = 'update';
				$url = 'http://'.$ip[$i].'/telanx/index.php?type='.$type;
				$rs = $this->post($url,$param);
				echo "正在同步节点".$ip[$i].',url:'.$url;
				echo $ip[$I]."结果:";
				$crs = json_decode($rs,true);
				if($crx['result']){
					$style = '$0f0';
				}else{
					$style = '#f00';
				}
				echo '<font color='.$style.'><b>'.$crs['msg'].'</b></font><br/>';
				if(!($rs && $crs['result']==1)){
					array_push($failIp,$ip[$i]);
				}
				$i++;
			}
			if(count($failIp)==0){
				$this->delTask($id);
			}else{
				$this->updateTask($id,array("ip"=>implode(";",$failIp)));
			}
		}
		//执行所有任务
		public function execAllTask(){
			$tasks = $this->field("id")->order('time asc')->select();
			$len = count($tasks);
			$i=0;
			while($i<$len){
				$this->execTask($tasks[$i]["id"]);
				$i++;
			}
		}
		
		//执行account
		public function syncAccount($ip){
			
		}
	}
?>