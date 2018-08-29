<?php
	namespace Admin\Model;
	use Think\Model;
	class OrderlistModel extends Model{
		protected $tableName = 'balance_changelog';

		// 获取最近12个月收入
		public function getIncomeList(){
			$model = new \Think\Model();
			$sql = "
			select a.month,ifnull(b.cash/100,0) as income, b.type from (select DATE_FORMAT(curdate(),'%Y-%m') as month 
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
			)a left join (select c.month,sum(c.change_amt) as cash, c.type from (SELECT case when remark like '%充值%' then 'charge' else 'adv' end as type, DATE_FORMAT(time, '%Y-%m') as month, change_amt FROM `balance_changelog` where remark like '%充值%' or remark like '%广告%')c GROUP by c.month,c.type) b on a.month=b.month
			";
			return $model->query($sql);
		}
		// 获取今日收入
		public function getTodayIncome() {
			$model = new \Think\Model();
			$sql = "
			select sum(A.amt)/100 as income, A.type from (select case when remark like '%充值%' then 'charge'
				when remark like '%广告%' then 'adv' end as type, change_amt as amt
				from balance_changelog where remark like '%充值%' or remark like '%广告%' and time>curdate()
				) A group by A.type
			";
			return $model->query($sql);
		}
		// 获取总收入
		public function getTotalIncome() {
			return $this->where("remark like '%充值%' or remark like '%广告%'")->sum('change_amt')/100;
		}
	}
?>
