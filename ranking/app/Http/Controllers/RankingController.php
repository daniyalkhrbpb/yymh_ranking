<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manhua;
use App\Models\Category; 
use App\Models\Chapter;
use Illuminate\Support\Str; 

class RankingController extends Controller
{
    /**
     * 首页数据展示
     */
    public function showHomepage()
    {
        // 1. 网友热搜：按更新时间倒序
        $hotSearches = Manhua::where('is_hot_search', 1)
                             ->orderBy('last_updated_time', 'desc') 
                             ->get(); 
        
        // 2. 热门推荐：按更新时间倒序 (解决显示数量不足的问题)
        $recommends = Manhua::where('is_recommend', 1)
                            ->orderBy('last_updated_time', 'desc')
                            ->get(); 
        
        // 3. 近期更新：按最后更新时间倒序
        $recentUpdates = Manhua::where('is_latest', 1)
                               ->orderBy('last_updated_time', 'desc')
                               ->get(); 
        
        // 4. 新漫上线：按更新时间倒序
        $newComics = Manhua::where('is_new', 1)
                           ->orderBy('last_updated_time', 'desc')
                           ->get(); 
        
        // 5. 热门排行：按采集到的真实 views 点击量倒序
        $topRankings = Manhua::where('is_ranking', 1)
                             ->orderBy('views', 'desc')
                             ->get(); 

        return view('homepage', compact(
            'hotSearches', 'recommends', 'recentUpdates', 'newComics', 'topRankings'
        ));
    }

    /**
     * 漫画详情页展示
     */
    public function showComic($id)
    {
        // 确保漫画存在并关联加载分类和章节
        $comic = Manhua::with(['category', 'chapters' => function($query) {
            $query->orderBy('sort_order', 'asc'); 
        }])->findOrFail($id);

        // 侧边栏的精彩推荐：使用真实的点击量排行，限制取前 10 个
        $sidebarComics = Manhua::where('is_ranking', 1)
                               ->orderBy('views', 'desc')
                               ->take(10)->get(); 
        
        // 底部的猜你喜欢：同分类，排除当前漫画，按点击量倒序
        $youMayLikeComics = Manhua::with('category')
                                  ->where('category_id', $comic->category_id)
                                  ->where('id', '!=', $comic->id) // 排除当前漫画
                                  ->orderBy('views', 'desc')
                                  ->get(); // 不限制数量

        // SEO 设置 (保持不变)
        $seo_title = "{$comic->title}_{$comic->title}漫画免费阅读_{$comic->title}在线阅读-YY漫画网";
        $seo_keywords = "{$comic->title},{$comic->title}漫画,{$comic->title}漫画免费阅读,{$comic->title}最新章节";
        // 注意：Str::limit 用于确保 description 不会太长
        $seo_description = "YY漫画网出品的漫画{$comic->title}是由{$comic->author}漫画作家创作,{$comic->title}漫画讲述了:".Str::limit($comic->summary, 120);

        return view('show', compact('comic', 'sidebarComics', 'youMayLikeComics', 'seo_title', 'seo_keywords', 'seo_description'));
    }

    /**
     * 章节阅读引导页展示 (复刻源站引导页)
     * URL: /chapter/{comic_id}/{chapter_id}.html
     */
    public function readChapter($comic_id, $chapter_id)
    {
        // 查找漫画和章节，如果找不到会抛出 404
        $comic = Manhua::findOrFail($comic_id);
        $chapter = Chapter::findOrFail($chapter_id);

        // 【新增】获取当前漫画的所有章节列表，按 sort_order 升序排列
        $all_chapters = Chapter::where('comic_id', $comic_id)
                               ->orderBy('sort_order', 'asc')
                               ->get();

        // 侧边栏和底部的精彩推荐：按点击量排行，限制取前 6 个
        $youMayLikeComics = Manhua::where('id', '!=', $comic->id) 
                                  ->orderBy('views', 'desc')
                                  ->take(6)->get(); 

        // SEO设置
        $seo_title = "{$comic->title}漫画({$comic->author})_第{$chapter->title}在线免费阅读-YY漫画网";
        $seo_keywords = "{$comic->title},{$chapter->title},{$comic->title}漫画最新章节,{$comic->title}漫画,{$comic->title}漫画免费观看";
        $seo_description = "您当前阅读的漫画《{$comic->title}》是由{$comic->author}创作的免费漫画,最新章节{$chapter->title}免费在线阅读。";

        $chapter_title = $chapter->title;
        $comic_url = route('comic.show', ['id' => $comic->id]);

        // 获取下一话和上一话的链接 (使用 sort_order 字段来查找)
        $next_chapter = Chapter::where('comic_id', $comic_id)
                                ->where('sort_order', '>', $chapter->sort_order)
                                ->orderBy('sort_order', 'asc')
                                ->first();

        $prev_chapter = Chapter::where('comic_id', $comic_id)
                                ->where('sort_order', '<', $chapter->sort_order)
                                ->orderBy('sort_order', 'desc')
                                ->first();

        $next_url = $next_chapter ? route('chapter.read', ['comic_id' => $comic->id, 'chapter_id' => $next_chapter->id]) : 'javascript:void(0)';
        $prev_url = $prev_chapter ? route('chapter.read', ['comic_id' => $comic->id, 'chapter_id' => $prev_chapter->id]) : 'javascript:void(0)';
        
        // 漫画封面图
        $comic_cover_url = $comic->cover_url; 

        return view('read_chapter_guide', compact(
            'comic', 
            'chapter', 
            'chapter_title',
            'comic_url', 
            'next_url',
            'prev_url',
            'youMayLikeComics',
            'comic_cover_url',
            'seo_title', 
            'seo_keywords', 
            'seo_description',
            'all_chapters' // <-- 【新增】将所有章节传递给视图
        ));
    }
}