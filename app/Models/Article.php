<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    //
    protected $fillable = [
        'doc_id',
        'source',
        'published_at',
        'author',
        'category',
        'content',
    ];
}
