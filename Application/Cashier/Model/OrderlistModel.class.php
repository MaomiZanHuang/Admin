<?php
	namespace Cashier\Model;
	use Think\Model;
	class OrderlistModel extends Model{
		protected $tableName = 'orderlist';

		// 获取最近12个月收入
		public function getIncomeList($user){
			$model = new \Think\Model();
			if ($user) {
				$addSql = " and operator='".$user."'";
			} else {
				$addSql = "";
			}
			$sql = "
			select a.month,ifnull(b.cash,0) as income from (select DATE_FORMAT(curdate(),'%Y-%m') as month 
			union select DATE_FORMAT(curdate() - INTERVAL 1 MONTH,'%Y-%m') as month
			union select DATE_FORMAT(curdate() - INTERVAL 2 MONTH,'%Y-%m') as month
			union select DATE_FORMAT(curdate() - INTERVAL 3 MONTH,'%Y-%m') as month
			union select DATE_FORMAT(curdate() - INTERVAL 4 MONTH,'%Y-%m') as month
			union select DATE_FORMAT(curdate() - INTERVAL 5 MONTH,'%Y-%m') as month
			union select DATE_FORMAT(curdate() - INTERVAL 6 MONTH,'%Y-%m') as month
			union select DATE_FORMAT(curdate() - INTERVAL 7 MONTH,'%Y-%m') as month
			union select DATE_FORMAT(curdate() - INTERVAL 8 MONTH,'%Y-%m') as month
			union select DATE_FORMAT(curdate() - INTERVAL 9 MONTH,'%Y-%m') as month
			union select DATE_FORMAT(curdate() - INTERVAL 10 MONTH,'%Y-%m') as month
			union select DATE_FORMAT(curdate() - INTERVAL 11 MONTH,'%Y-%m') as month
			)a left join (select DATE_FORMAT(otime, '%Y-%m') as month, sum(cash) as cash from orderlist where status=1".$addSql." group by month) b on a.month=b.month
			";
			return $model->query($sql);
		}

		// 获取总收入
		public function getTotalIncome($user) {
			if ($user) {
				$addSql = " AND operator='".$user."'";
			} else {
				$addSql = "";
			}
			return $this->where('status = 1'.$addSql)->sum('cash');
		}
	}
?>
