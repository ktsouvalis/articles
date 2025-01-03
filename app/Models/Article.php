<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'doc_id',
        'source',
        'published_at',
        'author',
        'category',
        'content',
        'created_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
