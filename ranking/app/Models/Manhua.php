<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manhua extends Model
{
    use HasFactory;

    // 假设您的表名是 comics
    protected $table = 'comics'; 
    protected $guarded = [];
    public $timestamps = false; // 关闭 Laravel 默认的时间戳维护

    // 关系：一个漫画属于一个分类
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // 关系：一个漫画有多个章节
    public function chapters()
    {
        return $this->hasMany(Chapter::class, 'comic_id');
    }

    /**
     * 访问器：确保 views 字段返回整数
     * @param  string  $value
     * @return int
     */
    public function getViewsAttribute($value): int
    {
        return (int)$value;
    }

    /**
     * 访问器：处理封面图路径，如果不存在则返回一个默认图
     * @param  string|null  $value
     * @return string
     */
    public function getCoverUrlAttribute($value): string
    {
        // 假设您的爬虫已经抓取了完整URL
        if (empty($value)) {
            return '/images/default-cover.jpg'; 
        }
        return $value;
    }
}