<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta http-equiv="Cache-Control" content="max-age=86400" />
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
<title>{{ $seo_title }}</title>
<meta name="keywords" content="{{ $seo_keywords }}"/>
<meta name="description" content="{{ $seo_description }}"/>

<meta property="og:type" content="novel"/>
<meta property="og:title" content="{{ $comic->title }}"/>
<meta property="og:description" content="{{ Str::limit($comic->summary, 100) }}"/>
<meta property="og:image" content="{{ asset($comic->cover_url) }}"/>
<meta property="og:novel:category" content="{{ $comic->category->name ?? '韩漫' }}"/>
<meta property="og:novel:author" content="{{ $comic->author }}"/>
<meta property="og:novel:status" content="{{ $comic->status }}"/>
<meta property="og:novel:update_time" content="{{ $comic->last_updated_time }}"/>
<meta property="og:novel:latest_chapter_name" content="{{ $comic->latest_chapter_title }}"/>

<link rel="icon" href="/favicon.ico" type="image/x-icon"/>
<link href="{{ asset('static/mammicms/') }}/css/b.min.css" rel="stylesheet">
<script type="text/javascript" src="{{ asset('static/mammicms/') }}/js/jquery.min.js"></script> 
<script type="text/javascript" src="{{ asset('static/mammicms/') }}/js/b.min.js"></script>	
<link href="{{ asset('static/mammicms/') }}/css/my.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
			<a class="navbar-brand" href="/">YY漫画网</a> 
		</div>
		<div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li class="navitem"><a href="/">首页</a></li>
                <li class="navitem"><a href="#">漫画大全</a></li>
			</ul>
		</div>		
	</div>
</nav>

<div class="container">
    <ol class="breadcrumb">
        <li><a href="/">首页</a></li>
        <li><a href="#">{{ $comic->category->name ?? '漫画' }}</a></li>
        <li class="active">{{ $comic->title }}</li>
    </ol>

    <div class="row">
        <div class="col-sm-8 col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">漫画简介</div>
                <div class="pannel-body info">
                    <div class="info1">
                        <img src="{{ asset($comic->cover_url) }}" height="130" width="100" alt="{{ $comic->title}}"/>
                    </div>
                    <div class="info2">
                        <h1 class="text-left">{{ $comic->title }}</h1>
                        <h3 class="text-left" style="line-height:20px; height:20px;padding:0px;margin:0px;">
                            漫画作者：{{ $comic->author }}
                        </h3>
                        <div>
                            <p>漫画简介：{{ $comic->summary }}</p>
                        </div>
                    </div>
                    <div style="clear:both"></div>
                </div>
                <div class="panel-body text-center info3">
                    <p>漫画类别：{{ $comic->category->name ?? '韩漫' }} / 漫画状态：{{ $comic->status }}</p>
                    <p>最后更新：<font color="#000000">{{ $comic->last_updated_time }}</font></p>
                    <p>最新章节：<a href="#">{{ $comic->latest_chapter_title }}</a></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 col-sm-4 hidden-xs hidden-sm">
            <div class="panel panel-default">
                <div class="panel-heading">精彩推荐</div>
                <div class="panel-body">
                    <ul class="list-group list-group-ext">
                        @foreach($sidebarComics as $index => $item)
                        <li class="list-group-item nowrap px13">
                            <font class='id_sequence'>{{ $index + 1 }}.</font>
                            <a href="{{ route('comic.show', $item->id) }}" title='{{ $item->title }}'>
                                {{ $item->title }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row rowzhangjie">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    连载列表
                    <div class="color1001">
                        排序：<a href="javascript:void(0);" class="desc desc_on">倒序</a> 
                        <em>|</em> 
                        <a href="javascript:void(0);" class="asc">正序</a>
                    </div>
                </div>
                <div class="panel-body" id="play_0">
                    <ul class="list-group list-charts">
                        @forelse($comic->chapters as $chapter)
                        <li>
                            <a href="{{ route('chapter.read', ['comic_id' => $comic->id, 'chapter_id' => $chapter->id]) }}" title="{{ $chapter->title }}">
                                {{ $chapter->title }}
                            </a>
                        </li>
                        @empty
                        <li><a>暂无章节</a></li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class='row'>
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">阅读说明 </div>
                <div class="panel-body book-ext-info">
                    1.YY漫画网提供{{ $comic->title }}无弹窗阅读，让读者享受干净，清静的阅读环境,我们的口号——“YY漫画网网真正的无弹窗漫画网”<br/>
                    2.我们将持续更新本书，但如果您发现本漫画<strong>{{ $comic->title }}</strong>
                    最新章节，而<strong>YY漫画网</strong>
                    阅读网又没有更新，请通知YY漫画网,您的支持是我们最大的动力。<br/>
                    3.读者在{{ $comic->title }}全卷浏览中如发现内容有与法律抵触之处，请马上向本站举报。希望您多多支持本站，非常感谢您的支持!<br/>
                    4.本漫画《{{ $comic->title }} 》是本好看的{{ $comic->category->name ?? '职场' }}漫画，但其内容仅代表作者{{ $comic->author }}本人的观点，与YY漫画网阅读网的立场无关。<br/>
                    5.如果读者在阅读{{ $comic->title }}时对作品内容、版权等方面有质疑，或对本站有意见建议请联系管理员处理。<br>
                    6.《{{ $comic->title }}》是一本优秀漫画,为了让作者:{{ $comic->author }}能提供更多更好崭新的作品，请您购买本书的VIP或{{ $comic->title }}完本、全本、完结版实体漫画及多多宣传本书和推荐，也是对作者的一种另种支持!漫画的未来，是需要您我共同的努力! 
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">猜你喜欢 </div>
                <div class="panel-body panel-recommand">
                    {{-- 使用我们查询到的 youMayLikeComics --}}
                    @foreach($youMayLikeComics as $comic)
                    <div class="col-lg-3 col-sm-4 col-md-4 col-sm-index ">
                        <div class="media" style="border:none;margin-bottom: 0;padding-bottom: 0">
                            <div class="media-left media-heading">
                                <a href="{{ route('comic.show', $comic->id) }}" title='{{ $comic->title }}'>
                                    <img src="{{ asset($comic->cover_url) }}" alt='{{ $comic->title }}' class="img" width="90" height="120" />
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
    
	<footer class="footer">
		<p> &copy;YY漫画网(www.syyym.net) <a href="#">网站地图</a><br/>
			本站所有的漫画、图片、评论等，均由网友发表或上传并维护或收集自网络，属个人行为，与YY漫画网立场无关。 </p>
	</footer>
</div>

<div style="display:none"></div>
</body>
</html>