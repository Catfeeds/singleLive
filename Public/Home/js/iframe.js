$(function(){


/*基本信息弹窗*/
$('.astb1').click(function(){
layer.open({
  type: 1,
  title: false,
  closeBtn: 1,
  area: ['420px', 'auto'], //宽高
  content: $('#enction').html()
});
})
$('.astb2').click(function(){
layer.open({
  type: 1,
  title: false,
  closeBtn: 1,
  area: ['420px', 'auto'], //宽高
  content: $('#enction1').html()
});
})
$('body').on('click','.dister_but_2',function(){
	layer.msg('提交成功'); 
	layer.closeAll('page');
})
$('body').on('click','.dister_but_1',function(){
	layer.closeAll('page');
})
/*通用弹窗*/
$('.tcon').click(function(){
	var msg = $(this).attr('data-msg');
	layer.confirm(msg, 
		{
			skin: 'sancu',
			closeBtn: 0,
			area: ['200px', 'auto'],
			 //宽高
			title:'提示信息'
		}, function(index){
			  layer.closeAll();
	      layer.msg('提交成功',{time: 2000 });
	});
});  

/*优惠券说明弹窗*/
$('.sinopr').click(function(){
layer.open({
  type: 1,
  title: false,
  skin: 'wert', //样式类名
  closeBtn: 2, //不显示关闭按钮
  anim: 2,
  area: ['600px', '300px'],
  shadeClose: true, //开启遮罩关闭
  content: '我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明我是说明...'
});
});  


/*优惠券选项卡*/

$('.fr_coupon ul li').click(function(){
	$(this).addClass('web_cou_aver').siblings().removeClass('web_cou_aver')
	
})

$('.fr_coupon1 ul li').click(function(){
	$(this).addClass('web_cou_aver').siblings().removeClass('web_cou_aver')
	
})


if($('.tbStu').length>0){
	
	$(".tbStu tr:nth-child(even)").addClass("trOdd");
}

$('.kopt').click(function(){
	layer.msg('领取成功',{time: 2000 });
})


})



 













