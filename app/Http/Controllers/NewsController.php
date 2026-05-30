<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        $posts = News::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('news.index', compact('posts'));
    }

    public function show(News $news): View
    {
        return view('news.show', compact('news'));
    }
}
