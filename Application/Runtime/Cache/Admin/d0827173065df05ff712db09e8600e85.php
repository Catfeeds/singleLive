<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>单用户订房系统</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="/Public/layui/css/layui.css" /><!--最新版-->
    <link rel="stylesheet" href="/Public/Admin/css/global.css" media="all">
    <link rel="stylesheet" href="/Public/Admin/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/Public/Admin/css/css.css" media="all">
    <link rel="stylesheet" href="/Public/zyupload/skins/zyupload-1.0.0.min.css " type="text/css">
</head>
<body>
<script src="/Public/Admin/js/jquery-1.10.1.min.js"></script> 
<script type="text/javascript" src="/Public/Admin/layer/layer.js"></script>
<script type="text/javascript" src="/Public/layui/layui.js"></script><!--最新版-->
<!--<script type="text/javascript" src="/Public/Admin/layui/layui.js"></script>-->
<script type="text/javascript" src="/Public/Admin/common/common.js"></script>
<script type="text/javascript" src="/Public/Admin/datas/nav.js"></script> 
<script type="text/javascript" src="/Public/Admin/js/data.js"></script>
<script type="text/javascript" src="/Public/Admin/js/index.js"></script>
<script type="text/javascript" src="/Public/zyupload/zyupload-1.0.0.min.js"></script>
<div class="layui-layout layui-layout-admin">
    <div class="layui-header header header-demo">
        <div class="layui-main">
            <div class="admin-login-box">
                <a class="logo" style="left: 0;" href="index.html">
                    <span style="font-size: 20px;"><?php echo ($_SESSION['config']['webName']); ?></span>
                </a>
                <!-- <div class="admin-side-toggle">
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </div> -->
            </div>
            <ul class="layui-nav admin-header-item">

                <li class="layui-nav-item">
                    <a href="javascript:;" class="admin-header-user">
                        <img src="<?php echo ($_SESSION['config']['headUrl']); ?>"/>
                        <span><?php echo ($_SESSION['root_user']['name']); ?></span>
                    </a>
                    <dl class="layui-nav-child">
                        <?php if(isset($_SESSION['root_Pwd'])): ?><dd>
                                <a href="/Admin/Pwd/index"><i class="fa fa-gear" aria-hidden="true"></i> 修改密码</a>
                            </dd><?php endif; ?>
                        <dd>
                            <a href="/Admin/Index/login"><i class="fa fa-sign-out" aria-hidden="true"></i> 注销</a>
                        </dd>
                    </dl>
                </li>
            </ul>
            <ul class="layui-nav admin-header-item-mobile">
                <li class="layui-nav-item">
                    <a href="/Admin/Index/login"><i class="fa fa-sign-out" aria-hidden="true"></i> 注销</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="layui-side layui-bg-black" id="admin-side">
        <div class="layui-side-scroll" id="admin-navbar-side" lay-filter="side">
            <ul class="layui-nav layui-nav-tree" lay-filter="demo">
                <?php if(is_array($_SESSION['root_permRows'])): $i = 0; $__LIST__ = $_SESSION['root_permRows'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$permRow): $mod = ($i % 2 );++$i;?><li class="layui-nav-item <?php if($_SESSION['ParentContor']==" 首页
                    "&&$permRow["perm_id"]==1){ echo "layui-this" ;}else if($_SESSION['ParentContor']==$permRow['perm_type']){ echo "layui-nav-itemed";}?> ">
                    <?php if($permRow["perm_id"] == 1): ?><a href="/Admin/Index/index">首页</a>
                        <?php else: ?>
                        <a href="javascript:;"><?php echo ($permRow["perm_type"]); ?></a>
                        <dl class="layui-nav-child">
                            <?php if(is_array($permRow["subClass"])): $i = 0; $__LIST__ = $permRow["subClass"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$subClass): $mod = ($i % 2 );++$i;?><dd
                                <?php if($_SESSION["Controller"]==$subClass["perm_url"]){echo "class='layui-this'"; }?>
                                >
                                <a href="/Admin/<?php echo ($subClass["perm_url"]); ?>/index"><?php echo ($subClass["perm_type"]); ?></a></dd><?php endforeach; endif; else: echo "" ;endif; ?>
                        </dl><?php endif; ?>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>

    <div class="layui-body" style="bottom: 0;border-left: solid 2px #1AA094;" id="admin-body">
    <div class="layui-tab admin-nav-card layui-tab-brief" lay-filter="admin-tab">
        <ul class="layui-tab-title">
            <li class="layui-this"><i class="fa fa-dashboard" aria-hidden="true"></i> <cite>新增客房</cite></li>
        </ul>
        <div class="layui-tab-item layui-show">
            <div class="conter">
                <div class="xxkt">
                    <ul>
                        <li class="xxkt_bt">新增客房</li>
                    </ul>
                </div>
                <form action="/index.php/Admin/HouseList/add" method="post" class="form-hotelAdd">
                    <div class="xxknr">
                        <div>
                            <div class="glylb_szmm"><span class="glylb_szmm_span">选择客房类型：</span>
                                <div class="layui-input-block z_min_left_10">
                                    <select class="fn-tinput requ" name="category">
                                        <option value="">--请选择客房类型--</option>
                                        <?php if(is_array($cate)): $i = 0; $__LIST__ = $cate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cates): $mod = ($i % 2 );++$i;?><option value="<?php echo ($cates["id"]); ?>"><?php echo ($cates["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="glylb_szmm"><span class="glylb_szmm_span">房间总数：</span>
                                <div class="layui-input-block z_min_left_10">
                                    <input class="fn-tinput requ" type="text" name="total_num" placeholder="请输入房间总数" >
                                </div>
                            </div>
                            <div class="glylb_szmm"><span class="glylb_szmm_span">客房名称：</span>
                                <div class="layui-input-block z_min_left_10">
                                    <input class="fn-tinput requ" name="name" placeholder="请填写客房名称">
                                </div>
                            </div>
                            <div class="glylb_szmm"><span class="glylb_szmm_span">房间金额：</span>
                                <div class="layui-input-block z_min_left_10">
                                    <input class="fn-tinput requ" type="text" name="money" placeholder="请输入房间金额">
                                </div>
                            </div>
                            <div class="glylb_szmm"><span class="glylb_szmm_span">反还积分：</span>
                                <div class="layui-input-block z_min_left_10">
                                    <input class="fn-tinput requ" type="text" name="sorce" placeholder="请输入反还积分">
                                </div>
                            </div>
                            <div class="glylb_szmm"><span class="glylb_szmm_span">是否可以使用电子卷：</span>
                                <div class="layui-input-block z_min_left_10">
                                    <select class="fn-tinput requ" name="paper">
                                        <option value="y">可用</option>
                                        <option value="n">不可用</option>
                                    </select>
                                </div>
                            </div>
                            <div class="glylb_szmm"><span class="glylb_szmm_span">房间简介：</span>
                                <div class="layui-input-block z_min_left_10">
                                    <textarea style="width:359px;height: 200px;min-height:200px;" name="equipment"></textarea>
                                </div>
                            </div>
                            <div class="glylb_szmm"><span class="glylb_szmm_span">房间设备：</span>
                                <div class="layui-input-block z_min_left_10">
                                    <textarea class="layui-textarea" name="equipment"></textarea>
                                </div>
                            </div>
                            <div class="glylb_szmm"><span class="glylb_szmm_span">房间描述：</span>
                                <div class="layui-input-block z_min_left_10">
                                    <textarea class="layui-textarea" name="mark"></textarea>
                                </div>
                            </div>
                            <div class="glylb_szmm"><span class="glylb_szmm_span">订房须知：</span>
                                <div class="layui-input-block z_min_left_10">
                                    <textarea class="layui-textarea" name="back"></textarea>
                                </div>
                            </div>
                            <div class="glylb_szmm"><span class="glylb_szmm_span">入住通知：</span>
                                <div class="layui-input-block z_min_left_10">
                                    <textarea class="layui-textarea" name="come"></textarea>
                                </div>
                            </div>
                            <div class="glylb_szmm"><span class="glylb_szmm_span">更改订单：</span>
                                <div class="layui-input-block z_min_left_10">
                                    <textarea class="layui-textarea" name="change"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="gater">
                        <input type="hidden" name="__GO__" value="/index.php/Admin/HouseList/index">
                        <input type="submit" value="确认" class="glylb_an_qd ajax-post" target-form="form-hotelAdd"/>
                        <a href="/index.php/Admin/HouseList/index"><button class="glylb_an_fh" type="button">返回</button></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


    <div class="layui-footer footer footer-demo" id="admin-footer">
        <div class="layui-main">
            <p>2016 &copy;
                <a target="_blank"><?php echo ($_SESSION['config']['webName']); ?></a> LGPL license
            </p>
        </div>
    </div>
    <div class="site-tree-mobile layui-hide">
        <i class="layui-icon">&#xe602;</i>
    </div>
    <div class="site-mobile-shade"></div>
    <script>
        /*layui.use('layer', function () {
            var $ = layui.jquery,
                    layer = layui.layer;

            $('#video1').on('click', function () {
                layer.open({
                    title: 'YouTube',
                    maxmin: true,
                    type: 2,
                    content: 'video.html',
                    area: ['800px', '500px']
                });
            });
        });*/
        /*编辑器*/
        var k = 1;
        layui.use('layedit', function(){
            var layedit = layui.layedit;
            LAY_demo = {
                option : {
                    height: 300,
                    uploadImage : {
                        url : '/index.php/Admin/AjaxPost/uploadEdit',
                        type : 'post',
                    },
                },
            };
            var name = new Array();
            $('.layui-textarea').each(function (){
                $(this).attr('id','LAY_demo' + k)
                name.push(layedit.build('LAY_demo'+ k,LAY_demo.option));
                k++
            })
            $('.ajax-post').mouseover(function(){
                for (var i = 0; i < name.length; i++) {
                    layedit.sync(name[i])
                }
            })
        });
        /*文件上传*/
        layui.use('upload', function(){
            var upload = layui.upload;
            upload.render({
                elem: '.uploadsOne',
                done: function(res, index, upload){
                    $('.uploadOne_img').attr('src',res.data.src);
                    $('.uploadOne_img').css('display','block');
                    $('.uploadOne_data').val(res.id);
                }
            })
        });

        $('.admin-side-toggle').on('click', function () {
            var sideWidth = $('#admin-side').width();
            if (sideWidth === 200) {
                $('#admin-body').animate({
                    left: '0'
                }); //admin-footer
                $('#admin-footer').animate({
                    left: '0'
                });
                $('#admin-side').animate({
                    width: '0'
                });
            } else {
                $('#admin-body').animate({
                    left: '200px'
                });
                $('#admin-footer').animate({
                    left: '200px'
                });
                $('#admin-side').animate({
                    width: '200px'
                });
            }
        });
    </script>
</div>
</body>
</html>