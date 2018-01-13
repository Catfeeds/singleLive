// JavaScript Document
$(function () {
    $('.forder li a').click(function () {
        $(this).addClass('acver').parents('li').siblings().find('a').removeClass('acver')
    })
    $('.app_Guest li').click(function () {
        $(this).addClass('avcet').siblings().removeClass('avcet')
    })
//商品加减

    $('.appjia').click(function () {
        var pp = $('.appcont');
        var getNum = parseInt(pp.html());
        if (getNum < 1000000) {
            pp.html(getNum + 1)
        } else {
            layer.open({
                className: 'sese',
                content: '不能大于99'
                , skin: 'msg'
                , time: 2 //2秒后自动关闭
            });
        }
    })
    $('.appjian').click(function () {
        var pp = $('.appcont');
        var getNum = parseInt(pp.html());
        if (getNum > 0) {
            pp.html(getNum - 1)
        } else {
            layer.open({
                className: 'sese',
                content: '不能小于0'
                , skin: 'msg'
                , time: 2 //2秒后自动关闭
            });
        }
    })
//通用提示框
    /*$(".spoert").click(function () {
     var msg = $(this).attr("msg-tite")
     layer.open({
     className: 'smtn',
     content: msg
     , btn: ['确认', '取消']
     , skin: 'footer'
     , yes: function (index) {
     layer.open({content: '提交成功'})
     }
     });
     })*/

    /*若有disabled属性 则不可点击*/
    $(document).on('click', '.fr_l1', function () {
        if ($(this).find('input').attr('disabled') !== undefined) {
            $(this).unbind('click');
        } else {
            if ($(this).hasClass('fr_img')) {
                $(this).find('input').prop('checked', false);
                $(this).removeClass('fr_img');
            } else {
                $('.fr_l1').removeClass('fr_img').find('input').prop('checked', false);
                $(this).find('input').prop('checked', true);
                $(this).addClass('fr_img')
            }
        }
    });


    $('.fr_radio').click(function () {
        $('.fr_radio').removeClass('radiodsi')
        if ($(this).hasClass('fr_img')) {
            $(this).find('input').prop('checked', false)
            $(this).removeClass('radiodsi')
        } else {
            $(this).find('input').prop('checked', true)
            $(this).addClass('radiodsi')
        }
    })

    $('.biem_cont').click(function () {
        $(this).addClass('biatacve').siblings().removeClass('biatacve')
    });

    $(document).on('.str3', 'click', function () {
        layer.open({
            className: 'attui',
            style: 'border:none; background-color:#78BA32; color:#fff;',
            content: '剩余房间 ：20'
        })
    });


//分类选择


    $('.xopert li').click(function () {

        $(this).addClass('mill').siblings().removeClass('mill');

        $('.tipon>.coper:eq(' + $(this).index() + ')').show().siblings().hide();

    })
    
//价格排序
    $('.sutor').click(function () {
        layer.open({

            content: '<div class="binpoy1"><div class="binpoy_right"><input type="text" name="textfield" id="textfield" placeholder="请输入二级密码"/></div></div>'

            , btn: ['取消', '确认']

            , skin: 'footer'

            , yes: function (index) {

                layer.open({content: '取消成功'})
            }
            , no: function (index) {

                layer.open({content: '提交成功'})
            }
        });
    });
})

















