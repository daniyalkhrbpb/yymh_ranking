<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manhua;
use App\Models\Category; 
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
}