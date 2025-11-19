<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1,minimum-scale=1">
    <title>{{ $seo_title }}</title>
    <meta name="keywords" content="{{ $seo_keywords }}" />
    <meta name="description" content="{{ $seo_description }}" />
    <link rel="canonical" href="{{ request()->url() }}" />
    <script>let KIMICMS = {islogin:'0',userid:'0'};let read={aid:'{{ $comic->id }}',cid:'{{ $chapter->id }}',articlename:'{{ $comic->title }}',chaptername:'{{ $chapter->title }}',order:'0.00'}</script>
    <script src="{{ asset('static/mammicms/') }}/js/jquery.min.js"></script>
    <script src="{{ asset('static/mammicms/') }}/js/mescroll.min.js"></script>
    <script src="{{ asset('static/mammicms/') }}/js/base.js"></script>
    <link href="{{ asset('static/mammicms/') }}/css/app.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('static/mammicms/') }}/css/style.css">
    <link rel="stylesheet" href="{{ asset('static/mammicms/') }}/css/read.css" >
</head>
<body data-lang="cn" style="font-size: 12px;">
<h1 class="arTit" style="visibility:hidden;">免费阅读:{{ $comic->title }}【{{ $chapter->title }}】 - YY漫画网</h1>
<div class="statusbar"></div>
<div class="clearfix"></div>
<style rel="stylesheet">@media only screen and (min-width:1024px){html{max-width:810px;}}body,html{overflow-x:hidden;height:auto}</style>
<div class="cm-topbar bg-white topMenu on">
  <div class="cm-topbar__container">
    <div class="center-title">{{ $chapter_title }}</div>
    <div class="pull-left">
      <button class="back-btn" onclick="location.href='{{ $comic_url }}';">后退</button>
    </div>
    <div class="pull-right">
      <a href="javascript:void(0);" class="btn-text">选集</a>
    </div>
  </div>
</div>
<div class="clearfix"></div>
<br><br><br><br>
	    <div style="width: 100%;background-color: black;color: white;padding: 10px 0;">
                        	<p style="text-align: center;line-height: 20px;">当前《{{ $comic->title }}》漫画为删减版！</p>
	<div style="text-align: center;">
		<button style="background-color: orangered;font-size: 18px;color: white;border: none;border-radius: 12px;width: 40%;margin-top: 5px;" onclick="javascript:location.href='https://js.kegalu.com/vip/api.php';">点击观看完整版</button>
	</div>
	<p style="text-align: center;line-height: 20px;">网站高峰打不开，免费阅读请下载APP</p>
	<div style="text-align: center;">
		<button style="background-color: orangered;font-size: 18px;color: white;border: none;border-radius: 12px;width: 40%;margin-top: 5px;" onclick="javascript:location.href='https://js.kegalu.com/vip/api.php';">VIP通道</button>
		<button style="background-color: orangered;font-size: 18px;color: white;border: none;border-radius: 12px;width: 40%;margin-top: 5px;" onclick="javascript:location.href='https://js.kegalu.com/vip/api.php';">免费APP</button>	
        	</div>
        </div>
	<article class="epContent episode-detail" id="reader-scroll">
    	<div id="showimgcontent" style="display: none;">
	   	</div>
	   	   <div class="acgn-reader-chapter v" id="reader-scroll">
            <div class="acgn-reader-chapter__scroll-box"  id="js_ftPageRow" >
                <div class="acgn-reader-chapter__item-box" id="imgsec">                                        <figure class="item" data-mod="0"><img class="calwh lazy" src="{{ asset('static/mammicms/') }}/img/load.gif" data-src="{{ asset('static/mammicms/') }}/img/logintips.png"></figure> 
                                    </div>
            </div>
        </div>
	   <div class="imgbg"></div> 
	   	<div class="mask"></div>
   	</article>
	<div class="clearfix"></div>
	  <center class="cm-nr__container"> 
	      <div class="action">
	          <a href="{{ $next_url }}" id="next" class="btn"><span id="nextINFO">继续阅读下一话</span></a>         </div>   
	 </center>
	 <div style="height:1rem;"></div>
	 <div class="clearfix"></div>
	    <div class="clearfix"></div>
	<div class="clearfix"></div>
<div class="offset-10"></div>
<div class="episode-side" data-mod="2">
  <div class="col fava">
    <button class="chapter-fav hd-collection" id="collectionBtn">
      <i class="icon-fav"></i>
      <div id="collectionStatusText" class="text collection-status selectedblue">收藏</div>
    </button>
  </div>
  <div class="col">
    <button onclick="location.href='{{ $comic_url }}'">
      <i class="icon-dir"></i>
      <div class="text">目录</div>
    </button>
  </div>
  <div class="col">
    <button class="chapter-like" id="like_114668" onclick="Digg();">
      <i class="icon-laud"></i>
      <div class="text1">点赞</div>
    </button>
  </div>
</div>
	<div class="clearfix"></div>
<div class="episode-pagination">
  <div class="col"><a href="{{ $prev_url }}" id="prev" class="prev">上一话</a></div>
  <div class="line"></div>
  <div class="col"><a href="{{ $next_url }}" id="next" class="next">下一话</a></div>
</div>
	<div class="clearfix"></div>
<div class="offset-10"></div>
<div class="bm-box">
   <ul id="read_list">
   	@foreach($youMayLikeComics as $like)
   	<li style="list-style: none;"><a href="{{ route('comic.show', ['id' => $like->id]) }}"><span class="on{{ $loop->iteration }}">{{ $loop->iteration }}</span>{{ $like->title }}：{{ Str::limit($like->summary, 20) }}</a></li>
   	@endforeach
    </ul>
</div>
<div class="tooltip-bar bottomMenu">
  <div class="tooltip-bar__row">
    <div class="col">
                    <a href="{{ $prev_url }}" id="prev"class="prev">上一话</a>    </div>
    <div class="col">
      <button class="chapter-like" id="like_114668" onclick="Digg();"><i class="icon-laud"></i><em class="text1">点赞</em></button>
    </div>  
    <div class="col">
     <button onclick="location.href='{{ $comic_url }}'"><i class="icon-dir"></i><div class="text">目录</div></button>
     </div>
    <div class="col fava">
      <button class="chapter-fav hd-collection" id="collectionBtn"><i class="icon-fav"></i><em id="collectionStatusText" class="text collection-status selectedblue">收藏</em></button>
    </div>
    <div class="col">
        <a href="{{ $next_url }}" id="next"class="next">下一话</a>     </div>
  </div>
</div>
	<div class="clearfix"></div>
<section class="epListFrame off">
    <div class="bg"></div>
    <div class="data">
        <ul class="list_area">
            {{-- 【已修复】只使用 Blade 渲染章节列表，移除 JS 模板，防止客户端 JS 再次执行渲染逻辑，导致重复。 --}}
            @foreach ($all_chapters as $chap)
                @php
                    // 判断是否为当前章节
                    $active_class = ($chap->id == $chapter->id) ? 'on' : '';
                    $chapter_link = route('chapter.read', ['comic_id' => $comic->id, 'chapter_id' => $chap->id]);
                @endphp
                <li><a href="{{ $chapter_link }}" class="{{ $active_class }}">{{ $chap->title }}</a></li>
            @endforeach
        </ul>
    </div>
</section>

<div class="circle-box bounceInUp animated">
       <a href="/" id="home"><i></i></a>
</div>
<div class="v8_mask"></div>
<div class="v-popup bounceInUp animated" id="login-tips">
      <div class="v-popup-main no_login">
	   <a class="close" href="javascript:void(0);"></a>
        <div class="v-popup-main__title">大人，您还没有登录呢</div>
        <div class="v-popup-action">
          <button class="btn" id="login-btn" onclick="base.loginIn()">立即登录</button>
        </div>
      </div>
</div>
<div id="Pay_vip" style="display: none">
    <div class="v7_mask show" id="gold-tips">
    <div class="v7_mask_tips">
    <a class="close" id="js_payClose" href="javascript:void(0);"></a>
    <h3 class="title">会员专属章节</h3>
    <p class="cont">仅支持会员用户解锁阅读</p>
    <button class="btn" id="Js_buy_vip">去开通会员</button>
  </div>
</div>
</div>
<div id="Err_cion" style="display: none;">
    <div class="acgn-gift-dialog acgn-pay__box">
        <div class="hd icon-comm-flower2"><i class="icon-comm-bow"></i><i class="icon-comm-close comm-close-btn close" id="js_payClose"></i></div>
        <div class="bd acgn-pay">
            <div class="pay-panel">
                <div class="split"></div>
                <div class="info">购买 {{ $comic->title }} {{ $chapter->title }}</div>
                <div class="payment">支付 <span class="imp">0</span> 漫画币</div>
                <div class="btn" id="Err_cionBuy"><i class="icon-read-star"></i>余额不足,充值余额<i class="icon-read-star"></i></div>
                <div class="assets">我的漫画币&nbsp;<span class="imp" id="J-user-cion">0</span><span class="refill" id="js_goPay">去充值<i class="ift-right"></i></span></div>
            </div>
        </div>
    </div>
</div>
<div id="Pay_cion" style="display: none;">
    <div class="acgn-gift-dialog acgn-pay__box">
        <div class="hd icon-comm-flower2"><i class="icon-comm-bow"></i><i class="icon-comm-close comm-close-btn close" id="js_payClose"></i></div>
    <div class="bd acgn-pay">
        <div class="pay-panel">
            <div class="split"></div>
            <div class="info">该章节已锁定，本章节为会员专属</div>
            <div class="payment">请升级会员之后解锁观看</div>
            <div class="btn" id="js_payChapterBuy" onclick="get_pay(0,0,1)"><i class="icon-read-star"></i>升级会员<i class="icon-read-star"></i></div>
                </div>
            </div>
            
        </div>
    </div>
<script src="{{ asset('static/mammicms/') }}/layer/layer.js"></script>
<script src="{{ asset('static/mammicms/') }}/js/epview.js"></script>

	<div id="promotion-banner">
		<div class="promo-logo">
			<img src="https://js.kegalu.com/vip/css/logo.png" alt="Logo" data-clarity-loaded="o900yz">
		</div>
		<div class="promo-text">
			欢迎下载本站APP  <br>免费看全集无栅碱漫画！
		</div>
		<a href="https://down.njshx.com/xxsman.apk" class="promo-button">下载APP</a>
		<div id="close-promotion-btn" class="promo-close">×</div>
	</div>
</body>
</html>