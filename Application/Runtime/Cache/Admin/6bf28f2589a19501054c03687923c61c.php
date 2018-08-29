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
		$(function(){
			var t = 0;	//总页数
			var pagination = function(o,d){
				var p_c = o.current,p_t = o.total;
				if(p_c==1)d.page_prev.hide();
				else d.page_prev.show();
				if(p_c==p_t)d.page_next.hide();
				else d.page_next.show();
				d.page_num.html(p_c+'/'+p_t);
			};	
		//从后台获取用户结果
		function search_admin(key,filter,page,callback){
			
			$.post("<?php echo U('Admin/Card/search_handler');?>",{type:'search',keywords:key,filter:filter,page:page},callback);
		}
		
		//点击搜索永远是获取第一页数据
		$('form').submit(function(e){
			e.preventDefault();
			R_H($(e.target).find('#keywords').val(),$(e.target).find('#filter').val(),1);
			
		});

		
		
			//分页组件事件处理
	
			var l=$('.btn-page-prev'),
			r=$('.btn-page-next'),
			g=$('.btn-page-go');
			l.click(function(){
				var pageNum = $('.page-num').text().split('/');
				var c=parseInt(pageNum[0]),t=parseInt(pageNum[1]);
				var g = $('.input-page-go').val();
				if(c==1)return false;
				R_H($('#keywords').val(),$('#filter').val(),c-1);
			});
			r.click(function(){
				var pageNum = $('.page-num').text().split('/');
				var c=parseInt(pageNum[0]),t=parseInt(pageNum[1]);
				var g = $('.input-page-go').val();
				
				if(c==t)return false;
				R_H($('#keywords').val(),$('#filter').val(),c+1);
			});
			g.click(function(){
				var pageNum = $('.page-num').text().split('/');
				var c=parseInt(pageNum[0]),t=parseInt(pageNum[1]);
				
				var g = parseInt($('.input-page-num').val());
				console.log(c,t,g);
				if(!(g<=t&&g>=1&&g)){
					alert('请输入合理的翻页！');
					
					//return false;
				}else
				R_H($('#keywords').val(),$('#filter').val(),g);
				
			});
			
			function R_H(keys,filter,page){
				search_admin(keys,filter,page,function(r){
				//清空数据
				$('.search-result-table tbody>:not(.th)').remove();
				if(!r.status){
					//提示错误！
					alert("查询失败！请重试！");
					return false;
				}
				if(r.rs.length==0){
					$('.search-result-table').append('<tr><td colspan=5>无任何相关结果。<td></tr>');
					$('.page').hide();
					return false;
				}
				var cp = r.cp;	//当前页
				var tp = r.tp;	//总页
				t = parseInt(tp);
				r = r.rs;
				
				var types = {
					chiji: '吃鸡',
					yf: '月费',
					jf: '季度',
					nf: '年费'
				};

				for(var i=0;i<r.length;i++){
					var status = r[i].activate_time;
					var cls = "text-main";
					if(!status){
						cls = "text-gray";
					}
					var type = types[r[i].type];
					$('.search-result-table').append("<tr class="+cls+"><td><input type='checkbox'></td><td>"+r[i].card_no+'</td><td>'+(r[i].charge_user||'-')+'</td><td>'+(r[i].gen_time)+'</td><td>'+(r[i].activate_time||'-')+'</td><td>'+r[i].points+'积分</td></tr>');
				}
				//分页组件
				$('.page').show();
				pagination({current:cp,total:tp},{page_prev:$('.btn-page-prev'),page_next:$('.btn-page-next'),page_num:$('.page-num')});
				
			});
			}		
			
			
			/*
			*增删改操作
			*/
			$('#btn-select-all').change(function(e){
				console.log('改变了');
				var check = e.target.checked;
				var checkboxDom = $('.search-result-table').find('input[type=checkbox]');
				$.each(checkboxDom,function(i,e){
					e.checked = check;
				});
			})
			//公共操作
			var popup;	//弹出层
			var Ext = {
				selectedItem:function(){
					//返回选中项目节点
					var checkBoxs = $('.search-result-table').find('input[type=checkbox]:checked');
					console.log(checkBoxs);
					return checkBoxs;
					
				},
				popupInit:function(){
					
					popup=Popup().getInstance();
				},
				warning:function(title,msg){
					popup.setTitle(title).setBody("<p class='text-center'>"+msg+"</p>").show();
				}
			};
			Ext.popupInit();

			
			//删除用户(可批量操作)
			$('.btn-del').click(function(){
				var selList = Ext.selectedItem();
				if(selList.length==0){
					Ext.warning("删除用户","请至少选择一个用户！");
					return false;
				}
				var userList = [];
				$.each(selList,function(i,e){
					var user = $(e).parent().next().html();
					userList.push(user);
				});
				popup.setTitle("删除提示").setBody("<p class='text-center'>是否要删除用户"+userList[0]+"等"+userList.length+"个用户</p>"+
				"<div style='text-align:center'><button class='button border-black popup-del-confirm'>确定</button><button class='button border-black popup-btn-cancel'>取消</button></div>").show();
			});
			
			$('.popup-body').on('click','.popup-del-confirm',function(e){
				var selList = Ext.selectedItem();
				var userList = [];
				popup.setBody("");
				$.each(selList,function(i,e){
					var user = $(e).parent().next().html();
					(function(user){
						$.post("<?php echo U('Admin/Card/del_ajax');?>",{user:user},function(r) {
							popup.append("<li>"+r.msg+"</li>");
						});
					})(user);
				});
				
				
			});
			//修改用户(不可批量操作)
			$('.btn-modify').click(function(){
				var selList = Ext.selectedItem();
				if(selList.length==0){
					Ext.warning("修改用户","请先选择一个用户！");
					return false;
				}
				if(Ext.selectedItem().length>1){
					Ext.warning("修改用户","一次只能修改一个用户");
					return false;
				}
				//获取用户名
				var user = selList.parent().next().html();
				popup.setTitle("修改用户").setCss({width:'400px',height:'450px'}).setBody("<iframe src='<?php echo U('Admin/User/modify');?>?user="+user+"' width=350 height=430></iframe>").show();
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
	
	<dd class='menu-s system <?php if((pubg == system)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/System/index');?> class='icon-gears (alias)'>系统配置</a></dd>
	-->
	<dd class='menu-s cash <?php if((pubg == cash)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Cash/index');?> class='icon-cny'>充值续费</a></dd>
	<dd class='menu-s user <?php if((pubg == user)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Goods/index');?> class='icon-users'>商品管理</a></dd>
	<dd class='menu-s pubg <?php if((pubg == pubg)): ?>selected<?php endif; ?>'><a href=<?php echo U('Admin/Card/index');?> class='icon-gears (alias)'>卡密管理</a></dd>
	<dd class='menu-s'><a href=<?php echo U('Admin/Login/logout');?> class='icon-sign-out'>退出登录</a></dd>					
	<dd class='cpy text-center'><p>拇指赞后台管理</p></dd>
</dl>
				</div>
				
				<div class='x10 content'>
					
					
					<div class="tab">
						<div class="tab-head">
						<strong>吃鸡申诉管理</strong>
						<ul class="tab-nav">
						<li class="active"><a href="#">卡密查询</a> </li>
						<li><a href="<?php echo U('Admin/Card/add');?>">批量新增卡密</a> </li>
						</ul>
						</div>
						
						<div class="tab-body">
						<div class="tab-panel active">
						<!----content start ------->
							<div class='x10 content'>
					<div class='right-body'>
						<!---搜索区域--->
						<div class='right-search'>
						<form style='max-width:500px;margin:0 auto;'>
							<input type='text' class='input' placeholder='请输入要查询的卡密' id='keywords' style='width:60%;border-radius:0;float:left'>
							<select style='height:34px;border-radius:0px;' id='filter'>
								<option value='all'>全部</option>
								<option value='normal'>已使用</option>
								<option value='expired'>未使用</option>
							</select>
							<button type='submit' class='button' class='float:left'>搜索</button>
							</div>
						</form>
						</div>
						<!--结果区域--->
						<div class='right-result'>
							<div class='right-result-head'><h4>全部结果:</h4></div>
							<div class='right-result-body'>
								<table class="table table-hover search-result-table"><tr class='th'><th>选择</th><th>卡号</th><th>绑定充值账号</th><th>生成时间</th><th>激活时间</th><th>类型</th></tr>
								</table>
								<!---操作选项-->
								<div class='left-op page' style='float:left;display:none;margin:15px 0'>
									<span>
										<input type='checkbox' id='btn-select-all'>全选
										<button class='button border-main btn-del'>删除</button>
										<button class='button border-main btn-modify'>修改</button>
										<button class='button bg-main btn-add'>添加</button>
									</span>
								</div>
								<!---分页组件-->
								<div class='right-result-pagination page' style='float:right;padding-right:100px;margin:15px 0 10px 0;display:none;'>
									<span>
										<button class='button border-main btn-page-prev'><i class='icon-caret-left text-big'></i></button>
										<span class='page-num text-big'>1/1</span>
										<button class='button border-main btn-page-next'><i class='icon-caret-right text-big'></i></button>
										<input type="text" class="input input-auto input-page-num" name="keywords" size="5" placeholder="页数">
										<button class='button bg-main btn-page-go'>转到</button>
									</span>
								</div>
								
							</div>
						</div>
						
						
				</div>
						<!----content end---------->
						
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