<?php
	namespace Admin\Model;
	use Think\Model;
	class UserModel extends Model{
		protected $tableName = 'user_user'; 
		//搜寻符合条件用户
		public function search($kw,$filter,$page){
			$model = new \Think\Model();
			switch($filter){
				case 'all':$fsql = "1=1";break;
				case 'normal':$fsql = "b.expiredate>=now()";break;
				case 'expired':$fsql = "b.expiredate<now()";break;
				case 'unactived':$fsql = "b.expiredate is null";break;
				default:$fsql = "1=1";
			}
			$pageSize = 20;
			$sql = "select a.user,a.email,case when b.expiredate is null then '未激活' else b.expiredate end as status,a.ip,a.time from user_user a left join account b on a.user=b.uid where a.user like '%$kw%' and ".$fsql." limit ".$pageSize*($page-1).",".$pageSize."";
			$rs = $model->query($sql);
			return $rs;
		}
		
		//查找符合条件的总数
		public function searchCount($kw,$filter){
			$model = new \Think\Model();
			switch($filter){
				case 'all':$fsql = "1=1";break;
				case 'normal':$fsql = "b.expiredate>=now()";break;
				case 'expired':$fsql = "b.expiredate<now()";break;
				case 'unactived':$fsql = "b.expiredate is null";break;
				default:$fsql = "1=1";
			}
			$pageSize = 20;
			$sql = "select count(*) from user_user a left join account b on a.user=b.uid where a.user like '%$kw%' and ".$fsql;
			$rs = $model->query($sql);
			$total = $rs[0]['count(*)'];
			$tp = $total/$pageSize;
			return ceil($tp);
		}
		//其它如用户资料修改，添加新用户，充值续期等，待完善。

		// 用户报表
		public function countUser() {
			$model = new \Think\Model();
			$ct1 = "select count(distinct user) as count, 'active' as type from balance_changelog where remark like '%充值%' or remark like '%广告%'";
			$ct2 = "select count(*) as count, 'total' as type from user_user";
			
			$rs1 = $model->query($ct1);
			$rs2 = $model->query($ct2);

			$rs = array(
				$rs1[0],
				$rs2[0]
			);

			return $rs;
		}
	}
?>