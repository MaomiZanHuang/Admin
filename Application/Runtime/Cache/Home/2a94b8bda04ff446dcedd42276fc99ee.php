<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
	<meta charset='utf-8'/>
	<link rel='stylesheet' href='/mzz/Public/lib/bootstrap/css/bootstrap.css'>
	<style type='text/css'>
	*,body{
		font-family:'微软雅黑';
	}
	.navbar {
    padding: 15px 0;
    margin: 0;
    background: #fff;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

.navbar-fixed-top {
    top: 0;
    border-width: 0 0 1px;
}
.navbar-fixed-top, .navbar-fixed-bottom {
    position: fixed;
    right: 0;
    left: 0;
    z-index: 1030;
}
.navbar {
    position: relative;
    min-height: 50px;
    margin-bottom: 20px;
    border: 1px solid transparent;
		position:fixed;
		top:0;
}
.navbar-header img{
	width:54px;
	height:54px;
	float:left;
}
.navbar .name-logo{
	margin-left:20px;
	line-height:54px;
	font-size:30px;
	color:rgb(237,134,138);
	float:left;
}
footer{
	height:50px;
	color: #959595;
	line-height:50px;
  background: #242E35;
}
#navbar .active{
	border-bottom:solid 4px rgb(228,62,28); 
}
#navbar li:not(.active) :hover{
	border-bottom:solid 4px rgb(228,134,158); 
}

.video-img{
	height:400px;
	border:1px dashed #f00;
}
.content section{
	padding:105px 0 30px 0;
}
.content .des{
	background:#f1f1f1;
}
.content .tutorial{
	background:rgb(91,192,222);
}
.content .contact{
	background:#242E35;
	//background:rgb(179,129,59);
}
.contact .head{
	color:rgb(179,129,59);
	margin-top:30px 0px;
}
.contact{
	color:#fff;
}
.contact .cpy{
	padding-top:50px;
	color:rgb(179,129,59);
}
</style>
<script type='text/javascript' src='/mzz/Public/lib/bootstrap/js/jquery.js'></script>
<script type='text/javascript'>
$(function(){
	//计算屏幕高度自动调整
	$('#navbar li').on('click', function(){
		var section = $(this).attr("ref");
		$('#navbar li').removeClass('active');
		$(this).addClass("active");
	});
});
</script>
<body>
	<!--导航栏开始-->
	<nav class="navbar navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
					<img src='/mzz/Public/img/logo.png'/>
					<div class="name-logo">蔷薇</div>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-right">
					<li class="active"><a href="#desc" ref='desc'>下載</a></li>
					<li><a href="#tutorial" ref='tutorial'>使用教程</a></li>
					<li><a href="#contact" ref='contact'>聯絡我們</a></li>
				</ul>
			</div>
		</div>
	</nav>
	<!--导航栏结束-->
	<!---中间内容开始--->
	<div class="content">
		<section id='desc' class="desc">
			<div class="container">
			<div class="row">
				<div class="col-md-5">
					<h1 style='font-family:楷體'>往前一步，海闊天空</h1>
					<p>網路，本該無界。</p>
					<p>薔薇瀏覽器插件VPN，</p>
					<p>簡單易用的科學上網服務，</p>
					<p>為國際化的互聯網工作學者量身打造</p>
					
					<button class="btn btn-info btn-lg">安装 Install <i class=' glyphicon glyphicon-download-alt'></i></button>
					<button class="btn btn-info btn-lg">Google Play <i class='glyphicon glyphicon-circle-arrow-right'></i></button>
				</div>
				<div class="col-md-7">
					<div class='video-img'></div>
				</div>
			</div>
		</div>
		</section>
		<section id='tutorial' class="tutorial">
			<div class='container'>
				<div class='media'>
					<div class='media-body'>
						<h4 class='media-heading'>如何使用How<span class='glyphicon glyphicon-question-sign'></span></h4>
						<div class='text-title'>第一步：安装.</div>
						打开扩展中心，以chrome为例，菜单>更多工具>扩展程序，或直接网址输入chrome://extensions。(其它国产浏览器可以跳过此步骤)
						把下载的扩展文件拖进该页面。
						<img src='/mzz/Public/img/home/install.png'>
						<div class='text-title'>第二步:注册账号</div>
						初次安装，会自动打开注册页面，只需要填写相关注册信息即可完成注册。
						<div class='text-title'>第三步:使用</div>
						点击扩展图标，选择一个代理节点，切换到一直模式，即可轻松访问google,facebook,youtube,twitter等网站了,
						是不是很简单呢？
						<img src='/mzz/Public/img/home/facebook.png'>
					</div>
				</div>
			</div>
				
		</section>
		<section id='contact' class="contact">
			<div class='container'>
				<div class='row'>
				<div class='col-md-6'>
				<div class='media'>
					<div class='media-body'>
						<h4 class='head'>聯絡我們<span class='glyphicon glyphicon-home'></span></h4>
						<address>
							<p>官方QQ群：<abbr title="Phone">289307257</abbr></p>
							<p>有任何問題，歡迎與客服QQ洽詢<p>
							<p><span class='glyphicon glyphicon-user'></span>客服1-小李子：2508547386</p>
							<p><span class='glyphicon glyphicon-user'></span>客服2-Telanx：1241818518</p>
							
						</address>
					</div>
				</div>
				</div>
				<div class='col-md-6 cpy'>
					<h3 class='text-center'>©蔷薇 2016</h3>
					<p class="text-center">Powered By Telanx</p>
				</div>
				</div>
				
			</div>
		</section>
	</div>
	<!--中间内容结束-->
</body>
</html>