<?php

namespace App\Models;

use App\Contracts\ContentCollectionModel;
use Database\Factories\NewsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends ContentCollectionModel
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

    public function getName(): string
    {
        return $this->title;
    }

    public function getCollectionSlug(): string
    {
        return 'news';
    }

    public function getIdentifier(): int
    {
        return $this->id;
    }

    public function getPermalink(): string
    {
        return "/news/$this->slug";
    }
}
