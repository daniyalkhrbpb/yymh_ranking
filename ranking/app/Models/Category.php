<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // 指向数据库中的分类表
    protected $table = 'categories';
    
    // 不需要 Laravel 自动维护时间戳 (created_at/updated_at)
    public $timestamps = false;
}