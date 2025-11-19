SQ = {
	thispostion: function(a) {
		var b = $(a).offset().left;
		a = $(a).offset().top + $(a).height();
		return {
			x: b,
			y: a
		}
	},
	windowpostion: function(a) {
		a = $(window).width() / 2 + $(window).scrollLeft();
		var b = $(window).height() / 2 + $(window).scrollTop();
		return {
			x: a,
			y: b
		}
	},
	mouseposition: function(a) {
		var b = 0,
			c = 0;
		a = a || window.event;
		if (a.pageX || a.pageY) b = a.pageX, c = a.pageY;
		else if (a.clientX || a.clientY) b = a.clientX + document.body.scrollLeft + document.documentElement.scrollLeft, c = a.clientY + document.body.scrollTop + document.documentElement.scrollTop;
		return {
			x: b,
			y: c
		}
	},
	Ajax: function(a) {
		a = $.extend({
			type: "post",
			data: "",
			dataType: "jsonp",
			before: function() {}
		}, a);
		burl = (-1 == a.request.indexOf("?") ? "?" : "&") + "_rnd=" + (new Date).getTime();
		$.ajax({
			type: a.type,
			url: a.request + burl,
			data: a.data,
			dataType: a.dataType,
			beforeSend: a.before,
			success: a.respon
		})
	},
	Ajax_async: function(a) {
		a = $.extend({
			type: "post",
			data: "",
			dataType: "jsonp",
			before: function() {}
		}, a);
		burl = (-1 == a.request.indexOf("?") ? "?" : "&") + "_rnd=" + (new Date).getTime();
		$.ajax({
			type: a.type,
			url: a.request + burl,
			async: !1,
			data: a.data,
			dataType: a.dataType,
			beforeSend: a.before,
			success: a.respon
		})
	},
	ajaxLoginCheck: function(a) {
		return "0" == a.is_login ? (SQ.Adiv(a), !1) : !0
	},
	boolIe: function() {
		return $.browser.msie && "6.0" == $.browser.version ? !0 : !1
	}
};