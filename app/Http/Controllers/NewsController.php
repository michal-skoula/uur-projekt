<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\View\View;

class NewsController extends ContentCollectionController
{
    public function index(): View
    {
        $posts = News::query()
            ->published()
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('news.index', compact('posts'));
    }

    public function show(News $news): View
    {
        $this->guardStatus($news);

        return view('news.show', compact('news'));
    }
}
