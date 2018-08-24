<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
  <head>
    <title>云梯|管理后台</title>
    <meta name="keywords" content="云梯;VPN" />
    <meta name="description" content="翻墙;VPN" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1.0,user-scalable=no">
    <link rel="stylesheet" href="/mzz/Public/lib/pintuer/pintuer.css" />
		<link rel="stylesheet" href="/mzz/Public/css/menu.css" />
		<style type="text/css">
		p.bottom-title {
			line-height: 60px;
			font-size: 1.5em;
		}
		</style>
    <script src="/mzz/Public/lib/pintuer/jquery.js"></script>
    <script src="/mzz/Public/lib/pintuer/pintuer.js"></script>
    <!--[if lt IE 8]>
		<script src="/mzz/Public/lib/pintuer/respond.js"></script>
		<![endif]-->
		<script type='text/javascript' src="//cdn.bootcss.com/echarts/3.6.1/echarts.min.js"></script>
		<script type="text/javascript">
		var userCount = <?php echo ($userCount); ?>;
		var incomeList = <?php echo ($incomeList); ?>.reverse();
		var getCount = type => (userCount.filter(x => x.type === type)[0] || {count: 0}).count;
		$(function() {
			$('#vip-count').text(getCount('active'));
			$('#user-count').text(userCount.map(x => x.count).reduce(function (prev, next) {
				return (+prev) + (+next);
			}, 0));
			var myChart = echarts.init(document.getElementById('user-report'));
			// 指定图表的配置项和数据
			var option = {
				title : {
						text: '用户人数报表',
						subtext: '',
						x:'center'
				},
				tooltip : {
						trigger: 'item',
						formatter: "{a} <br/>{b} : {c} ({d}%)"
				},
				legend: {
						orient: 'vertical',
						left: 'bottom',
						data: ['VIP付费用户', '已过期用户', '未激活用户']
				},
				series : [
						{
								name: '用户比例构成',
								type: 'pie',
								radius : '55%',
								center: ['50%', '50%'],
								data:[
										{value: Number(getCount('active')), name:'VIP付费用户' + getCount('active')},
										{value: Number(getCount('expired')), name:'已过期用户' + getCount('expired')},
										{value: Number(getCount('unactive')), name:'未激活用户' + getCount('unactive')}
								],
								itemStyle: {
										emphasis: {
												shadowBlur: 10,
												shadowOffsetX: 0,
												shadowColor: 'rgba(0, 0, 0, 0.5)'
										}
								}
						}
				]
			};
			// 使用刚指定的配置项和数据显示图表。
			myChart.setOption(option);


			// 第二个报表
			var myChart2 = echarts.init(document.getElementById('income-report'));
			var option2 = {
					title: {
						text: '近12个月收入趋势',
						x: 'center'
					},
					color: ['#3398DB'],
					tooltip : {
							trigger: 'axis',
							axisPointer : {            // 坐标轴指示器，坐标轴触发有效
									type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
							}
					},
					grid: {
							left: '3%',
							right: '4%',
							bottom: '3%',
							containLabel: true
					},
					xAxis : [
							{
									type : 'category',
									data : incomeList.map(x => x.month),
									axisTick: {
											alignWithLabel: true
									}
							}
					],
					yAxis : [
							{
									type : 'value'
							}
					],
					series : [
							{
									name:'收入',
									type:'bar',
									barWidth: '60%',
									label: {
											normal: {
													show: true,
													position: 'top'
											}
									},
									data: incomeList.map(x => x.income)
							}
					]
			};
			myChart2.setOption(option2);
		});
		</script>
	</head>
<body>
  <!---导航栏-->
	<!--此处要传入当前登录用户的用户名-->
  <div id='nav-top'>
		<div class='nav-center'>
			<div class='nav-logo hidden-l fleft'><img src='/mzz/Public/img/logo.png' width=50px height=50px /></div>
			<div class='nav-title fleft'>云梯</div>
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
	<dd class='menu-s index <?php if((index == index)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Index/index');?> class='icon-home'>开始</a></dd>	
	<!--
	<dd class='menu-s node <?php if((index == node)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Node/index');?> class='icon-sitemap'>节点管理</a></dd>
	<dd class='menu-s cashier <?php if((index == cashier)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Cashier/index');?> class='icon-users'>收银员管理</a></dd>
	<dd class='menu-s cash <?php if((index == cash)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Cash/index');?> class='icon-cny'>充值续费</a></dd>
	<dd class='menu-s system <?php if((index == system)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/System/index');?> class='icon-gears (alias)'>系统配置</a></dd>
	-->
	<dd class='menu-s user <?php if((index == user)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Goods/index');?> class='icon-users'>商品管理</a></dd>
	<dd class='menu-s pubg <?php if((index == pubg)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Card/index');?> class='icon-gears (alias)'>卡密管理</a></dd>
	<dd class='menu-s'><a href=<?php echo U('Admin/Login/logout');?> class='icon-sign-out'>退出登录</a></dd>					
	<dd class='cpy text-center'><p>云梯后台管理</p></dd>
</dl>
				</div>
				
				<div class='x10 content'>
					<!-- 业务数据报表 -->
					<!-- 用户数量 -->
					<div class="line">
						<div class="x8" id="user-report" style="height:300px"></div>
						<div class="x4">
							<p class="text-center" style="font-size:2em;line-height:50px;margin-top: 100px;"><font color="#f00" id="vip-count">-</font>/<font color="#00f" id="user-count">-</font></p>
							<p class="text-center" style="font-size:1.2em">VIP/总用户</p>
						</div>
					</div>
					<div class="line">
						<div id="income-report" style="height: 300px;"></div>
						<p class="bottom-title text-center">总收入:<font color="#f00" id="total-income"><?php echo ($totalIncome); ?></font></p>
					</div>
					<!-- 收入报表 -->
				</div>
			</div>
		</div>
	</div>
		
 <!---footer--->
	<div id='footer'>
		&#169;云梯 2018
</div>	
	</body>
</html>