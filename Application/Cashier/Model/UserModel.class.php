<?php
	namespace Cashier\Model;
	use Think\Model;
	class UserModel extends Model{
		protected $tableName = 'user_user'; 
		//搜寻符合条件用户
		public function search($kw,$filter,$page){
			$model = new \Think\Model();
			switch($filter){
				case 'all':$fsql = "1=1";break;
				case 'normal':$fsql = "status=1";break;
				case 'expired':$fsql = "status=2";break;
				case 'unactived':$fsql = "status=0";break;
				default:$fsql = "1=1";
			}
			$pageSize = 8;
			$sql = "select a.user,a.email,case when b.expiredate is null then '未激活' else b.expiredate end as status,a.ip,a.time from user_user a left join account b on a.user=b.uid where a.user like '%$kw%' and ".$fsql." limit ".$pageSize*($page-1).",".$pageSize."";
			$rs = $model->query($sql);
			return $rs;
		}
		
		//查找符合条件的总数
		public function searchCount($kw,$filter){
			$model = new \Think\Model();
			switch($filter){
				case 'all':$fsql = "1=1";break;
				case 'normal':$fsql = "status=1";break;
				case 'expired':$fsql = "status=2";break;
				case 'unactived':$fsql = "status=0";break;
				default:$fsql = "1=1";
			}
			$pageSize = 8;
			$sql = "select count(*) from user_user where user like '%$kw%' and ".$fsql;
			$rs = $model->query($sql);
			$total = $rs[0]['count(*)'];
			$tp = $total/$pageSize;
			return ceil($tp);
		}
		//其它如用户资料修改，添加新用户，充值续期等，待完善。

		// 用户报表
		public function countUser() {
			$model = new \Think\Model();
			$sql = "select b.type,count(*) as count from user_user a left join (select uid as user, (case when expiredate is null then 'unactive' when expiredate > now() then 'active' else 'expired' end) as type from account) b on a.user=b.user group by b.type";
			$rs = $model->query($sql);
			return $rs;
		}
	}
?>