$(function(){
	$('body').on('click','.parent-ajax-get',function() {
		var target;
	    if($(this).hasClass('confirm')){//判断是否需要确认
	    	 var nead_confirm = true;
	    }else{
	    	 var nead_confirm = false;
	    }
	    if ((target = $(this).attr('href')) || (target = $(this).attr('url'))) {
	    	 if(nead_confirm){
		        	parent.layer.confirm('确定执行该操作？', function(index){
		        		var index = parent.layer.msg('请稍后', {
							  icon: 16
							  ,shade: 0.01
							});
		                $.get(target,parentsuccess, "json");
		        	});
		        }else{
		        	var index = parent.layer.msg('请稍后', {
							  icon: 16
							  ,shade: 0.01
							});
		            $.get(target,parentsuccess, "json");
		        }
	    }
	    return false;
	});
	//ajax get请求
	$('.ajax-get').click(function () {
	    var target;
	    if($(this).hasClass('confirm')){//判断是否需要确认
	    	 var nead_confirm = true;
	    }else{
	    	 var nead_confirm = false;
	    }
	    var msg = '确定执行该操作吗？';
	    msg = $(this).attr('data-msg')?($(this).attr('data-msg')):msg;
	    if ((target = $(this).attr('href')) || (target = $(this).attr('url'))) {
	    	 if(nead_confirm){
		        	parent.layer.confirm(msg, function(index){
		        		var index = parent.layer.msg('请稍后', {
							  icon: 16
							  ,shade: 0.01
							});
		                $.get(target,success, "json");
		        	});
		        }else{
		        	var index = parent.layer.msg('请稍后', {
							  icon: 16
							  ,shade: 0.01
							});
		            $.get(target,success, "json");
		        }
	    }
	    return false;
	});
	/*用于iframe框架父级弹窗的提交事件*/
	$('body').on('click','.parent-ajax-post',function() {
		var data;//提交数据
	    var target_form = $(this).attr('target-form');
	    var file_url = $(this).attr('file-url');
        if (file_url) {
            doUpload(file_url,target_form);//上传组件执行操作
        }
	    var msg = '确定执行该操作吗？';
	    msg = $(this).attr('data-msg')?($(this).attr('data-msg')+',确定执行该操作吗？'):msg;
	    if($(this).hasClass('confirm')){//判断是否需要确认
	    	 var nead_confirm = true;
	    }else{
	    	 var nead_confirm = false;
	    }
	    if($(this).hasClass('validate')){//判断是否需要确认
	    	 var nead_validate = true;
	    }else{
	    	 var nead_validate = false;
	    }

	    var flag = true;

	    var target = ($(this).attr('href'))||($(this).attr('url'));

	    if (($(this).attr('type') == 'submit') || target) {
	    	var form = $(this).parents('.' + target_form);

	        if (form.get(0) == undefined) {
	            return false;
	        } else if (form.get(0).nodeName == 'FORM') {
	            if ($(this).attr('url') !== undefined) {
	                target = $(this).attr('url');
	            } else {
	                target = form.get(0).action;
	            }

	            /*判断必填表单是否为空*/
	            form.find('.requ').each(function(){
	            	p_ipt = $(this).parent('label').next('.formRight');
	            	this_ipt = (p_ipt.find('input[type="text"]'))||(p_ipt.find('input[type="password"]'));
	            	if(this_ipt.val() == false){
	            		this_ipt.focus();
	            		this_ipt.removeClass('form-success');
	            		this_ipt.addClass('form-error');
	            		form.find('input[target-form]').hide();
	            		flag = false;
	            		return false;
	            	}
	            });
	            if(flag==false){return false;}
	            data = form.serialize();

	        } else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {
	        	data = form.serialize();
	        } else {
	        	data = form.find('input,select,textarea').serialize();
	        }
	        if (nead_validate) {
	        	var validate = form.validate().form();
	        }

	        if (validate !== false) {
	        	if(nead_confirm){
		        	parent.layer.confirm(msg, function(index){
	        			var index = parent.layer.msg('请稍后', {
						  icon: 16
						  ,shade: 0.01
						});
		     	        $.post(target,data,parentsuccess, "json");
		        	});
		        }else{
        			var index = parent.layer.msg('请稍后', {
					  icon: 16
					  ,shade: 0.01
					});
		        	$.post(target,data,parentsuccess, "json");
		        }
	        }
	    }
	    return false;
	});
	/*post提交数据*/
	$('.ajax-post').click(function () {
	    var data;//提交数据
	    var target_form = $(this).attr('target-form');
	    var file_url = $(this).attr('file-url');
        if (file_url) {
            doUpload(file_url,target_form);//上传组件执行操作
        }
	    var msg = '确定执行该操作吗？';
	    msg = $(this).attr('data-msg')?($(this).attr('data-msg')):msg;
	    if($(this).hasClass('confirm')){//判断是否需要确认
	    	 var nead_confirm = true;
	    }else{
	    	 var nead_confirm = false;
	    }
	    if($(this).hasClass('validate')){//判断是否需要执行验证
	    	 var nead_validate = true;
	    }else{
	    	 var nead_validate = false;
	    }

	    var flag = true;

	    var target = ($(this).attr('href'))||($(this).attr('url'));

	    if (($(this).attr('type') == 'submit') || target) {
	    	var form = $(this).parents('.' + target_form);

	        if (form.get(0) == undefined) {
	            return false;
	        } else if (form.get(0).nodeName == 'FORM') {
	            if ($(this).attr('url') !== undefined) {
	                target = $(this).attr('url');
	            } else {
	                target = form.get(0).action;
	            }

	            /*判断必填表单是否为空*/
	            form.find('.requ').each(function(){
	            	p_ipt = $(this).parent('label').next('.formRight');
	            	this_ipt = (p_ipt.find('input[type="text"]'))||(p_ipt.find('input[type="password"]'));
	            	if(this_ipt.val() == false){           		
	            		this_ipt.focus();
	            		this_ipt.removeClass('form-success');
	            		this_ipt.addClass('form-error');
	            		form.find('input[target-form]').hide();
	            		flag = false;
	            		return false;

	            	}
	            });
	            if(flag==false){return false;}
	            data = form.serialize();

	        } else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {
	        	data = form.serialize();
	        } else {

	        	data = form.find('input,select,textarea').serialize();
	        }
	        if (nead_validate) {
	        	var validate = form.validate().form();
	        }
	        if (validate !== false) {
	        	if(nead_confirm){
		        	parent.layer.confirm(msg, function(index){
	        			var index = parent.layer.msg('请稍后', {
						  icon: 16
						  ,shade: 0.01
						});
		     	        $.post(target,data,success, "json");
		        	});
		        }else{
        			var index = parent.layer.msg('请稍后', {
					  icon: 16
					  ,shade: 0.01
					});
		        	$.post(target,data,success, "json");
		        }
	        }
	    }
	    return false;
	});
})

function success(data) {
	if (data.status) {
		parent.layer.msg(data.info,{icon: 1,time:data.time*1000});
		setTimeout(function () {
			var index = parent.layer.msg('加载中', {
							  icon: 16
							  ,shade: 0.01
							});
	        window.location.href = data.url
	    }, data.time*1000);
		 return false;
	} else {
		parent.layer.msg(data.info,{icon: 2});
		 return false;
	}
}

function parentsuccess(data) {
	if (data.status) {
		parent.layer.msg(data.info,{icon: 1,time:data.time*1000});
		setTimeout(function () {
			var index = parent.layer.msg('加载中', {
							  icon: 16
							  ,shade: 0.01
							});
	        $("#iframe").attr("src",data.url);
	        layer.closeAll('page');
	        parent.location.href = data.url
	    }, data.time*1000);
		return false;
	} else {
		parent.layer.msg(data.info,{icon: 2});
		return false;
	}
}

