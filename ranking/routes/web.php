<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RankingController;
use Spatie\ResponseCache\Middlewares\CacheResponse;

// 首页
Route::get('/', [RankingController::class, 'showHomepage'])
     ->middleware(CacheResponse::class); // 开启全页缓存，速度飞快

// 漫画详情页 (伪静态 URL，模拟源站结构)
// 例如: http://your-site.com/yy/1234.html
Route::get('/yy/{id}.html', [RankingController::class, 'showComic'])
     ->middleware(CacheResponse::class)
     ->name('comic.show');

// 章节阅读页 (为您预留)
// 例如: http://your-site.com/chapter/1234/5678.html
Route::get('/chapter/{comic_id}/{chapter_id}.html', [RankingController::class, 'readChapter'])
     ->middleware(CacheResponse::class)
     ->name('chapter.read');