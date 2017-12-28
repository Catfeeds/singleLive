$(function(){
	/*下拉默认值*/
	$('select').each(function(){
		$(this).val($(this).attr('value'));
	});
	/*下拉默认值*/
	$('.ajax-prompt').click(function(){
		$(this).attr('disabled','true');
		var self = $(this);
	    var target;
	    var msg = '确定执行该操作吗？';
	    msg = $(this).attr('data-msg')?($(this).attr('data-msg')):msg;
		if ((target = $(this).attr('href')) || (target = $(this).attr('url'))) {
		    layer.prompt({
		    	title:msg,
		    	formType:0,
		    },function(text,index){
		    	$.get(target+'/prompt/'+text,success, "json");
		    });
		}
	    return false;
	});

	// $('.ajax-get-time').click(function () {
	//     var target;
	//     var self = $(this);
	//     if($(this).hasClass('confirm')){//判断是否需要确认
	//     	 var nead_confirm = true;
	//     }else{
	//     	 var nead_confirm = false;
	//     }
	//     var msg = '确定执行该操作吗？';
	//     msg = $(this).attr('data-msg')?($(this).attr('data-msg')):msg;
	//     if ((target = $(this).attr('href')) || (target = $(this).attr('url'))) {
	//     	 if(nead_confirm){
	// 	        	parent.layer.confirm(msg, function(index){
	// 	        		var index = parent.layer.msg('请稍后', {
	// 						  icon: 16
	// 						  ,shade: 0.01
	// 						});
	// 	                $.get(target,function(){
	// 	                	if (data.status) {
	// 							layer.msg(data.info,{icon: 1,time:data.time*1000});
	// 							self.parent('tr').find('.showTime').text(
	// 								TimeCheck(0);
	// 								);
	// 							return false;
	// 						} else {
	// 							layer.msg(data.info,{icon: 2});
	// 							return false;
	// 						}
	// 	                }, "json");
	// 	        	});
	// 	        }else{
	// 	        	var index = parent.layer.msg('请稍后', {
	// 						  icon: 16
	// 						  ,shade: 0.01
	// 						});
	// 	            $.get(target,successTime, "json");
	// 	        }
	//     }
	//     return false;
	// });
























});
	/**
	 * [TimeCheck 计时方式]
	 * @Author   尹新斌
	 * @DateTime 2017-07-20
	 * @Function []
	 * @param    {[type]}   sum [description]
	 */
	function timeCheck(sum,dom,hours){
		var min = dom.attr('tmin'),tplus = dom.attr('tplus'),max = dom.attr('tmax');
		var interval, reg = /^\d$/,
        sleep = 1000;//间隔时间
        // var sum = 86400;
        if (!interval) {
            interval = setInterval(function() {
                sum++;
                var h = parseInt(sum / 3600);
                var m = parseInt( (sum % 3600) / 60);
                var s = parseInt( (sum % 3600) % 60);
                if (h < min) {
                	var used = min;
                }else{
                	if (m >= tplus) {
                		var used = h + 1;
                	}else{
                		var used = h;
                	}
                }

                if (parseInt(used) > parseInt(max)) {
                	used = '<span style="color:red;">已超时</span>' +used
                }
                hours.html(used);
                if (h < 10) {
                    h = '0'+h;
                }
                if (m < 10) {
                    m = '0'+m;
                }
                if (s < 10) {
                    s = '0'+s;
                }
                dom.text(h +':'+ m +':'+ s);
            }, sleep);
        } else {
            clearInterval(interval);
            interval = null;
        }
	}