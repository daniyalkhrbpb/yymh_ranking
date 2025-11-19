<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    // 指向数据库中的章节表
    protected $table = 'chapters';
    
    // 不需要自动维护时间戳
    public $timestamps = false;
}