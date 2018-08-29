<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
  <head>
    <title>拇指赞|管理后台</title>
    <meta name="keywords" content="全网通;VPN" />
    <meta name="description" content="翻墙;VPN" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1.0,user-scalable=no">
    <link rel="stylesheet" href="/mzz/Public/lib/pintuer/pintuer.css" />
		<link rel="stylesheet" href="/mzz/Public/css/menu.css" />
		<style type='text/css'>
		.toolbar{
			margin-top:30px;
			float:left;
		}
		.des{
			margin:30px 0;
			float:right;
		}
		.des .box{
			float:left;
			margin-left:10px;
		}
		.des .block{
			float:left;
			width:30px;
			height:25px;
		}
		</style>
    <script src="/mzz/Public/lib/pintuer/jquery.js"></script>
    <script src="/mzz/Public/lib/pintuer/pintuer.js"></script>
		<script src="/mzz/Public/lib/popup.js"></script>
    <!--[if lt IE 8]>
		<script src="/mzz/Public/lib/pintuer/respond.js"></script>
		<![endif]-->
		<script type='text/javascript'>
		/*
		登金陵凤凰台--李白
		凤凰台上凤凰游，凤去台空江自流。
		吴宫花草埋幽径，晋代衣冠成古丘。
		三山半落青天外，二水中分白鹭洲。
		总为浮云能蔽日，长安不见使人愁。
		*/
		$(function(){
			// 一些操作如生成卡密，清空，导入考密等
			var genRandomCode = function () {
				var genSingleCode = function () {
					var min = 'A'.charCodeAt();
					var max = 'Z'.charCodeAt();
					var num = Math.floor(10*Math.random());
					var char = String.fromCharCode(min + Math.floor((max-min) * Math.random()));
					return Math.random() > 0.6 ? num : char;
				}
				var genRandom4Code = function () {
					return Array.apply(null, {length:4}).map(genSingleCode).join('');
				}
				return Array(6).join('-').split('-').map(genRandom4Code).join('-');
			}
			// 批量生成卡密100张
			$('#gen_card').click(function () {
				var n = 100;
				$('textarea').html(Array.apply(null, {length: n}).map(genRandomCode).join('\n'));
			});
			$('#reset_card').click(function() {
				$('textarea').html('');
			});
			$('#import_card').click(function(){
				var codes = $('textarea').val().split('\n').join(';');
				var fee_type = $('select[name=fee_type]').val();
				$.post("<?php echo U('Admin/Card/add_ajax');?>",{codes, fee_type},function(r) {
					$(".import-result").html(r.msg);
				});
			});
		});
		</script>
	</head>
<body>
  <!---导航栏-->
	<!--此处要传入当前登录用户的用户名-->
  <div id='nav-top'>
		<div class='nav-center'>
			<div class='nav-logo hidden-l fleft'><img src='/mzz/Public/img/logo.png' width=50px height=50px /></div>
			<div class='nav-title fleft'>拇指赞</div>
			<div class='nav-reg fright'><font color=#f00><?php echo ($user["user"]); ?></font>,您好！</div>
		</div>
  </div>
  
  <!---中部内容区--->
  <div id='content'>
		<div class='nav-center'>
			<div class='line'>
				<div class='x2'>
					<!--此处要传入当前选中菜单的class值-->
					<dl class='menu'>
	<dd class='menu-s index <?php if((pubg == index)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Index/index');?> class='icon-home'>开始</a></dd>	
	<!--
	<dd class='menu-s node <?php if((pubg == node)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Node/index');?> class='icon-sitemap'>节点管理</a></dd>
	<dd class='menu-s cashier <?php if((pubg == cashier)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Cashier/index');?> class='icon-users'>收银员管理</a></dd>
	<dd class='menu-s cash <?php if((pubg == cash)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Cash/index');?> class='icon-cny'>充值续费</a></dd>
	<dd class='menu-s system <?php if((pubg == system)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/System/index');?> class='icon-gears (alias)'>系统配置</a></dd>
	-->
	<dd class='menu-s user <?php if((pubg == user)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Goods/index');?> class='icon-users'>商品管理</a></dd>
	<dd class='menu-s pubg <?php if((pubg == pubg)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Card/index');?> class='icon-gears (alias)'>卡密管理</a></dd>
	<dd class='menu-s'><a href=<?php echo U('Admin/Login/logout');?> class='icon-sign-out'>退出登录</a></dd>					
	<dd class='cpy text-center'><p>拇指赞后台管理</p></dd>
</dl>
				</div>
				
				<div class='x10 content'>
					
					
					<div class="tab">
						<div class="tab-head">
						<strong>卡密管理</strong>
						<ul class="tab-nav">
						<li><a href="<?php echo U('Admin/Card/index');?>">卡密查询</a> </li>
						<li class="active"><a href="#">批量新增卡密</a> </li>
						</ul>
						</div>
						
						<div class="tab-body">
						<div class="tab-panel active">
						<!--content start ------->
							<div class='x10 content'>
								<div class='right-body'>
									<!--结果区域--->
									<div class='right-result'>	
										<div class="line">
											<div class="x8">
												<button class="button" id="gen_card">批量生成卡密</button>
												<button class="button bg-red" id="reset_card">清空</button>
												<button class="button bg-main"id="import_card">批量导入到</button>
												<select style="height:34px;border-radius:0px;" id="filter" name="fee_type">
													<?php if(is_array($charge_options)): $i = 0; $__LIST__ = $charge_options;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$option): $mod = ($i % 2 );++$i;?><option value="<?php echo ($option["points"]); ?>"><?php echo ($option["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
												</select>
												<p></p>
												<textarea class="input" rows="20" cols="10" placeholder="按行输入卡密"></textarea>
											</div>
											<div class="x4">
											导入结果:
											<p class="import-result"></p>
											</div>
										</div>					
									</div>
							</div>
						<!-- content end---------->	
						</div>
						</div>
					</div>	
				</div>
			</div>
		</div>
	</div>
		
 <!---footer--->
	<div id='footer'>
		&#169;拇指赞 2018
</div>	
	</body>
</html>