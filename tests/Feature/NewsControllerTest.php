<?php

use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the news index lists only published posts', function (): void {
    $published = News::factory()->published()->create();
    $draft = News::factory()->draft()->create();

    $this->get(route('news.index'))
        ->assertOk()
        ->assertViewHas('posts', function ($posts) use ($published, $draft): bool {
            $ids = $posts->pluck('id');

            return $ids->contains($published->id) && ! $ids->contains($draft->id);
        });
});

test('a published news post is visible', function (): void {
    $news = News::factory()->published()->create();

    $this->get(route('news.show', $news))->assertOk();
});

test('a draft news post returns 404 for guests', function (): void {
    $news = News::factory()->draft()->create();

    $this->get(route('news.show', $news))->assertNotFound();
});

test('an authenticated admin can preview a draft news post', function (): void {
    $news = News::factory()->draft()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('news.show', $news))
        ->assertOk();
});
