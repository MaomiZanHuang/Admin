<?php
	namespace Admin\Model;
	use Think\Model;
	class CardModel extends Model{
		protected $tableName = 'card'; 
		//搜寻符合条件用户
		public function search($kw,$filter,$page){
			$model = new \Think\Model();
			switch($filter){
				case 'all':$fsql = "1=1";break;
				case 'normal':$fsql = "activate_time is not null";break;
				case 'expired':$fsql = "activate_time is null";break;
				default:$fsql = "1=1";
			}
			$pageSize = 20;
			$sql = "select id, card_no, price, points, gen_time, activate_time, charge_user from card where card_no like '%$kw%' and ".$fsql." limit ".$pageSize*($page-1).",".$pageSize."";
			$rs = $model->query($sql);
			return $rs;
		}
		
		//查找符合条件的总数
		public function searchCount($kw,$filter){
			$model = new \Think\Model();
			switch($filter){
				case 'all':$fsql = "1=1";break;
				case 'normal':$fsql = "activate_time is not null";break;
				case 'expired':$fsql = "activate_time is null";break;
				default:$fsql = "1=1";
			}
			$pageSize = 20;
			$sql = "select count(*) from card where card_no like '%$kw%' and ".$fsql;
			$rs = $model->query($sql);
			$total = $rs[0]['count(*)'];
			$tp = $total/$pageSize;
			return ceil($tp);
		}
		//其它如用户资料修改，添加新用户，充值续期等，待完善。

		// 用户报表
		public function countUser() {
			$model = new \Think\Model();
			$sql = "select CASE WHEN b.type IS NULL THEN  'unactive' ELSE b.type END as type,count(*) as count from user_user a left join (select uid as user, (case when expiredate is null then 'unactived' when expiredate > now() then 'active' else 'expired' end) as type from account) b on a.user=b.user group by b.type";
			$rs = $model->query($sql);
			return $rs;
		}
	}
?>