<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
<link rel='stylesheet' href='/mzz/Public/lib/pintuer/pintuer.css' />
<script src="/mzz/Public/lib/pintuer/jquery.js"></script>
<script type="text/javascript">
$(function() {
  var goods_id = '<?php echo ($goods_id); ?>';
	$('.add-spec').click(function(e) {
    var spec = $('input[name=spec]').val().trim();
    var specItems = spec.split('/');
    if (specItems.length !== 4) {
      alert('新增规格格式不正确！');
      return false;
    }

    // 添加成功后刷新本页面
    $.post("<?php echo U('Admin/Goods/addSpec');?>", {
      goods_id: goods_id,
      spec: spec
    }, function(r) {
      alert(r.msg);
      if (r.status) {
        location.reload(); 
      }
    });
  });
  $('.update-spec').click(function(e) {
    var specs = $(e.target).parent().parent();
    var id = specs.find('td:eq(0)').text();
    var title = specs.find('td:eq(1)').text(); 
    var amt = specs.find('td:eq(2)').text(); 
    var rmb = specs.find('td:eq(3)').text(); 
    var points = specs.find('td:eq(4)').text(); 

    $.post("<?php echo U('Admin/Goods/updateSpec');?>", {
      id: id,
      title: title,
      amt: amt,
      rmb: rmb,
      points: points
    }, function(r) {
      alert(r.msg);
      if (r.status)
      location.reload();
    });
    
  });
  $('.del-spec').click(function(e) {
    var specs = $(e.target).parent().parent();
    var id = specs.find('td:eq(0)').text();
    $.post("<?php echo U('Admin/Goods/delSpec');?>", {
      id: id
    }, function(r) {
      alert(r.msg);
      if (r.status)
      location.reload();
    });
  });
});
</script>
</head>
<body>
  <div class="form-group">
    <div class="field">
        <div class="line">
          <div class="xl8"><input name="spec" class="input" placeholder="标题/数量/价格/积分价格"></div>
          <div class="xl4"><button class="button bg-sub add-spec">添加规格</button></div>
        </div>
        <div class="line">
            新增格式: 标题/实际充值数量/RMB价格/积分价格<br/>
            例如: 100赞/100/0.01/32<br/>
        </div>
        <br/>
        <div class="line">
            <table class="table table-bordered">
                <tr><th>ID</th><th>标题</th><th>数量</th><th>价格</th><th>积分价格</th><th>操作</th></tr>
                <?php if(is_array($specs)): $i = 0; $__LIST__ = $specs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$spec): $mod = ($i % 2 );++$i;?><tr>
                    <td><?php echo ($spec["id"]); ?></td><td contenteditable="true"><?php echo ($spec["title"]); ?></td><td contenteditable="true"><?php echo ($spec["amt"]); ?></td><td contenteditable="true"><?php echo ($spec["rmb"]); ?></td><td contenteditable="true"><?php echo ($spec["points"]); ?></td><td><button class="button bg-main update-spec">更新</button><button class="button bg-dot del-spec">删除</button></button></td>
                  </tr><?php endforeach; endif; else: echo "" ;endif; ?>
              </table>
        </div>
        <div class="line">
            <button class="button btn-block bg-main update-spec" size=30>批量更新价格</button>
        </div>
        
        <div class="line">
          <div class="xl8">
              <div class="input-group">
                  <span class="addbtn">
                    <button type="button" class="button">
                    100数量 = </button>
                  </span>
                  <input type="text" class="input" name="keywords" size="50" placeholder="数量100所需积分" />
                </div>
          </div>
          <div class="xl4"><button class="button bg-sub">一键比率设置</button></div>
        </div>
      </div>
      <div class="line">
        
        一键比率设置价格并不会保存，需要点击[批量更新价格]才生效
      </div>
    </div>
    </div>
  </div>
</body>
</html>