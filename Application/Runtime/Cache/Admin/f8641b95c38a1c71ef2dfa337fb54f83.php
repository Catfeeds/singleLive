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
            <li class="layui-this">
                <i class="fa fa-dashboard" aria-hidden="true"></i>
                <cite>客房列表</cite>
            </li>
        </ul>
        <div class="layui-tab-content" style="min-height: 150px; padding: 5px 0 0 0;">
            <div class="layui-tab-item layui-show">
                <div class="conter">
                    <div class="conter_sait">
                        <form action="/index.php/Admin/HouseList/index" method="post" class="form-hotel">
                            <div class="wort">
                                <div class="layui-input-inline">
                                    <input name="startTime" autocomplete="off" placeholder="选择开始时间" class="layui-input test-item" value="<?php echo I('startTime');?>" type="text">
                                </div>
                            </div>
                            <div class="wort">
                                <div class="layui-input-inline">至
                                </div>
                            </div>
                            <div class="wort">
                                <div class="layui-input-inline">
                                    <input name="endTime" autocomplete="off" placeholder="选择结束时间" class="layui-input test-item" value="<?php echo I('endTime');?>" type="text">
                                </div>
                            </div>
                            <div class="wort" style="width:280px;">
                                <input name="title" lay-verify="title" value="<?php echo I('title');?>" autocomplete="off" placeholder="客房名称" class="layui-input" type="text">
                            </div>
                            <div class="wort">
                                <div class="layui-input-inline">
                                    <select style="width: 200px;height: 38px;" name="category">
                                        <option value="">--请选择客房类型--</option>
                                        <?php if(is_array($cate)): $i = 0; $__LIST__ = $cate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cates): $mod = ($i % 2 );++$i;?><option value="<?php echo ($cates["id"]); ?>"><?php echo ($cates["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="wort">
                                <button type="submit" id="select" class="layui-btn">查找</button>
                            </div>
                            <div class="wort">
                                <a href="/index.php/Admin/HouseList/add">
                                    <button type="button" class="layui-btn layui-btn-danger">新增</button>
                                </a>
                            </div>
                        </form>
                    </div>
                    <div class="botg">
                        <table class="layui-table">
                            <thead>
                            <tr>
                                <th>客房分类</th>
                                <th>客房名称</th>
                                <th>客房金额</th>
                                <th>反还积分</th>
                                <th>是否可以使用电子卷</th>
                                <th>添加时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(is_array($db)): $i = 0; $__LIST__ = $db;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><tr>
                                    <td><?php echo ($row["cateName"]); ?></td>
                                    <td><?php echo ($row["name"]); ?></td>
                                    <td><?php echo ($row["money"]); ?></td>
                                    <td><?php echo ($row["sorce"]); ?></td>
                                    <td><?php echo $row['paper'] == 'y' ? '可用' : '不可用';?></td>
                                    <td><?php echo ($row["add_time"]); ?></td>
                                    <td>
                                        <a class="z_coios_2" href="/index.php/Admin/HouseList/banner/id/<?php echo ($row["id"]); ?>">轮播图</a>
                                        <?php switch($row["status"]): case "1": ?><a href="/index.php/Admin/HouseList/set_status?co=HouseList&id=<?php echo ($row["id"]); ?>&t=House&sta=1" class="z_coios_1 ajax-get confirm" data-msg='您确定禁用？'>禁用</a><?php break;?>
                                            <?php case "2": ?><a href="/index.php/Admin/HouseList/set_status?co=HouseList&id=<?php echo ($row["id"]); ?>&t=House&sta=2" class="z_coios_2 ajax-get confirm" data-msg='您确定启用？'>启用</a><?php break; endswitch;?>
                                        <a class="z_coios_4" href="/index.php/Admin/HouseList/edit/id/<?php echo ($row["id"]); ?>">修改</a>
                                        <a href="/index.php/Admin/HouseList/house_del/id/<?php echo ($row["id"]); ?>" class="z_coios_3 ajax-get confirm" data-msg='您确定删除？'>删除</a>
                                    </td>
                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
                        </table>
                        <div class="feniou">
                            <ul>
                                <?php echo ($page); ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    layui.use('laydate', function() {
        var laydate = layui.laydate;
        lay('.test-item').each(function(){
            laydate.render({
                elem: this
                ,trigger: 'click'
            });
        });
    })
</script>

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