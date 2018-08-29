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
		</style>
    <script src="/mzz/Public/lib/pintuer/jquery.js"></script>
    <script src="/mzz/Public/lib/pintuer/pintuer.js"></script>
		<script src="/mzz/Public/lib/popup.js"></script>
    <!--[if lt IE 8]>
		<script src="/mzz/Public/lib/pintuer/respond.js"></script>
		<![endif]-->
		<script type='text/javascript'>
		$(function(){
			var popup =(new Popup()).getInstance();
			var userCharge = {
				user: '',
				points: 0,
				card: ''
			};
			// 预选充值选项
			$('.pre-charge button').click(function(e) {
				$('.pre-charge button').removeClass('active');
				$(e.target).addClass('active');
				var type = $(e.target).attr('data-type');
				var addTime = {
					n: 0,
					type: 'hour',
					price: 0
				};
				switch(type) {
					case 'day':
						Object.assign(addTime, { n: 1, type: 'day', price: 5 });
						break;
					case 'month':
						Object.assign(addTime, { n: 1, type: 'month', price: 15 });
						break;
					case 'quarter':
						Object.assign(addTime, { n: 3, type: 'month', price: 40 });
						break;
					case 'year':
						Object.assign(addTime, { n: 12, type: 'month', price: 115 });
						break;
					default: undefined;
				}
				$('input[name=chargeTime]').val(addTime.n);
				$('select[name=unit').val(addTime.type === 'day' ? 'd' : 'm');
				$('input[name=cash]').val(addTime.price);
				$('input[name=chargeTime]').change();
				
			});
			$(".btn-search").click(function(){
				var user = $("#user").val().trim();
				if(user===""){
					popup.showTip("用户名不能为空！");
					setTimeout(function(){popup.hideTip();},1000);
					return false;
				}
				popup.showTip("正在查询用户...");
				$.getJSON("<?php echo U('Admin/User/getUser');?>&user="+user,function(r){
					userCharge.user = r[0].user;
					popup.hideTip();
					if(r.length==0){
						$(".btn-charge").prop("disabled",true);
						$(".user-charge").html("");
						$("tbody").html("<tr><td colspan='4' class='text-red text-center'>无任何结果！</td></tr>");
						return false;
					}else{
						$(".btn-charge").prop("disabled",false);
						var user = r[0];
						$(".user-charge").html(user.user);
						$("tbody").html("<tr><td>"+user.user+"</td><td>"+user.qq+"</td><td>"+user.points+"</td></tr>");
					}
				});
				
			});


			userCharge.points = $('select[name=points]').val();
			$("select[name=points]").change(function(){
				userCharge.points=$(this).val();
			});
			
			$(".btn-charge").click(function(e){
				if ($(this).hasClass('charge1')) {
					userCharge.card = $("input[name=card]").val();
					if (!userCharge.card.trim()) {
						popup.showTip('卡密不能为空！');
						setTimeout(function(){popup.hideTip();},1000);
						return false;
					}
				} else {
					userCharge.card = void 0;
				}

				$.post("<?php echo U('Admin/Cash/recharge');?>", userCharge ,function(r){
					popup.showTip(r.msg||r);
					setTimeout(function(){popup.hideTip();},1000);
					if(r.status==1){
						setTimeout(function(){
							location.reload();
						},1000);
					}
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
	<dd class='menu-s index <?php if((cash == index)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Index/index');?> class='icon-home'>开始</a></dd>	
	<!--
	<dd class='menu-s node <?php if((cash == node)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Node/index');?> class='icon-sitemap'>节点管理</a></dd>
	<dd class='menu-s cashier <?php if((cash == cashier)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Cashier/index');?> class='icon-users'>收银员管理</a></dd>
	
	<dd class='menu-s system <?php if((cash == system)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/System/index');?> class='icon-gears (alias)'>系统配置</a></dd>
	-->
	<dd class='menu-s cash <?php if((cash == cash)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Cash/index');?> class='icon-cny'>充值续费</a></dd>
	<dd class='menu-s user <?php if((cash == user)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Goods/index');?> class='icon-users'>商品管理</a></dd>
	<dd class='menu-s pubg <?php if((cash == pubg)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Card/index');?> class='icon-gears (alias)'>卡密管理</a></dd>
	<dd class='menu-s'><a href=<?php echo U('Admin/Login/logout');?> class='icon-sign-out'>退出登录</a></dd>					
	<dd class='cpy text-center'><p>拇指赞后台管理</p></dd>
</dl>
				</div>
				
				<div class='x10 content'>
					<h3>用户充值</h3>
					<hr>
					<div class="search-form" onsubmit="return false;">
						<form style='max-width:500px;margin:0 auto;'>
							<input type='text' class='input' placeholder='请输入用户名' id='user' style='width:60%;border-radius:0;float:left'>
							<button class='button bg-main btn-search' class='float:left'>搜索</button>
							
						</form>
					</div>
					<div class="userinfo">
						<table class="table">
						<thead>
							<tr class="th">
								<th>账号</th><th>QQ</th><th>积分余额</th>
							</tr>
						</thead>
							<tbody>
							<tr>
								<tr><td colspan='4' class='text-red'></td></tr>
							<tr>
							</tbody>
						</table>
					</div>
					<hr>
					<div class="recharge-form panel" style=''>
						<div class="panel-head">选择<span class='text-red user-charge'></span>卡密充值:</div>
						<div class="panel-body">
						<div class='line'>
							<input type='text' name="card" class='input' placeholder='请输入卡密' style='width:60%;border-radius:0;float:left'>
							
							<button class="button bg-main btn-charge charge1" disabled>充值</button></div>
						</div>
					</div>
					<hr/>
					<div class="recharge-form panel" style='margin-bottom:50px'>
						<div class="panel-head">选择<span class='text-red user-charge'></span>充值:</div>
						<div class="panel-body">
						<div class='line'>
							充值积分:
							<select style="height:34px;" name="points">
								<?php if(is_array($chargeOptions)): $i = 0; $__LIST__ = $chargeOptions;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$charge): $mod = ($i % 2 );++$i;?><option value="<?php echo ($charge["points"]); ?>"><?php echo ($charge["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
							</select>
							<button class="button bg-main btn-charge charge2" disabled>充值</button></div>
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