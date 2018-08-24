<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
  <head>
    <title>全网通|管理员登录</title>
    <meta name="keywords" content="全网通;VPN" />
    <meta name="description" content="翻墙;VPN" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1.0,user-scalable=no">
    <link rel="stylesheet" href="/mzz/Public/lib/pintuer/pintuer.css">
		<style type='text/css'>
		#nav-top{
			height:60px;
			padding-top:5px;
			box-shadow: #aaa 1px 1px 30px;
		}
		.nav-center{
			width:80%;
			margin:0 auto;
		}
		.nav-title{
			font-size:30px;
			line-height:50px;
		}
		.nav-reg{
			line-height:50px;
		}
		.fleft{
			float:left;
		}
		.fright{
			float:right;
		}
	.wrap{
		max-width:730px;
		height:400px;
		margin:0 auto;
		padding-top:5%;
		//box-shadow: 0 0 1px 1px rgba(0,0,0,0.25);
	}
	.img,.login{
		float:left;
	}
	.login{
		max-width:380px;
		overflow:hidden;
	}
	.slidepage{
		width:760px;
	}
	.login-form{
	 width:380px;
	 float:left;
	 
	}
	.login-form form{
		padding:20px 20px 0 20px;
	}
	.getpwd{
		width:380px;
		float:left;
	}
	.form-title{
		padding-top:5px;
		margin-bottom:10px;
		height:30px;
	}
	.form-group .x3{
		margin-top:8px;
	}
	.backlogin{
		margin-top:170px;
	}
		
		
		#footer{
			position:fixed;
			bottom:0;
			width:100%;
			background:rgb(238,238,238);
			height:50px;
			line-height:50px;
			font-size:15px;
			text-align:center;
		}
		</style>
    <script src="/mzz/Public/lib/pintuer/jquery.js"></script>
    <script src="/mzz/Public/lib/pintuer/pintuer.js"></script>
    <!--[if lt IE 8]>
		<script src="/mzz/Public/lib/pintuer/respond.js"></script>
		<![endif]-->
		<script type='text/javascript'>
			$(function(){
			var msgNode = $('.error');
			if(msgNode.length){
				for(i=0;i<3;i++){
				msgNode.animate({'marginLeft':'-10px'},30).animate({'marginLeft':'0px'},30).animate({'marginLeft':'10px'},30).animate({'maeginLeft':'0px'},30);
					}
				setTimeout(function(){msgNode.animate({opacity:0},1000).remove()},2000);
			}
	$('.gopwd').click(function(){
		$('.slidepage').animate({"marginLeft":"-380px"},500);
	})
	$('.backlogin').click(function(){
		$('.slidepage').animate({"marginLeft":"0px"},500);
	})
	//更换验证码
	$('.verify').click(function(){
		$(this).find('img').attr('src',"<?php echo U('Home/User/verifycode');?>");
	})
	
	});
		</script>
	</head>
<body>
  <!---导航栏-->
  <div id='nav-top'>
		<div class='nav-center'>
			<div class='nav-logo hidden-l fleft'><img src='/mzz/Public/img/logo.png' width=50px height=50px /></div>
			<div class='nav-title fleft'>全网通</div>
			<div class='nav-reg fright'></div>
		</div>
  </div>
  
  <!---中部内容区--->
  <div id='content'>
		<div class='wrap'>
				
					<div class='hidden-l img xl0'><img style="width:330px;height:320px;" src="http://pic.baike.soso.com/p/20101020/20101020171427-853952265.jpg"/></div>
					<div class='login xl12'>
						<div class='slidepage'>
						<div class='login-form'>
						<!---登录框开始-->
						<div class='text-center bg-main bg-inverse form-title'><span class='icon-users'></span>管理员登录</div>
						<form  action="<?php echo U('Admin/Login/index');?>" method='POST' class='form form-x'>
									
									<div class='form-group'>
											<div class='x3'>用户名</div>
											<div class='x9'>
												<input type='text' name='user' class='input' data-validate="required:必填">
											</div>
									</div>
									
									<div class='form-group'>
										<div class='x3'>密码</div>
										<div class='x9'>
											<input type='password' name='pwd' class='input' data-validate="required:必填">
										</div>
									</div>
									
									<div class='form-group'>
										<div class='x3'>验证码</div>
										<div class='x5'>
											<input type='text' name='verifycode' class='input' data-validate="required:必填">
										</div>
										<div class='x4'>
											<a href=# class='verify'>
												<img height=33px src=<?php echo U('Home/User/verifycode');?>/>
											</a>
										</div>
									</div>
								
									<br/>
									<div class='form-group'>
										<div class='x3'></div>
										<div class='x9'>
											<button type='submit' class='button button-block bg-main'>登录</button>
										</div>
									</div>
									<p class='text-right gopwd'><a href="#">忘记密码？→</a></p>
								</form>
								<div class='alert alert-red text-center error' <?php echo ($error?'':'hidden'); ?>><?php echo ($error); ?></div>
						<!--登录框结束--->
						</div>
						
						<!--忘记密码---->
						<div class='getpwd login-form'>
							<div class='text-center bg-main bg-inverse form-title'><span class='icon-key'></span>找回密码</div>
							<form>
							<p>Tips:暂不支持自助找回密码，请联系管理员！</p>
							<p class='text-left backlogin'><a href="#">←返回登录</a></p>
							</form>
						</div>
						<!--忘记密码结束-->
						</div>
					</div>
				</div>
	</div>
		
				
 <!---footer--->
  <div id='footer'>
		CopyRight2015 全网通
  </div>

		
	</body>
</html>