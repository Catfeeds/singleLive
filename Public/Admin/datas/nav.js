$(function () {


    $('.bg_span').click(function () {

        if ($(this).hasClass('bgimg')) {

            $(this).removeClass('bgimg')

            $(this).parent().siblings().toggle(300)

        } else {

            $(this).addClass('bgimg')

            $(this).parent().siblings().toggle(300)

        }

    })


    /**权限组全选JQ*/
    $('.rules_row').click(function () {
        var flag = false;
        $(this).parents('.checkmod').find('.rules_row').each(function () {
            if ($(this).is(":checked")) {
                flag = true;
            }
        });
        if (flag) {
            $(this).parents('.checkmod').find('.rules_all').prop('checked', true);
        } else {
            $(this).parents('.checkmod').find('.rules_all').prop('checked', false);
        }
    });
    /**权限组全选JQ*/
    $('.rules_all').click(function () {
        if ($(this).is(":checked")) {
            $(this).parents('.checkmod').find('.rules_row').prop('checked', true);
        } else {
            $(this).parents('.checkmod').find('.rules_row').prop('checked', false);
        }
    });

//数据库选择多选
    $(".ids").click(function () {

        if ($(this).is(":checked")) {
            $(this).parents('.layui-table').find('.bot').prop('checked', true);
        } else {
            $(this).parents('.layui-table').find('.bot').prop('checked', false);
        }
    })
//数据库选择多选
    $('.bot').click(function () {
        var flag = false;
        $(this).parents('.layui-table').find('.bot').each(function () {
            if ($(this).is(':checked')) {
                flag = true;
            }
        });
        if (flag) {
            $(this).parents('.layui-table').find('.ids').prop('checked', true);
        } else {
            $(this).parents('.layui-table').find('.ids').prop('checked', false);
        }
    })


    $('.dutot').click(function () {

        if ($(this).hasClass('bgimg')) {


            $('.bodpe_botm').css('display', 'none')

            $(this).removeClass('bgimg')


            $('.dutot').css('background', 'url(/Public/Admin/images/fl_03.png) no-repeat left center')
        } else {


            $('.bodpe_botm').css('display', 'none')

            $('.dutot').css('background', 'url(/Public/Admin/images/fl_03.png) no-repeat left center')

            $(this).addClass('bgimg')

            $(this).css('background', 'url(/Public/Admin/images/sp_03.png) no-repeat left center')

            $(this).parents('.bodpe').find('.bodpe_botm').toggle(0)

        }

    })


    //选项卡

    $('.ant li').click(function () {

        $(this).addClass('atur').siblings().removeClass('atur');

        $('.mobr>.mobt:eq(' + $(this).index() + ')').show().siblings().hide();
    })


// //全选反选	

// 	 $("#checkAll").click(function() {
//         $('.bb').attr("checked",this.checked); 
//       });
//       var $subBox = $(".bb");
//       $subBox.click(function(){
//         $("#checkAll").attr("checked",$subBox.length == $(".bb:checked").length ? true : false);
//       });


    $('.ano').click(function () {

        $('.ioy').attr('checked', false)

    })
    $('.ioy').click(function () {

        $('.ano').attr('checked', false)

    })


//////////////通用弹窗

    $('.abc').click(function () {


        var msg = $(this).attr('data-msg');

        $('.sancu').find('.sancu_cont').html(msg);

        layui.use('layer', function () {

            layer.confirm(msg,

                {
                    skin: 'sancu',

                    closeBtn: 0,

                    area: ['20%', 'auto'], //宽高

                    title: '提示信息'

                }, function (index) {

                    layer.close(index);

                    layer.msg('提交成功');


                });


        });


    });


    //公用iframe

    $('.iframe').click(function () {
        var iframe = $(this).attr('data-iframe');
        var url = $(this).attr('url');
        layer.open({
            type: 2,
            title: false,
            shadeClose: true,
            shade: 0.8,
            area: ['50%', '50%'],
            content: iframe,
        });

    });

    $('.iframe_abc').click(function () {

        var msg = $(this).attr('data-msg');

        layui.use('layer', function () {

            layer.confirm(msg,
                {
                    skin: 'sancu',

                    closeBtn: 0,

                    area: ['20%', 'auto'], //宽高

                    title: '提示信息'

                }, function (index) {

                    layer.close(index);

                    layer.msg('提交成功');

                    parent.layer.closeAll();

                });

        });
    });

    $('.cancel').click(function () {

        parent.layer.closeAll();

    });
    //公用iframe结束


    //分类新增
    $(".tjfl").click(function () {


        $(".mttopr").last().after($(".mttopr").last().clone(true));

    });

    $('.scfl').click(function () {

        if ($(".mttopr").length < 2) {

            return

        }

        $(".mttopr").parent().children(".mttopr:last").remove();


    })

//表格复选框

    $('.cnke').click(function () {


        $('.fopt').attr('checked', this.checked)


    })


    $('.fopt').click(function () {


        $('.cnke').attr('checked', $('.fopt').length == $('.fopt:checked').length ? true : false)


    })

//多图上传 初始化插件
    // $(".zyupload").zyUpload({
    // 	width            :   "650px",                 // 宽度
    // 	height           :   "auto",                 // 宽度
    // 	itemWidth        :   "140px",                 // 文件项的宽度
    // 	itemHeight       :   "115px",                 // 文件项的高度
    // 	url              :   "/upload/UploadAction",  // 上传文件的路径
    // 	fileType         :   ["jpg","png","txt","js","exe"],// 上传文件的类型
    // 	fileSize         :   51200000,                // 上传文件的大小
    // 	multiple         :   true,                    // 是否可以多个文件上传
    // 	dragDrop         :   true,                    // 是否可以拖动上传文件
    // 	tailor           :   true,                    // 是否可以裁剪图片
    // 	del              :   true,                    // 是否可以删除文件
    // 	finishDel        :   false,  				  // 是否在上传文件完成后删除预览
    // 	/* 外部获得的回调接口 */
    // 	onSelect: function(selectFiles, allFiles){    // 选择文件的回调方法  selectFile:当前选中的文件  allFiles:还没上传的全部文件
    // 		console.info("当前选择了以下文件：");
    // 		console.info(selectFiles);
    // 	},
    // 	onDelete: function(file, files){              // 删除一个文件的回调方法 file:当前删除的文件  files:删除之后的文件
    // 		console.info("当前删除了此文件：");
    // 		console.info(file.name);
    // 	},
    // 	onSuccess: function(file, response){          // 文件上传成功的回调方法
    // 		console.info("此文件上传成功：");
    // 		console.info(file.name);
    // 		console.info("此文件上传到服务器地址：");
    // 		console.info(response);
    // 		$("#uploadInf").append("<p>上传成功，文件地址是：" + response + "</p>");
    // 	},
    // 	onFailure: function(file, response){          // 文件上传失败的回调方法
    // 		console.info("此文件上传失败：");
    // 		console.info(file.name);
    // 	},
    // 	onComplete: function(response){           	  // 上传完成的回调方法
    // 		console.info("文件上传完成");
    // 		console.info(response);
    // 	}
    // });


})

//图片上传


//时间插件
/*layui.use('laydate', function () {
    var laydate = layui.laydate;

    var start = {
        min: laydate.now()
        , max: '2099-06-16 23:59:59'
        , istoday: false
        , choose: function (datas) {
            end.min = datas; //开始日选好后，重置结束日的最小日期
            end.start = datas //将结束日的初始值设定为开始日
        }
    };

    var end = {
        min: laydate.now()
        , max: '2099-06-16 23:59:59'
        , istoday: false
        , choose: function (datas) {
            start.max = datas; //结束日选好后，重置开始日的最大日期
        }
    };

    document.getElementById('LAY_demorange_s').onclick = function () {
        start.elem = this;
        laydate(start);
    }
    document.getElementById('LAY_demorange_e').onclick = function () {
        end.elem = this
        laydate(end);
    }

});*/



