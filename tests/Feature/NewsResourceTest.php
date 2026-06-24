<?php

use App\Filament\Resources\News\Pages\CreateNews;
use App\Filament\Resources\News\Pages\EditNews;
use App\Filament\Resources\News\Pages\ListNews;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

test('list page loads', function (): void {
    News::factory()->count(3)->create();

    Livewire::test(ListNews::class)->assertOk();
});

test('create form loads with its sections', function (): void {
    Livewire::test(CreateNews::class)
        ->assertOk()
        ->assertFormFieldExists('title')
        ->assertFormFieldExists('slug')
        ->assertFormFieldExists('excerpt')
        ->assertFormFieldExists('content')
        ->assertFormFieldExists('author')
        ->assertFormFieldExists('status')
        ->assertFormFieldExists('published_at');
});

test('can create a news item', function (): void {
    Livewire::test(CreateNews::class)
        ->fillForm([
            'title' => 'Hello World',
            'slug' => 'hello-world',
            'excerpt' => 'A short summary.',
            'content' => '<p>Body</p>',
            'author' => 'Jane Doe',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    expect(News::where('slug', 'hello-world')->exists())->toBeTrue();
});

test('rejects a duplicate slug', function (): void {
    News::factory()->create(['slug' => 'hello-world']);

    Livewire::test(CreateNews::class)
        ->fillForm([
            'title' => 'Hello Again',
            'slug' => 'hello-world',
            'content' => '<p>Body</p>',
        ])
        ->call('create')
        ->assertHasFormErrors(['slug' => 'unique']);
});

test('can edit a news item', function (): void {
    $news = News::factory()->create(['title' => 'Original']);

    Livewire::test(EditNews::class, ['record' => $news->id])
        ->fillForm(['title' => 'Updated Title'])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    expect($news->fresh()->title)->toBe('Updated Title');
});
