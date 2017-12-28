$(function(){

	$('.ajax-phone').blur(function(){
		var db = $(this).val();
		db = {mobile:db}
		htmlobj=$.ajax({url:"/Admin/Rechange/Phone",async:false,data:db});
		$(".text").val(htmlobj.responseText);
	})


	$('.ajax-hotel').change(function(){
		var db = $(this).val();
		db = {hotel:db}
		htmlobj=$.ajax({url:"/Admin/Rechange/hotel_room",async:false,data:db});
		window.data = eval(htmlobj.responseText);
		var song = "<option value=''>请选择房间类型</option>";
		if(data){
			$.each(data, function(idx, obj) {
				song += "<option name='id' value='"+obj.id+"' >"+obj.roomName+"</option>";
			});
		}
			$('.result').html(song); 		

	})
	$('.ajax-room').change(function(){
		window.ID = $(this).val();
		$.each(data, function(idx, obj) {
			if(ID == obj.id){	
				var txt = '单价'+obj.price+'元/天';	
				$('.rechargeTime').attr('placeholder',txt);
			}
		})
	})

	$('.Deduction').change(function(){

		mobile = $('.ajax-phone').val();
		hotel = $('.ajax-hotel').val();
		room = $('.ajax-room').val();

		db = {mobile:mobile,hotel:hotel,room:room};

		htmlobj=$.ajax({url:"/Admin/Deduction/Select",async:false,data:db});

		db = eval(htmlobj.responseText);

		htm = "";
		if(db){
			$.each(db,function(idx, obj){
				time = obj.duration - obj.used;
				htm+="<div><input style='width:15px;height: 15px;margin-top:3px;' type='checkbox' name='order[]' value='"+obj.id+"'> 订单编号："+obj.no+" 订单所剩时长: "+time+"小时</div>";
			})
		}else{
			htm = "<div style='line-height: 33px;color:#F00;'>对不起，您的时间余额不足</div>"
		}
		$('.timeOut').html(htm); 
	})

	$('.rechargeTime').keyup(function(){
		day = $(this).val();

		$.each(data, function(idx, obj) {
	
			if(ID == obj.id){	
				price = obj.price*day;
			}
		})

		$('.monery').attr('value',price);
	})


})