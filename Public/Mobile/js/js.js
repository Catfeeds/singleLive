(function(global){

		function remChange(){

			var aa = document.documentElement.clientWidth || document.body.clientWidth

			document.documentElement.style.fontSize=20*document.documentElement.clientWidth/1024+'px';

		}

		remChange();

		global.addEventListener('resize',remChange,false);

	})(window);

$(function(){

//底部js
	$('footer li a').click(function(){

		$('footer li a').removeClass('foter_tp')

		$(this).addClass('foter_tp')

	})

// 加减

$('.jia').click(function() {
	var pp = $(this).parents('.joist').find('.num');
	var getNum = parseInt(pp.html());
	if(getNum < 100) {
		pp.html(getNum + 1)
		compute();
	} else {
      layer.open({
          content: '不可以大于100'
          ,skin: 'msg'
          ,time: 2 //2秒后自动关闭
        });
		// alert("不可以大于100");
	}
})

$('.jian').click(function() {

	var pp = $(this).parents('.joist').find('.num');
	var getNum = parseInt(pp.html());
	if(getNum > 0) {
		pp.html(getNum - 1)
		compute();
	} else {
      layer.open({
          content: '不可以小于0'
          ,skin: 'msg'
          ,time: 2 //2秒后自动关闭
        });		
		// alert("不可以小于0");
	}
})



function compute(){
  var amount = 0;
  $('.roomtype').each(function(){
    var a = $(this).find('.roomnum').text();
    var b = $(this).find('.daysnum').text();
    var c = $(this).find('.amount').text();
    amount = amount + a*b*c;
  });
  $('#all_amount').text(amount);
}



//公用提示弹窗


function Prompt_nei() {

	layer.open({

    content: '提交成功'

    ,skin: 'msg'

    ,time: 2 //2秒后自动关闭

  });

}


$('.Prompt').click(function() {
   var msg = $(this).attr('data-msg');

	layer.open({

    title: [

      '提示信息',

      'background-color:#ffffff; color:#736767;text-align: left;background-image:url(img/xiaolian.png); background-repeat:no-repeat; background-position:1rem center; background-size:2.5rem;text-indent:8%;margin:0;height: 40px;line-height: 40px;'

  ]

  ,className: 'bcmt'

  ,anim: 'up'

  ,content: msg

  ,btn: ['确认', '取消']

  ,yes: function(index){

        layer.close(index)

		Prompt_nei()

  }
});

})




//收藏——查看

var Zwid = $('.am_tou ul')

var Fwid =$('.am_tou li').width()

var FwidLen =$('.am_tou li').length

Zwid.width(parseInt(Fwid*FwidLen))


var Zim = $('.imt_img ul')

var Fim =$('.imt_img li').width()

var FimdLen =$('.imt_img li').length

Zim.width(parseInt(Fim*FimdLen+20+"px"))

	$('.am_tou li').click(function() {

		$(this).addClass('goot').siblings().removeClass('goot');

		$('.am_zui>.am_htc:eq(' + $(this).index() + ')').show().siblings().hide();

	})

//收藏——查看














	})