<?php

use App\Enums\ContentStatus;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

test('list page loads', function (): void {
    Page::factory()->count(3)->create();

    Livewire::test(ListPages::class)->assertOk();
});

test('can create a page with a slug', function (): void {
    Livewire::test(CreatePage::class)
        ->fillForm([
            'title' => 'About Us',
            'slug' => 'about-us',
            'status' => ContentStatus::PUBLISHED->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    expect(Page::where('slug', 'about-us')->exists())->toBeTrue();
});

test('can create a homepage with empty slug', function (): void {
    Livewire::test(CreatePage::class)
        ->fillForm([
            'title' => 'Home',
            'slug' => '',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    expect(Page::where('slug', '')->exists())->toBeTrue();
});

test('rejects a second page with empty slug', function (): void {
    Page::factory()->create(['slug' => '']);

    Livewire::test(CreatePage::class)
        ->fillForm([
            'title' => 'Another Home',
            'slug' => '',
        ])
        ->call('create')
        ->assertHasFormErrors(['slug']);

    expect(Page::where('slug', '')->count())->toBe(1);
});

test('rejects duplicate non-empty slug', function (): void {
    Page::factory()->create(['slug' => 'about-us']);

    Livewire::test(CreatePage::class)
        ->fillForm([
            'title' => 'About',
            'slug' => 'about-us',
        ])
        ->call('create')
        ->assertHasFormErrors(['slug' => 'unique']);
});

it('validates slug is kebab-case', function (string $slug): void {
    Livewire::test(CreatePage::class)
        ->fillForm(['title' => 'Test', 'slug' => $slug])
        ->call('create')
        ->assertHasFormErrors(['slug' => 'regex'])
        ->assertNotNotified();
})->with([
    'uppercase letters' => 'About-Us',
    'underscores' => 'about_us',
    'leading hyphen' => '-about',
    'trailing hyphen' => 'about-',
    'spaces' => 'about us',
]);

test('can edit a page', function (): void {
    $page = Page::factory()->create(['slug' => 'original-slug']);

    Livewire::test(EditPage::class, ['record' => $page->id])
        ->fillForm(['title' => 'Updated Title', 'slug' => 'updated-slug'])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    expect($page->fresh()->title)->toBe('Updated Title');
});

test('can create a page nested under a parent', function (): void {
    $parent = Page::factory()->create(['slug' => 'services']);

    Livewire::test(CreatePage::class)
        ->fillForm([
            'title' => 'Consulting',
            'slug' => 'consulting',
            'parent_id' => $parent->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    expect(Page::where('slug', 'consulting')->first()->parent_id)->toBe($parent->id);
});

test('absolute url joins the parent slug chain', function (): void {
    $parent = Page::factory()->create(['slug' => 'services']);
    $child = Page::factory()->create(['slug' => 'consulting', 'parent_id' => $parent->id]);

    expect($child->getAbsoluteUrl())->toBe(url('/services/consulting'));
});

test('homepage absolute url is the site root', function (): void {
    $homepage = Page::factory()->create(['slug' => '']);

    expect($homepage->getAbsoluteUrl())->toBe(url('/'));
});
