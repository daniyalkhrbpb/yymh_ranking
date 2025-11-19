function preventScroll(){
    
}
//预加载图片
"undefined" != typeof jQuery && !
function(a) {
	"use strict";
	a.imgpreload = function(b, c) {
		c = a.extend({}, a.fn.imgpreload.defaults, c instanceof Function ? {
			all: c
		} : c), "string" == typeof b && (b = [b]);
		var d = [];
		a.each(b, function(e, f) {
			var g = new Image,
				h = f,
				i = g;
			"string" != typeof f && (h = a(f).attr("src") || a(f).css("background-image").replace(/^url\((?:"|')?(.*)(?:'|")?\)$/gm, "$1"), i = f), a(g).bind("load error", function(e) {
				d.push(i), a.data(i, "loaded", "error" == e.type ? !1 : !0), c.each instanceof Function && c.each.call(i, d.slice(0)), d.length >= b.length && c.all instanceof Function && c.all.call(d), a(this).unbind("load error")
			}), g.src = h
		})
	}, a.fn.imgpreload = function(b) {
		return a.imgpreload(this, b), this
	}, a.fn.imgpreload.defaults = {
		each: null,
		all: null
	}
}(jQuery);
var imgNum = 0;
var images = [];
function preLoadImg() {
	var imgs = document.images;
	for (var i = 0; i < imgs.length; i++) {
		images.push(imgs[i].src);
	}
	var cssImages = getallBgimages();
	for (var j = 0; j < cssImages.length; j++) {
		images.push(cssImages[j]);
	}
}
//工具栏
toggleMenuBar = function(o) {
    try {
        void 0 === o && (o = null);
        var e = $(".topMenu").hasClass("off");
        if (e && "off" == o) return !1;
        o || (o = e ? "on" : "off"), $(".topMenu").removeClass("on" == o ? "off" : "on").addClass(o), $(".bottomMenu,.aftips").removeClass("on" == o ? "off" : "on").addClass(o)
    } catch (o) {
        console.log(o)
    }
},
//章节列表
toggleEpList = function (o) {
        try {
            var t = $(".epListFrame");
            if (t.hasClass("off") && "off" == o) return !1;
            "on" == o ? t.find(".bg").fadeIn(100, (function () {
                t.removeClass("off").addClass("on"), preventScroll();
                var o = t.find(".data").scrollTop() + t.find("a.on").offset() - $(document).scrollTop() - 40;
                setTimeout((function () {
                    t.find(".data").animate({scrollTop: o}, 400)
                }), 150)
            })) : (t.find(".bg").hide(), t.removeClass("on").addClass("off"), resumeScroll())
        } catch (o) {
            console.log(o)
        }
},
//自动滚屏
startAutoScroll = function (o) {
    try {
        w = !0, function (o) {
            try {
                var n = $("body").height() - $(window).height(), l = Math.pow(o, 2) + 2,
                    c =  l;
                t = window.requestAnimationFrame((function o() {
                    $(document).scrollTop() < n && (window.scrollBy(0, c), t = window.requestAnimationFrame(o));
                }))
            } catch (o) {
					
                console.log(o)
            }
        }(o), autoScrollControl(), toggleMenuBar("off")
    } catch (o) {
        console.log(o)
    }
},
//停止滚屏
stopAutoScroll = function (o) {			
    try {
        w = !1, cancelAnimationFrame(t)
    } catch (o) {
        console.log(o)
    }
},autoScrollControl = function () {
    try {
        $(".epContent").bind("click touchend mousewheel", (function (o) {
            w && (o.preventDefault(), o.stopPropagation(), stopAutoScroll())
        }))
    } catch (o) {
        console.log(o)
    }
};
$(function() {
	  //展开小圆点
	 $('.aside-menu,.aside-nav a').click(function(e) {
        if (!$(".aside-nav a").hasClass('on')) {
            $(".aside-nav a").addClass("on");
			$(".aside-menu").addClass("close");
			$(".aside-nav a").removeClass("off");
			$(".circle-box").hide();
        } else {
			$(".aside-nav a").removeClass("on");
			$(".aside-menu").removeClass("close");
			$(".aside-nav a").addClass("off");
			$(".circle-box").show();
        }
     });
     drags();
    //点击中间弹出 关闭顶部和底部菜单
    $(".epContent").on("click", function(){
        toggleMenuBar();
        toggleEpList('off');
		$(".aside-menu").removeClass("on");
    });
    //弹出章节列表
    $(document).on("click", ".btn-text,#smenu", function(){
        toggleMenuBar('off');
		toggleEpList('on');
		$(".epListFrame").addClass("on");
		$(".epListFrame").removeClass("off");
		$(".epListFrame .bg").show();
		return false;
    });
	//加载章节列表
	$(function() {
		$(".btn-text,#smenu").one('click',function(e){
			sidelink();
		});
   });	
    //关闭章节列表
    $(".epListFrame .bg,.epListFrame a").on("click", function(){
        if (typeof toggleEpList == "function"){
            toggleEpList('off');
        }
    });
    //自动滚屏
    $(document).on("click", "#autoscroll", function(){
        if (typeof startAutoScroll == "function"){
            var scrollSpeed = $(this).data('speed');
            startAutoScroll(scrollSpeed);
			$(".aside-menu").addClass("on");
        }
    });
	//停止滚屏
    $(document).on("click", ".aside-menu", function(){
        if (typeof stopAutoScroll == "function"){
            var scrollSpeed = $(this).data('speed');
            stopAutoScroll(scrollSpeed);
			$(".aside-menu").removeClass("on");
        }
    });	
	//返回顶部 OR 停止滚屏
	$("#gotop").click(function(){
        $('html,body').animate({scrollTop:0},300);
         if (typeof stopAutoScroll == "function"){
            var scrollSpeed = $(this).data('speed');
            stopAutoScroll(scrollSpeed);
			$(".aside-menu").removeClass("on");
        }
    });
	//滚动显示工具栏
    $(window).scroll(function() {
        if ($(document).scrollTop() >= 100 && $(document).scrollTop() <= $('.epContent').height()){
            toggleMenuBar("off");
        }else{
            toggleMenuBar("on");	 
        }
    });
	//转换连接
	$('.prev[data-type=prev],.next[data-type=next]').on('click', function() {
		var newsurl = $(this).attr("data-url");
		location.href = newsurl;
	});
	//打开登录提示
    $(document).on("click", ".logintips", function(){
			$("#login-tips").addClass("on").removeClass("off");
			$(".v8_mask").show();
        }
	 );
	 //关闭登录提示
    $(document).on("click", "#login-tips .close", function(){
			$("#login-tips").addClass("off").removeClass("on");
			$(".v8_mask").hide();
        }
	 );	
	//关闭APP提示
    $(document).on("click", "#app-tips .close", function(){
			$("#app-tips").addClass("off").removeClass("on");
			$(".v8_mask").hide();
        }
    );		
});
//APP提示
function setAppDownCookie() {
	var nowDate = new Date();
	nowDate.setTime(nowDate.getTime() + (600000));
	document.cookie = "AppDownTips=1;expires=" + nowDate.toGMTString() + ";path=/"
}
function getAppDownCookie() {
	var nameValue = "";
	var arr, reg = new RegExp("(^| )AppDownTips=([^;]*)(;|$)");
	if (arr = document.cookie.match(reg)) {
		nameValue = decodeURI(arr[2])
	}
	return nameValue
}
$(function() {
$(".browser").click(function() {
	setAppDownCookie();
    $(".v8_mask").hide();
	$("#app-tips").addClass("off").removeClass("on");
	$(".episode-detail").removeClass("off");
  });
});		
//图片加载
let cnamea = $('.center-title').text();
//章节列表
function sidelink(){
	$.ajax({
		url: "/api/comic/zyz/chapterlink",
		data: {
			id: id,
			type: cnamea
		},
		type: "get",
		dataType: "json",
		async: !0,
		success: function(l) {
			var k = "";
			$.each(l.data, function(c, h) {
				c = h.list;
				var l = $("#litempzj").html();
				$.each(c, function(c, h) {
					k += l.replace(/{name}/g, h.name).replace(/{nlink}/g, h.url).replace(/{on}/g, h.on)
				})
			});
			$("#chapterlist").after(k);
			$(".lazy").lazyload({
		       threshold: 200,
	         });
		}
	})
};
//延迟2秒加载
/*添加收藏**/

/*赞一个**/
function Digg(){
    base.msgTips("谢谢大人的认可");
    $(".chapter-like").addClass("active");
	$(".text1").html("已赞过").attr("onclick", "");
}
//错误反馈

//PC端小圆点拖拽
function drags() {
var drags = {
	down: !1,
	x: 0,
	y: 0,
	winWid: 0,
	winHei: 0,
	clientX: 0,
	clientY: 0
},
	asideNav = $("#aside-nav")[0],
	getCss = function(a, e) {
		return a.currentStyle ? a.currentStyle[e] : document.defaultView.getComputedStyle(a, !1)[e]
};
$("#aside-nav").on("mousedown", function(a) {
	drags.down = !0, drags.clientX = a.clientX, drags.clientY = a.clientY, drags.x = getCss(this, "right"), drags.y = getCss(this, "top"), drags.winHei = $(window).height(), drags.winWid = $(window).width(), $(document).on("mousemove", function(a) {
		if (drags.winWid > 640 && (a.clientX < 120 || a.clientX > drags.winWid - 50)) //50px
		return !1;
		if (a.clientY < 180 || a.clientY > drags.winHei - 120) return !1;
		var e = a.clientX - drags.clientX,
			t = a.clientY - drags.clientY;
		asideNav.style.top = parseInt(drags.y) + t + "px";
		asideNav.style.right = parseInt(drags.x) - e + "px";
	})
}).on("mouseup", function() {
	drags.down = !1, $(document).off("mousemove")
});
};