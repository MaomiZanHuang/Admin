//调整高度
(function(){window.onload=window.onresize=function(){
		//是否有标题
		var tH = $('#nav-top')?$('#nav-top').outerHeight():0;
		var de = document.documentElement;
		var sH = window.innerHeight||de.innerHeight||(de && de.clientHeight)||document.body.clientHeight;//屏幕可用高度
		console.log(sH);
		var bH = $('#footer').outerHeight();//底部高度
		$('#content').css({'min-height':(sH-bH-tH)+'px'});	
	}
})();
//弹窗
(function(w){
	w.pop=function(mask,container,title,body){
	
	function setTitle(str){
		title.html(str);
		return this;
	}
	function setBody(str){
		body.html(str);return this;
	}
	function append(str){
		body.html(str);return this;
	}	
	function setCss(obj){
		container.css(obj);
		return this;
	}
	function setBodyCss(obj){
		body.css(obj);
		return this;
	}
	function show(){
		//居中位置
		
		var sH = Number(window.innerHeight);//屏幕可用高度
		var cH = parseInt(container.css('height')||100);
		var mH = ch > sH ? 0 : Math.abs(sH-100-cH);
		alert(mh)
		container.css({
			'margin':'auto',
			'margin-top':(mH/2)+'px'
		});
		container.show();
		mask.show();
		return this;
	}
	function hide(t){
		var delay = parseInt(t);
		mask.fadeOut();
		return this;
	}
	return {
		setTitle:setTitle,
		setBody:setBody,
		setCss:setCss,
		setBodyCss:setBodyCss,
		append:append,
		show:show,
		hide:hide
	}
};	
})(window);