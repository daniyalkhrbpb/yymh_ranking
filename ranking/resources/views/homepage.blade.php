<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta http-equiv="Cache-Control" content="max-age=86400" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
<title>免费在线漫画平台_免费韩国漫画-YY漫画网</title>
<meta name="keywords" content="YY漫画网,YY漫画网在线,YY漫画网免费漫画,韩漫,韩漫在线" />
<meta name="description" content="YY漫画网是免费的漫画在线分享网站。漫画爱好者可以在这里收集,分享自己喜爱和原创漫画作品。" />
<link rel="icon" href="/favicon.ico" type="image/x-icon"/>
<link href="{{ asset('static/mammicms/') }}/css/b.min.css" rel="stylesheet">
<link href="{{ asset('static/mammicms/') }}/css/my.css" rel="stylesheet">
<script type="text/javascript" src="{{ asset('static/mammicms/') }}/js/jquery.min.js"></script> 
<script type="text/javascript" src="{{ asset('static/mammicms/') }}/js/b.min.js"></script>	
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"> <span class="sr-only">ZZ漫画网</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
			<a class="navbar-brand" href="/">ZZ漫画网</a> 
		</div>
		<div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li class="navitem"><a href="/">首页</a></li>
				<li class="navitem"><a href="#">近期更新</a></li>
				<li class="navitem"><a href="#">热门排行</a></li>
			</ul>
            <div style="float: right">
				<ol class="mt000breadcrumb mt000">
					<li><input placeholder="请输入关键词" type="text" class="form-control"></li>
					<li><button type="button" class="btn btn-sm btop11a">漫画搜索</button></li>
				</ol>
			</div>
		</div>		
	</div>
</nav>
<div class="container">

	<!-- <div class="row visible-xs visible-sm">
		<div class="col-md-12" style="margin:5px 0 20px 0 ">
			<div class="panel panel-default">
				<div class="panel-heading"> <span class="glyphicon glyphicon-tower"></span> 网友热搜 </div>
				<div class="panel-body panel-recommand">
					<ul class="list-inline px14">
                        @foreach($hotSearches as $comic)
						<li>
                            <a href="{{ route('comic.show', $comic->id) }}" title='{{ $comic->title }}'>{{ $comic->title }}</a> 
                            <small>({{ $comic->latest_chapter_title }})</small>
                        </li>
                        @endforeach
					</ul>
				</div>
			</div>
		</div>
	</div> -->

    <div class="row">
		<div class="col-sm-12 col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading"> <span class="glyphicon glyphicon-tower"></span> 热门推荐 </div>
				<div class="panel-body panel-recommand">
                    {{-- 热门推荐区块: 循环显示所有数据 --}}
                    @foreach($recommends as $comic)
					<div class="col-lg-3 col-sm-4 col-md-4 col-sm-index ">
                        <div class="media" style="border:none;margin-bottom: 0;padding-bottom: 0">
                            <div class="media-left media-heading">
                                <a href="{{ route('comic.show', $comic->id) }}" title='{{ $comic->title }}'>
									<img src="{{ asset($comic->cover_url) }}" alt="{{ $comic->title }}"  class="img" width="100" height="130">
                                    <!-- <img src="{{ $comic->cover_url }}" alt='{{ $comic->title }}' class="img" width="100" height="130" /> -->
                                </a>
                            </div>
                            <div class="media-body">
                                <h3 class="media-heading book-title">
                                    <a href="{{ route('comic.show', $comic->id) }}" title='{{ $comic->title }}'>{{ $comic->title }}</a>
                                </h3>
                                <p></p> 
                                <a style="color:#666" href="{{ route('comic.show', $comic->id) }}">{{ $comic->latest_chapter_title }}</a>
                                <p>{{ date('Y-m-d', strtotime($comic->last_updated_time)) }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>

    <div class="row">
        <div class="col-sm-8 col-md-8">
			<div class="panel panel-default">
				<div class="panel-heading"><span class="glyphicon glyphicon-star"></span>近期更新</div>
				<div class="panel-body">
					<table id="llastupdate" class="table">
                        @foreach($recentUpdates as $comic)
						<tr>
							<td class="px13">
                                <a href="{{ route('comic.show', $comic->id) }}" title="{{ $comic->title }}">{{ $comic->title }}</a>
                            </td>
							<td class="hidden-xs index-chapter nowrap">
                                {{-- 近期更新：已按时间倒序 --}}
                                <a href="{{ route('comic.show', $comic->id) }}" title="{{ $comic->latest_chapter_title }}">{{ Str::limit($comic->latest_chapter_title, 20) }}</a>
                            </td>
							<td class='book_author visible-lg nowrap'>
                                {{ $comic->latest_chapter_title }}
                            </td>
							<td class='book_update visible-lg nowrap'>{{ date('Y-m-d', strtotime($comic->last_updated_time)) }}</td>
                        </tr>
                        @endforeach
					</table>
				</div>
			</div>
		</div>

        <div class="col-sm-4 col-md-4">
            <div class="panel panel-default">
				<div class="panel-heading"><span class="glyphicon glyphicon-indent-right"></span> 新漫上线 </div>
				<div class="panel-body" >
					<ul class="list-group list-group-ext">
                        @foreach($newComics as $comic)
						<li class="list-group-item nowrap px13 ">
                            {{-- 新漫上线：显示更新日期 --}}
                            <span class="badge">{{ date('m-d', strtotime($comic->last_updated_time)) }}</span> 
                            <a href="{{ route('comic.show', $comic->id) }}" title="{{ $comic->title }}" class="list-a111">{{ $comic->title }}</a>
                        </li>
                        @endforeach
					</ul>
				</div>
			</div>
            <div class="panel panel-default">
                <div class="panel-heading"><span class="glyphicon glyphicon-stats"></span> 热门排行 </div>
                <div class="panel-body" >
                    <ul class="list-group list-group-ext">
                        @foreach($topRankings as $comic)
                        <li class="list-group-item nowrap px13 ">
                            <span class="badge">{{ $comic->views }}</span>
                            <a href="{{ route('comic.show', $comic->id) }}" title="{{ $comic->title }}" class="list-a111">
                                {{ $comic->title }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
		</div>
	</div>
    
	<footer class="footer">
		<p> &copy;YY漫画网 <a href="#">网站地图</a><br/>
			本站所有的漫画、图片、评论等，均由网友发表或上传并维护或收集自网络，属个人行为，与YY漫画网立场无关。 </p>
	</footer>
</div>
</body>
</html>