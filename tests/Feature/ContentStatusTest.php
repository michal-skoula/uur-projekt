<?php

use App\Enums\ContentStatus;
use App\Models\News;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the published scope returns only published records', function (): void {
    Page::factory()->published()->create();
    Page::factory()->create(); // draft by default
    Page::factory()->disabled()->create();

    $published = Page::query()->published()->get();

    expect($published)->toHaveCount(1)
        ->and($published->first()->status)->toBe(ContentStatus::PUBLISHED);
});

test('status is cast to the ContentStatus enum and exposed via getStatus()', function (): void {
    $page = Page::factory()->disabled()->create()->fresh();

    expect($page->status)->toBe(ContentStatus::DISABLED)
        ->and($page->getStatus())->toBe(ContentStatus::DISABLED);
});

test('the published scope works across content collections', function (): void {
    News::factory()->published()->create();
    News::factory()->draft()->create();

    expect(News::query()->published()->count())->toBe(1);
});
