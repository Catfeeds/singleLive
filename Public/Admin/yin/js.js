$(function(){
	/**
	 * [upload_options 文件上传方法]
	 * 在file的input中加入class='uploadOne'即可边长AJAX上传
	 * 在file的input中加入data-width='int' data-height='int'即可居中剪裁对应图片
	 * 更换展示图片 需要设定父级class='uploadOne_parent' 再在img标签中加入class='uploadOne_img'
	 * 更换隐藏域传值 需要设定父级class='uploadOne_parent' 再在隐藏域标签中加入class='uploadOne_data'
	 */
	var upload_options = {
		elem:'.uploadOne',
		url:'../../../../index.php/Admin/AjaxPost/uploadOne',
		before:uploadBefore,
		success:uploadsuccess
	};
	layui.use('upload',function(){
		layui.upload(upload_options);
	});
	function uploadBefore() {
		$('.uploadOne').each(function(){
			var w = $(this).attr('data-width'),
			h = $(this).attr('data-height');
			if ($(this).parents('form').find('input[name="width"]').length > 0) {
				$(this).parents('form').find('input[name="width"]').val(w);
			}else{
				$(this).parents('form').append('<input type="hidden" name="width" value="'+w+'">');
			}

			if ($(this).parents('form').find('input[name="height"]').length > 0) {
				$(this).parents('form').find('input[name="width"]').val(h);
			}else{
				$(this).parents('form').append('<input type="hidden" name="height" value="'+h+'">');
			}
		});
	}
	function uploadsuccess(data,input) {
		var p = $(input).parents('.uploadOne_parent');
		p.find('.uploadOne_img').attr('src','/Uploads'+data.savepath+data.savename);
		p.find('.uploadOne_data').val(data.filesid);
	}
});