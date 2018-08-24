<?php
	namespace Admin\Model;
	use Think\Model;
	class GoodsModel extends Model{
		protected $tableName = 'goods_item'; 
		//搜寻符合条件用户
		public function search($kw,$filter,$page){
			$model = new \Think\Model();
			$pageSize = 20;
			$sql = "select id, cata_id, goods_id, title, logo, pics, detail, sort_index, online, api_extra_params, api_fixed_params,api_host, api_method, business_cata from goods_item where title like '%$kw%' limit ".$pageSize*($page-1).",".$pageSize."";
			$rs = $model->query($sql);
			return $rs;
		}
		
		//查找符合条件的总数
		public function searchCount($kw,$filter){
			$model = new \Think\Model();
			$pageSize = 20;
			$sql = "select count(*) from goods_item where title like '%$kw%' ";
			$rs = $model->query($sql);
			$total = $rs[0]['count(*)'];
			$tp = $total/$pageSize;
			return ceil($tp);
		}
		//其它如用户资料修改，添加新用户，充值续期等，待完善。

	}
?>