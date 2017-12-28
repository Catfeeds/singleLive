$(function(){
	layui.use(['laytpl','flow','util'], function(){
		var util = layui.util;
		var laytpl = layui.laytpl;
		var flow   = layui.flow;//流加载
		if ( $('#view').length > 0 ) {
			/*流加载页面*/
			flow.load({
				elem: '#view' //指定列表容器
				,isAuto:true
				,end:$('#view').attr('end-msg')?$('#view').attr('end-msg'):'没有更多了'
				,done: function(page, next){ //到达临界点（默认滚动触发），触发下一页
					var lis = [];
					var getTpl = layout.innerHTML;
					//以jQuery的Ajax请求为例，请求下一页数据（注意：page是从2开始返回）
					$.get($('#view').attr('url')+'/p/'+page, function(res){
					//假设你的列表返回在data集合中
						layui.each(res.db, function(index, item){
							laytpl(getTpl).render(item, function(html){
								lis.push(html);
							});
						});
					//执行下一页渲染，第二参数为：满足“加载更多”的条件，即后面仍有分页
					//pages为Ajax返回的总页数，只有当前页小于总页数的情况下，才会继续出现加载更多
					next('<span id="page'+page+'">'+lis.join('')+'</span>', page < res.page);
					if ($('#page'+page).find('.timeCheck').length > 0) {
						$('.timeCheck').each(function(){
							timeCheck($(this).attr('tnow'),$(this));
						});
					}
					});
				}
			});
			/*流加载页面*/
		}
		/*固定块*/
		util.fixbar({
			bar1: '&#x1002;'
			,css: {right: 5, bottom: 100 }
			,bgcolor : 'rgba(0,0,0,0.3)'
			,click: function(type){
				console.log(type);
				if(type === 'bar1'){
					 window.location.reload();
				}
			}
		});
		/*固定块*/
		$('body').on('click','.ajax-get',function () {
			$(this).attr('disabled','true');
			var self = $(this);
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
			    	 	layer.open({
							content: msg
							,btn: ['确定', '取消']
							,skin: 'footer'
							,yes: function(index){
								$.get(target,success, "json");
							}
						});
			        }else{
			            $.get(target,success, "json");
			        }
		    }
		    return false;
		});
		$('.ajax-post').click(function () {

			$(this).attr('disabled','disabled');
			var self = $(this);
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
		        		layer.open({
							content: msg
							,btn: ['确定', '取消']
							,skin: 'footer'
							,yes: function(index){
								$.post(target,data,success, "json");
							}
						});
			        	self.attr('disabled',false);
			        }else{
			        	$.post(target,data,success, "json");
			        	self.attr('disabled',false);
			        }
		        }
		    }
		    return false;
		});
		function success(data) {
			if (data.status) {
				layer.open({
					content: data.info
					,skin: 'msg'
					,time: data.time //2秒后自动关闭
				});
				setTimeout(function () {
			        window.location.href = data.url
			    }, data.time*1000);
				 return false;
			} else {
				layer.open({
					content: data.info
					,skin: 'msg'
					,time: data.time //2秒后自动关闭
				});
				return false;
			}
		}












	});
});
	function timeCheck(sum,dom){
	    // var min = dom.attr('tmin'),tplus = dom.attr('tplus');
	    var interval, reg = /^\d$/,
        sleep = 1000;//间隔时间
        if (!interval) {
            interval = setInterval(function() {
                sum++;
                var h = parseInt(sum / 3600);
                var m = parseInt( (sum % 3600) / 60);
                var s = parseInt( (sum % 3600) % 60);
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





// $('.Prompt').click(function() {
//    var msg = $(this).attr('data-msg');

// 	layer.open({

//     title: [

//       '提示信息',

//       'background-color:#ffffff; color:#736767;text-align: left;background-image:url(img/xiaolian.png); background-repeat:no-repeat; background-position:1rem center; background-size:2.5rem;text-indent:8%;margin:0;height: 40px;line-height: 40px;'

//   ]

//   ,className: 'bcmt'

//   ,anim: 'up'

//   ,content: msg

//   ,btn: ['确认', '取消']

//   ,yes: function(index){

//         layer.close(index)

// 		Prompt_nei()

//   }
// });

// })