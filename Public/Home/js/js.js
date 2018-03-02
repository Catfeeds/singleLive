$(function() {
	/*点击文本框变色*/
	  $(":text").focus(function() {
	    $(this).css('border-color', '#ff9900');
	    
	  }).blur(function() {
	    $(this).css('border-color', '');
	   
	  })
	/*首页切换*/
	$('.web_efnt ul li').click(function() {
		$(this).addClass('inacvr').siblings().removeClass('inacvr')
	})

	/*通用弹窗*/
	$('.tcon').click(function() {
		var msg = $(this).attr('data-msg');
		layer.confirm(msg, {
			skin: 'sancu',
			closeBtn: 0,
			area: ['200px', 'auto'],
			//宽高
			title: '提示信息'
		}, function(index) {
			layer.closeAll();
			layer.msg('提交成功', {
				time: 2000
			});
		});
	});
	/*注册*/
    $('.z_int span').click(function(){
    	if($(this).hasClass('j')){
    		$(this).removeClass('j')
    	$(this).css('background','url(img/zc03.png) no-repeat left center')
    	$(this).find('input').prop('checked',false)
    	}else{
    		$(this).addClass('j')
    	$(this).css('background','url(img/zc03_0.png) no-repeat left center')
    	$(this).find('input').prop('checked',true)
    	}	
    })
    /*客房详情img*/
	$('.GuestRoom_banner_right a').click(function(){
		var Guest = $(this).attr('msg_img')
		$('.GuestRoom_banner_left img').attr('src',Guest)
	})
	$('.joina').click(function(){
		if($(this).hasClass('aop')){
			$(this).parents('.joint').find('.joint_cont').hide()
			$(this).find('img').attr('src',"/Public/Home/img/s1.png")
			$(this).removeClass('aop')
		}else{
			$(this).parents('.joint').find('.joint_cont').show()
			$(this).find('img').attr('src',"/Public/Home/img/s2.png")
			$(this).addClass('aop')
		}
		
	})
	/*套餐*/
	$('.omert ul  li').click(function(){
		var Guest = $(this).attr('msg_img')
		$('.img_tist img').attr('src',Guest)
	});
	$('.appjia').click(function() {
		var pp = $('.appcont');
		var limit = $(this).attr('limit');
		var getNum = parseInt(pp.val());
		if (getNum < parseInt(limit)) {
			pp.val(getNum + 1)
		} else {
			layer.msg('不能超过限购份数', {
				time: 2000
			});
		}
	})
	$('.appjian').click(function() {
		var pp = $('.appcont');
		var getNum = parseInt(pp.val());
		if(getNum > 1) {
			pp.val(getNum - 1)
		} else {
			layer.msg('不能小于1', {
				time: 2000
			});
		}
	})
	
	/*$('.ont_inp_2').click(function() {
		layer.open({
		  type: 1,
		  title: false,
		  skin: 'mindet', //样式类名
		  closeBtn: 0, //不显示关闭按钮
		  anim: 2,
		  shadeClose: true, //开启遮罩关闭
		  content: '剩余房间 ：2105/2016'
		});
	});*/
	/*常见问题*/
	$('.problem_left li').click(function(){
		$(this).addClass('inavcr').siblings().removeClass('inavcr')	
		$('.problem_right>.minorigth:eq(' + $(this).index() + ')').show().siblings().hide();
	})

	/*个人中心基本信息弹窗-开始*/
	/*修改手机号*/
	$('.op').click(function(){
		layer.open({
		  type: 1,
		  title: false,
		  closeBtn: 1,
		  area: ['420px', 'auto'], //宽高
		  content: $('#enction').html()
		});
	});
	/*修改邮箱*/
	$('.op1').click(function(){
		layer.open({
			type: 1,
			title: false,
			closeBtn: 1,
			area: ['420px', 'auto'], //宽高
			content: $('#enction1').html()
		});
	});
	/*个人中心基本信息弹窗-结束*/
	/*$('body').on('click','.dister_but_2',function(){
		layer.msg('提交成功'); 
		layer.closeAll('page');
	})
	$('body').on('click','.dister_but_1',function(){
		layer.closeAll('page');
	})*/
	/*查询*/
	$('.liomt').click(function() {
		layer.open({
		  type: 1,
		  title: false,
		  skin: 'mindet', //样式类名
		  closeBtn: 0, //不显示关闭按钮
		  anim: 2,
		  shadeClose: true, //开启遮罩关闭
		  content: '剩余房间 ：2105/2016'
		});
	});
})



