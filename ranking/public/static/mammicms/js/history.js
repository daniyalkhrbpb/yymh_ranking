var indexedDB =  window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB,dbname = 'history',table='tab_history',c=$,r = 20, f = !1, m = 0,open = indexedDB.open(dbname),thisdb = null;
var s=$,v=null,g={collect:{index: 0, tab: s(".js_collect_tab"), icons: s(".js_collect_icon"), main: s("#js_collect_main"), handle: s("#js_collect_handle"), handleSelect: s("#js_collect_select"), handleDelete: s("#js_collect_delete"), statusNone: s("#js_collect_main").siblings(".js_status_none"), statusLoading: s("#js_collect_main").siblings(".js_status_loading"), statusError: s("#js_collect_main").siblings(".js_status_error"), statusAgain: s("#js_collect_main").siblings(".js_status_error .js_status_again")
    }},p={collect: {confirm: s(".layerui-collectConfirm").html(), iconView: ['<i class="ift-menu"></i>', '<i class="ift-list"></i>'], iconManage: ['<i class="ift-edit"></i>', "完成"]  }};
(function () {
    open.onupgradeneeded = (e) =>
    {
        thisdb = e.target.result;
        if(!thisdb.objectStoreNames.contains(table))
        {
            let objStorage = thisdb.createObjectStore(table,{keyPath:'id',autoIncrement:true});
            objStorage.createIndex("mid","mid",{unique:false});
        }
    }
})();
set_history = (name,cname,info_url,uri,cover,author,intro) =>
{
   open.onsuccess = (e) =>
   {
       console.log('数据库链接成功');
       thisdb = e.target.result;
       let tran = thisdb.transaction(table,'readonly');
       let request = tran.objectStore(table).index('mid').get(read.aid);
       request.onsuccess = (e) =>
       {
           console.log('查询键值成功');
           if(request.result) {
               console.log('查询到相关数据，开始更新');
               let data = {'id':request.result.id,'mid':read.aid,'cid':read.cid,'cover':cover,'info_url':info_url,'name':name,'intro':intro,'cname':cname,'author':author,'url':uri};
               let add = thisdb.transaction(table,'readwrite').objectStore(table).put(data);
               add.onsuccess = e =>{
                   console.log('数据更新成功',e);
               }
               add.onerror = e => {
                   console.log('数据更新失败',e);
               }
           }else{
               console.log('未查询到数据,进行增加');
               let data = {'mid':read.aid,'cid':read.cid,'cover':cover,'info_url':info_url,'name':name,'intro':intro,'cname':cname,'author':author,'url':uri};
               let add = thisdb.transaction(table,'readwrite').objectStore(table).put(data);
               add.onsuccess = e =>{
                   console.log('添加数据成功',e);
               }
               add.onerror = e => {
                   console.log('添加数据失败',e);
               }
           }
           close_history()
       }
       request.onerror = e =>
       {
           console.log('查询键值失败',e);
       }
   }
   open.onerror = (e) => {
       console.log('数据库链接失败:'+e.target.errorCode);
   }
}
//获取阅读历史
get_history = () =>
{
    open.onsuccess = (e) =>
    {
        thisdb = e.target.result;
        let request = thisdb.transaction(table,'readwrite').objectStore(table).getAll();
        request.onsuccess = (e) =>
        {
            if(request.result.length > 0)
            {
                var arr = request.result;
                if(arr.length > 0){$('.js_status_none').hide()}
                for(let i=0; i < arr.length; i++)
                {
                    $('#js_collect_main').append('<div class="bookrack-item js_item" data-id="'+arr[i].id+'"><div class="bookrack-inner"><img src="'+arr[i].cover+'" alt="" class="cover" style="background: url('+arr[i].cover+') center center / cover no-repeat; opacity: 1;"><h3 class="title">'+arr[i].name+'</h3><p class="subtitle"></p><p class="desc">阅读至 '+arr[i].cname+'</p><div class="btns"><div class="btn btn-primary is-radius js_collect_cancel" onclick="isread(\''+arr[i].url+'\')">继续阅读</div></div><div class="cloth"></div><span class="mark js_mark"><i class="ift-check"></i></span></div></div>');
                }
            }
        }
        close_history();
    }
    open.onerror = (e) =>
    {
        console.log('打开数据库失败'+e.target.errorCode);
    }
}
del_history = (n) =>
{
    console.log(n);
    var open = indexedDB.open(dbname);
    open.onsuccess = (e) => {
        this.thisdb = e.target.result;
        let arr = n.split(',');
        for (let i = 0; i < arr.length; i++) {
            let key = parseInt(arr[i]);
            this.thisdb.transaction(table, 'readwrite').objectStore(table).delete(key);
        }
        close_history();
    }
    open.onerror = (e) => {
        console.log(e.target.errorCode);
    }
}
close_history=()=>{
    this.thisdb.close();
    this.thisdb = null;
}
isread = (url) =>{window.location.href = url;}
$('.js_collect_icon').on("click", function() {var t = s(this), e = t.data("func"), c = g.collect.main.find(".js_item");"manage" === e && (!c.length || g.collect.main.hasClass("items-manage") ? d(!1) : d(!0)), "view" === e && (e = 1 === parseInt(t.data("status")) ? 0 : 1, t.html(p.collect.iconView[e]).data("status", e), e ? g.collect.main.addClass("items-list").removeClass("items-block") : g.collect.main.addClass("items-block").removeClass("items-list"))})
d=(t)=> {var e = g.collect, c = e.icons.filter('[data-func="manage"]');e.main.find(".js_mark").removeClass("checked"), t ? (c.html(p.collect.iconManage[1]), e.main.addClass("items-manage"), e.handle.removeClass("hide")) : (c.html(p.collect.iconManage[0]), e.main.removeClass("items-manage"), e.handle.addClass("hide"))}
g.collect.handleSelect.on("click", function() {var t = n();g.collect.main.find(".js_mark")[t ? "removeClass" : "addClass"]("checked"), n()})
n=()=> {
    var t = g.collect.main.find(".js_item"), e = g.collect.main.find(".js_mark");
    if (t.length ? g.collect.statusNone.hide() : (g.collect.statusNone.show(), d(!1)), e.length) {e = g.collect.main.find(".js_mark").not(".checked").length;return g.collect.handleSelect.text([e ? "全选" : "取消全选"]), !e}
    return g.collect.handleSelect.text("全选"), !1
}
g.collect.main.on("click", ".js_item", function() {var t = s(this), e = t.find(".js_mark");g.collect.main.hasClass("items-manage") ? (e.hasClass("checked") ? e.removeClass("checked") : e.addClass("checked"), n()) : ''})
g.collect.handleDelete.on("click", function() {
    var t = g.collect.main.find(".js_mark.checked");
    if (0 === t.length) return msg("请先选中要删除的漫画～"), !1;
    var a = [], i = [];
    t.each(function(t, e) {
        var c = $(this).parents(".js_item"), n = parseInt(c.data("id"));
        i.push(c), a.push(n)
    });
    let n = a.join(",");
    del_history(n);
    location.reload();
});
$('.js_record_tab').on('click',function () {window.location.href = $(this).data('href');});
$('.js_collect_tab').on('click',function () {window.location.href = '/user/bookcase'});
(function (){
    get_history();
})();