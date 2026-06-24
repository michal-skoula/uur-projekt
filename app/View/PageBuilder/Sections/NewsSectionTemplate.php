<?php

namespace App\View\PageBuilder\Sections;

use App\Contracts\SectionTemplate;
use App\Models\News;
use Awcodes\Curator\Models\Media;
use Illuminate\Contracts\View\View;

final class NewsSectionTemplate implements SectionTemplate
{
    public string $tagline = '';

    public string $title = '';

    public string $buttonText = '';

    /** @var array<int, array{title: string, slug: string, excerpt: string, thumbnail: Media|null, author: string, date: string}> */
    public array $posts = [];

    public function prepareData(array $data): static
    {
        $this->tagline = $data['tagline'] ?? '';
        $this->title = $data['title'] ?? '';
        $this->buttonText = $data['button_text'] ?? '';

        $this->posts = News::query()
            ->published()
            ->orderByDesc('published_at')
            ->limit(3)
            ->get()
            ->map(fn (News $news): array => [
                'title' => $news->title,
                'slug' => $news->slug,
                'excerpt' => $news->excerpt ?? '',
                'thumbnail' => $news->thumbnail ? Media::query()->where('id', (int) $news->thumbnail)->first() : null,
                'author' => $news->author ?? '',
                'date' => $news->published_at?->format('d.m.Y') ?? '',
            ])
            ->values()
            ->all();

        return $this;
    }

    public function render(): View
    {
        return view('page-builder.sections.news', [
            'tagline' => $this->tagline,
            'title' => $this->title,
            'buttonText' => $this->buttonText,
            'posts' => $this->posts,
        ]);
    }
}
