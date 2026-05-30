<?php

namespace App\Models;

use Database\Factories\NewsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    /** @use HasFactory<NewsFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'thumbnail',
        'author',
        'published_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'published_at' => 'datetime',
    ];
}
