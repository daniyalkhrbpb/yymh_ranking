var showe = !1,isShowMenu=!1,headMenuDom=$('#js_headMenu'),footMemuDom=$('#js_footMenu'),isAuto = !1,isDark=null,enableAutoRead = !1,timerHandle = null,isVists = true,scale=1,n =$('body'),index='';
(function () {
    show();
    var t = get_cookie("nightMode");null !== t && t !== undefined && (t = t ? parseInt(t) : 0, this.isDark = t)
    refresh();
    if(KIMICMS.islogin>0 && KIMICMS.userid>0) {
        isfav();
        islogin();
    }
})();
n.on("mouseleave", "#js_headMenu", function() {close();});
n.on("mouseleave", "#js_footMenu", function() {close();});
n.on("mousemove", function(t) {bodyMouseMove(t)})
n.on("click", "#readerContainer", function() {onReaderClick()})
n.on("click", "#js_ftAutoBtn", function() {isVists && cliAutoRead();})
n.on("click", "#js_nightMode", function() {change()})
n.on("click", "#js_dayMode", function() {change()})
n.on('click','#collectionBtn',function (e) {e.stopPropagation();if(KIMICMS.islogin && KIMICMS.userid) fav(); else layer.msg('请先登录,方可收藏漫画');})
n.on('click','#js_Error',function (e) {
    e.stopPropagation();
    var ticketTpl = '';
    index = layer.open({
        type:1,
        shade:.8,
        title: '反馈错误',
        area: ['300px', '300px'],
        content:'<div class="feed-handles"> <div class="feed-input-wr"> <textarea class="feed-input" placeholder="^_^请仔细描述您的问题（必填）"></textarea> </div> <input class="feed-email" type="text" value="" placeholder="请填写验证码"><img src="/api/user/check/code" style="width:40%;height: 52px;display: inline;margin: -19px;margin-left: 1px;" onclick=\'this.src="/api/user/check/code"\'> <div class="feed-confirm guest" onclick="error()">提交反馈</div> </div>'
    })

})
function error()
{
    var code = $(".feed-email").val(),text = $(".feed-input").val();
    if(code == '' || code == undefined || code == null){
        layer.msg('验证码不可为空');
        return false;
    }
    if(text == '' || text == undefined || text == null){
        layer.msg('反馈内容不可为空');
        return false;
    }
    var data = {mid:read.aid,cid:read.cid,mname:read.articlename,cname:read.chaptername,code:code,text:text}
    $.post('/api/user/userarr/error',data,function (res){
        if(res.code == 0){
            layer.msg('反馈成功');
            layer.close(index);
        }else{
            layer.msg(res.msg);
        }
    },'json');
}
n.on('click','#js_payClose',function (e) {
    e.stopPropagation();
    layer.close(index1);
})
function ispay(vip,cion,page)
{
    if(vip>0 || cion >0)
    {
        if(KIMICMS.islogin==0 || KIMICMS.userid==0)
        {
          return index1 =  layer.open({
                type: 1, 
                title: '温馨提示',
                 area:  ['300px', '200px'],
                offset: ['200px', '40px'],
              content: $('#Pay_cion').html()
            });
        }else{
            get_buy(vip,cion,page);
        }
    }
}
function get_pay(vip,cion,page)
{
    if(KIMICMS.islogin==0 || KIMICMS.userid==0){
        get_login();
    }else{
        $.post('/api/pay/finance/cion',{
            mid:read.aid,
            cid:read.cid,
            page:page
        },function(res) {
            if(res.code == 0) window.location.reload();
            else layer.msg(res.msg);
        },'json');
    }
}
n.on('click','#Err_cionBuy',function () {window.location.href = '/user/index';})
n.on('click','#Js_buy_vip',function () {window.location.href = '/user/index';})
function get_buy(vip,cion,page)
{
    $.post('/api/pay/finance/ispay',{
        id:read.cid,page:page
    },function(res){
        let code = parseInt(res.code);
        switch(code){
            case 0:
                var parr = res.data;
                $('#imgsec').empty();
                for (var i = 0; i < parr.length; i++)
                {
                    $('#imgsec').append('<figure class="item" data-mod="0"><img class="calwh lazy" src="'+parr[i]['pic']+'" ></figure>');
                }
                break;
            case 102:
                layer.closeAll();
                index1 = layer.open({
                   type: 1, 
                title: '开通会员',
                 area:  ['300px', '200px'],
                offset: ['200px', '40px'],
                    content: $('#Err_cion').html()
                });
                break;
            case 108:
                layer.closeAll();
                index1 = layer.open({
                    type:1,
                    shade:.8,
                    closeBtn: !1,
                    area:['368px'],
                    title: !1,
                    skin: "transparent-bg",
                    shadeClose: !1,
                    zIndex: 140,
                    content: $('#Pay_vip').html()
                });
                break;
            case 103:
                layer.closeAll();
                index1 = layer.open({
                type: 1, 
                title: '开通会员',
                 area:  ['300px', '200px'],
                offset: ['200px', '40px'],
                    content: $('#Pay_cion').html()
                });
                break;
            default:
                layer.open({
                    content: res.msg,
                    shadeClose: false,
                    btn: '返回上一层',
                    yes: function(index) {
                        window.history.back(-1);
                    }
                });
        }
    }, 'json');
}
n.on('click','#js_autoBuy',function (e) {
    e.stopPropagation();
    $.post('/api/user/userarr/auto',{auto:1},function (res)
    {
        if(res.code == 0)
        {
            layer.msg('自动订阅开启成功');
            layer.close(index1);
            window.location.reload();
        }else{
            layer.msg(res.msg);
        }
    },'json');
})
function islogin()
{
    if(KIMICMS.islogin>0 && KIMICMS.userid>0) {
        $.post('/api/user/userarr/info',{'uid':KIMICMS.userid},function (res)
        {
        if(res.code == 0){
            $('#J_userInfo .name').text(res.user.nick);
            let vip = res.user.vip ? '是' : '否';
            $('#user-inner-vip').text(vip);
            $('#js_payChapterBuy').text('购买阅读');
            $('#J-user-cion').text(res.user.cion);
            $('#user-inner-cion').text(res.user.cion);
            $('#api-user-login').empty().append('<div class="status-exit"><div class="exit-btn" id="J_logout">退出登录</div></div>');
            var arr = res.book;
            if(arr.length > 0)
            {
                $('#J_bookNone').css('display','none');
                for(let i = 0;i<arr.length;i++)
                {
                    $('#J_bookshelfList').append('<li class="book-item collect-item"><a href="'+arr[i]['read_url']+'" target="_blank" class="book-info"><img class="img" src="'+arr[i]['cover']+'" style="background: url('+arr[i]['cover']+') center center / cover no-repeat;"><h4 class="title">'+arr[i]['bname']+'</h4><p class="desc isnew">读至: '+arr[i]['read_name']+'</p></a></li>');
                }
                $('#J_bookshelfBox').css('display','block');
                $('#J_bookshelfCount').text(res.count);
            }
        }else{
            layer.msg(res.msg);
        }
    },'json');
}}
//退出登录
n.on('click','#J_logout',function (e)
{
    window.location.href='/user/logout';
})
fav=()=>
{
    if($('#collectionBtn').hasClass('active'))
    {
        $.post('/api/user/bookcase/del',{articleid:read.aid},function (res)
        {
            if(res.code ==0){
                layer.msg('取消收藏成功');
                $("#collectionBtn").removeClass("active"), $("#collectionStatusText").text("收藏")
            }else{
                layer.msg(res.msg);
            }
        },'json');
    }else{
        $.post('/api/user/bookcase/add',{articleid:read.aid,articlename:read.articlename,chaptername:read.chaptername,chapterid:read.cid},function (res)
        {
            if(res.code ==0){
                layer.msg('收藏成功');
                $("#collectionBtn").addClass("active"), $("#collectionStatusText").text("已收藏")
            }else{
                layer.msg(res.msg);
            }
        },'json');
    }

}
function isfav()
{
    $.post('/api/user/bookcase/isfav',{aid:read.aid,cid:read.cid},function (res)
    {
        if(res.code ==0){
            $("#collectionBtn").addClass("active"), $("#collectionStatusText").text("已收藏")
        }
    },'json');
}
function refresh()
{
    var t = this.isDark;
    if (null === t) {
        var e = ((new Date).getUTCHours() + 8) % 24;
        t = e > 7 && e < 19 ? 0 : 1,
            this.isDark = t
    }
    t ? ($("#readerContainer").addClass("night"),
        $("#js_nightMode").hide(),
        $("#js_dayMode").show()) : ($("#js_dayMode").hide(),
        $("#js_nightMode").show(),
        $("#readerContainer").removeClass("night"))
}
function change()
{
    null === this.isDark ? this.isDark = 1 : this.isDark = this.isDark ? 0 : 1,
        this.refresh(), get_cookie("nightMode", this.isDark, {expires: 24})
}
function get_cookie(t,e,n) {
    if (n = n || {},
    void 0 === e) {
        var r = new RegExp("(?:(?:^|.*;)\\s*" + t + "\\s*\\=\\s*([^;]*).*$)|^.*$");
        return decodeURIComponent(document.cookie.replace(r, "$1")) || null
    }
    null === e && (n.expires = -1);
    var i = new Date;
    n.expires && i.setTime(i.getTime() + 36e5 * n.expires),
        document.cookie = t + "=" + encodeURIComponent(e) + ";" + (n.expires ? "expires=" + i.toGMTString() + ";" : "") + "path=/;" + (n.domain ? "domain=" + n.domain + ";" : "")

}
function doStopAutoRead() {
    this.isAuto && (cancelAnimationFrame(this.timerHandle),
        this.isAuto = !1,
        setTimeout(function() {
            this.enableAutoRead = !1
        }, 100),
        $("#js_ftAutoBtn").removeClass("active"))
}
function atferStartAutoRead()
{
    closeCatalog();
    $("#js_ftAutoBtn").addClass("active");
}
function closeCatalog() {
    this.showe && (this.showe = !1, this.toggleClass())
}
function startAutoRead()
{
    this.isAuto || this.isVists && (this.isAuto = !0,
        this.enableAutoRead = !0,
        this.atferStartAutoRead(),
        this.timerHandle = requestAnimationFrame(function() {
            timerAutoSpeed()
        }))
}
//点击登录，弹出登录iframe弹窗
n.on('click','#J_layerlogin',function (e)
{
    e.stopPropagation();
    get_login();
});
function get_login()
{
    index = layer.open({
        type:3,
        content: '<div class="v-popup bounceInUp animated on"id="login-tips"><div class="v-popup-main no_login"><a class="close" id="js_payClose" href="javascript:void(0);"></a><div class="v-popup-main__title">大人，您还没有登录呢</div><div class="v-popup-action"><button class="btn"id="login-btn"onclick="base.loginIn()">立即登录</button></div></div></div>'
    });
    layer.style(index, {
        backgroundColor: "#fff"
    });
}
n.on("click", "#J_login_close_btn", function() {
    layer.close(index)
});
function move(t) {
    var e = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;
    arguments.length > 2 && arguments[2] !== undefined && arguments[2];
    $("#readerContainer").animate({
        scrollTop: t
    }, e)
}
n.on('click','#js_goPay',()=>{window.location.href = '/user/buy'})
function timerAutoSpeed()
{
    let top = $('#readerContainer').scrollTop();
    let height = $('#reader-scroll').height();
    let a = top + Math.max(parseInt(1 / 3), 1);
    let o = Math.max(height - $(window).height(), 0);
    if(height-(top+64) < $(window).height()){
        this.doStopAutoRead();
    }else{
        move(a)
        this.timerHandle = requestAnimationFrame(function() {
            this.timerAutoSpeed()
        })
    }
}
function cliAutoRead()
{
    if (this.isAuto) doStopAutoRead();
    else {
        var t = 1;
        this.startAutoRead(t)
    }
}
$(document).on("mousewheel DOMMouseScroll", function (e){
    let top = $('#readerContainer').scrollTop();
    let height = $('#reader-scroll').height();
    if(top <= 64 || height-(top+64) < $(window).height())return show();
    if(top >= 64)return close();
});
function onReaderClick()
{
    setTimeout(function() {
        this.isShowMenu ? close() : show()
    }, 100);
}
function bodyMouseMove(t) {
    var e = t.pageY;
    let height = $(window).height();
    if (e <= 64 || e > height - 64)
        return show(),
            void (this.isHoverMenu = !0);
    this.isHoverMenu = !1
}
function toggleClass() {
    $("#js_btnCatalog").removeClass('active');
    $("#js_chapterCatalog").removeClass('show');
}
function show(){
    this.isShowMenu || (this.isShowMenu = !0,
        this.headMenuDom.addClass("show"),
        this.footMemuDom.addClass("show"))
}
function close()
{
    closeCatalog()
    this.isShowMenu && (this.isShowMenu = !1,
        this.headMenuDom.removeClass("show"),
        this.footMemuDom.removeClass("show"))
}
n.on("click", "#scaleFullscreenBtn", function() {
    fullscreen()
})
n.on('click','#js_catalogBtn',function (e) {
    e.stopPropagation();
    var listType;
    if ($("#js_catalogBtn").hasClass("order-reverse")) {
        listType = 1;
        $("#js_catalogBtn").removeClass("order-reverse");
        $("#js_catalogText").text("升序");
    } else {
        listType = 0;
        $("#js_catalogBtn").addClass("order-reverse");
        $("#js_catalogText").text("降序")
    }
    resort($('#js_catalogList li'), listType);
})
function resort(data, listType)
{
    if (data.length !== 0) {
        var arr = [];
        for (var i = 0; i < data.length; i++) {
            arr.push(data[i]);
        }
    } else {
        return false
    }
    arr.sort(function (a, b) {
        return a.getAttribute('data-chapter') - b.getAttribute('data-chapter')
    });
    if (listType == 0) {
        arr.reverse()
    }
    $('#js_catalogList').html('');
    for (var i = 0; i < data.length; i++) {
        $('#js_catalogList').append(arr[i]);
    }
}
function fullscreen()
{
    if (document.fullscreenElement) exitFullscreen()
    else launchFullscreen(document.documentElement)
}
n.on("click", "#js_btnCatalog", function()
{
    if(!showe){
        $(this).addClass('active');
        $('#js_chapterCatalog').addClass('show');
    }else{
        toggleClass()
    }
    showe = !showe
})
n.on("click", "#js_guessSidebarBtn", function() {
    console.log(n.width());
    (closeMenuControl(),
        $("#js_guessSidebar").hasClass("sidebar-show")) ? $("#js_guessSidebar").removeClass("sidebar-narrow sidebar-show") : (n.width() < 1760 && $("#js_guessSidebar").addClass("sidebar-narrow"),
        $("#js_guessSidebar").addClass("sidebar-show"))
});
$("#js_guessSidebar").on("mousemove", function(e) {
    var i = $(e.target);
    i.hasClass("sidebar-item") || i.parents(".sidebar-item").length ? $("#sidebarMain").css("right", -294) : $("#sidebarMain").css("right", 0)
})
function closeMenuControl() {
    close();
}
function launchFullscreen(element) {
    if (element.requestFullscreen) {
        element.requestFullscreen()
    } else if (element.mozRequestFullScreen) {
        element.mozRequestFullScreen()
    } else if (element.msRequestFullscreen) {
        element.msRequestFullscreen()
    } else if (element.webkitRequestFullscreen) {
        element.webkitRequestFullScreen()
    }else{
        layer.msg('当前浏览器不支持全屏模式');
    }
}
function exitFullscreen() {
    if (document.exitFullscreen) {
        document.exitFullscreen()
    } else if (document.msExitFullscreen) {
        document.msExitFullscreen()
    } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen()
    } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen()
    }else{
        layer.msg('退出全屏失败');
    }
}

