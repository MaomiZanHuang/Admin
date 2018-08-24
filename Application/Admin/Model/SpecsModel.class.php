<?php
	namespace Admin\Model;
	use Think\Model;
	class SpecsModel extends Model {
		protected $tableName = 'goods_spec';
		// 新增规格
		public function addSpec($spec) {
			return $this->add($spec);
		}

		// 更新规格
		public function updateSpec($id, $spec) {
			return $this->where("id='%s'", $id)->data($spec)->save();
		}

		// 删除规格
		public function delSpec($id) {
			return $this->where("id='%s'", $id)->delete();
		}
	}
?>
