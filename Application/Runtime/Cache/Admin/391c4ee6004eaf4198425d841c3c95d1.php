<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
<link rel='stylesheet' href='/mzz/Public/lib/pintuer/pintuer.css' />
<script src="/mzz/Public/lib/pintuer/jquery.js"></script>
<script type="text/javascript">
$(function() {
	var GOODS = <?php echo ($goods); ?>;

	$("select[name=cata_id]").val(GOODS.cata_id);
	$("input[name=goods_id]").val(GOODS.goods_id);
	$("input[name=title]").val(GOODS.title);
	$("input[name=logo]").val(GOODS.logo);
	$("input[name=pics]").val(JSON.parse(GOODS.pics || '[]').join('\n'));
	$("textarea[name=detail]").val(GOODS.detail);
	
	$("select[name=api_method]").val(GOODS.api_method);
	$("input[name=api_host]").val(GOODS.api_host);
	$("textarea[name=api_fixed_params]").val(GOODS.api_fixed_params);
	$("textarea[name=api_extra_params]").val(GOODS.api_extra_params);
	$("select[name=business_cata]").val(GOODS.business_cata);

});
</script>
</head>
<body>
<div class="text-center"> 
	<font color=#f00><?php echo ($msg); ?></font>
</div>
<form method="post" class="form-x" action="">
	<div class="form-group">
			<div class="label">
				<label for="username">所属分类</label>
			</div>
			<div class="field">
				<div class="line">
					<div class="xl6">
						<select class="input" name="cata_id" value="<?php echo ($goods["cata_id"]); ?>">
							<?php if(is_array($catas)): $i = 0; $__LIST__ = $catas;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cata): $mod = ($i % 2 );++$i;?><option value="<?php echo ($cata["cata_id"]); ?>"><?php echo ($cata["title"]); ?>[<?php echo ($cata["cata_id"]); ?>]</option><?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
				<div class="label">
					<label for="password">商品ID</label>
				</div>
				<div class="field">
					<div class="line">
						<div class="xl6">
							<input type="text" class="input" name="goods_id" size="30" placeholder="商品ID，请按分类来">
						</div>
					</div>
				</div>
			</div>
		<div class="form-group">
			<div class="label">
				<label for="password">商品名</label>
			</div>
			<div class="field">
				<div class="line">
					<div class="xl6">
						<input type="text" class="input" name="title" size="30" placeholder="商品标题">
					</div>
				</div>
			</div>
		</div>
		
		<div class="form-group">
			<div class="label">
				<label for="email">LOGO</label>
			</div>
			<div class="field">
				<div class="line">
						<div class="xl6">
							<input type="text" class="input" name="logo" size="30" placeholder="用于logo地址">
						</div>
						<div>
							<img id="logo" width="60" height="60" src="" />
						</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="label">
				<label for="email">图片描述</label>
			</div>
			<div class="field">
					<div class="line">
							<div class="xl6">
								<textarea type="text" class="input" name="pics" size="30" placeholder="一行一个图片地址"></textarea>
							</div>
						</div>
			</div>
		</div>
			<div class="form-group">
				<div class="label">
					<label for="email">商品描述</label>
				</div>
				<div class="field">
						<div class="line">
								<div class="xl6">
					<textarea type="text" class="input" name="detail" rows="4" placeholder="支持HTML标签"></textarea>
					</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="label">
					<label for="email">请求类型</label>
				</div>
				<div class="field">
					<div class="line">
						<div class="xl6">
								<select class="input" name="api_method">
										<option value="POST">POST</option>
									</select>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="label">
					<label for="email">请求HOST</label>
				</div>
				<div class="field">
						<div class="line">
								<div class="xl6">
					<input type="text" class="input" name="api_host" size="30" placeholder="请求API地址">
					</div>
					</div>
		
				</div>
			</div>
			<div class="form-group">
				<div class="label">
					<label>固定参数</label>
				</div>
				<div class="field">
						<div class="line">
								<div class="xl6">
									<textarea  class="input" name="api_fixed_params" rows="4" placeholder="JSON格式固定参数"></textarea>
									<p id="api_fixed_params_error"  class="text-red" style="display: none">JSON格式不正确,不能用单引号，必须双引号</p>
								</div>
					</div>
		
				</div>
			</div>
			<div class="form-group">
				<div class="label">
					<label for="email">动态参数</label>
				</div>
				<div class="field">
						<div class="line">
								<div class="xl6">
					<textarea class="input" name="api_extra_params" rows="4" placeholder="JSON格式参数映射"></textarea>
					<p id="api_extra_params_error" class="text-red" style="display: none">JSON格式不正确,不能用单引号，必须双引号</p>
					</div>
					
					<div class="xl6">
					示例:
					{
						qq: 'qq',
						needs_num_0: 'amt'
					}
					数量amt QQ号qq 说说shuoshuo_id 抖音作品douyin_id 全民K歌作品song_id
					</div>
					</div>
		
				</div>
			</div>
			<div class="form-group">
					<div class="label">
						<label for="email">业务类型</label>
					</div>
					<div class="field">
							<div class="line">
									<div class="xl6">
						<select class="input" name="business_cata">
							<option value="QQ">QQ</option>
							<option value="QQ_SHUOSHUO">QQ_SHUOSHUO</option>
							<option value="DOUYIN">DOUYIN</option>
							<option value="QUANMIN_KGE">QUANMIN_KGE</option>
							<option value="WEIBO_FANS">WEIBO_FANS</option>
							<option value="WEIBO_BLOG">WEIBO_BLOG</option>
						</select>
						</div>
						<div class="xl6">
							说明：QQ(需要输入QQ号)QQ_SHUOSHUO(需要输入QQ号和说说ID)DOUYIN(需要输入抖音作品)
							QUANMIN_KGE(需要输入全民作品)WEIBO_FANS(需要输入微博主页)WEIBO_BLOG(需要输入博文地址)
						</div>
					</div>
					</div>
				</div>
		<div class="form-button">
			<button class="button bg-main" type="submit">提交</button>
		</div>
</form>

</body>
</html>