<?php

use App\Models\Page;
use App\Settings\NavMenuSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

describe('save', function (): void {
    it('persists the structure submitted from the frontend', function (): void {
        $parent = Page::factory()->create();
        $child = Page::factory()->create();

        $structure = [
            ['collection' => 'pages', 'id' => $parent->id, 'children' => [
                ['collection' => 'pages', 'id' => $child->id, 'children' => []],
            ]],
        ];

        Livewire::test('page-builder.builder')
            ->call('save', $structure)
            ->assertHasNoErrors();

        expect(app(NavMenuSettings::class)->structure)->toBe($structure);
    });

    it('reflects the submitted structure on the component itself', function (): void {
        $page = Page::factory()->create();

        $structure = [
            ['collection' => 'pages', 'id' => $page->id, 'children' => []],
        ];

        Livewire::test('page-builder.builder')
            ->call('save', $structure)
            ->assertSet('menuStructure', $structure);
    });

    it('drops items that no longer exist in the database', function (): void {
        config(['content-collections.disabled' => ['pages']]);

        $existing = Page::factory()->create();
        $missingId = $existing->id + 999;

        $structure = [
            ['collection' => 'pages', 'id' => $existing->id, 'children' => []],
            ['collection' => 'pages', 'id' => $missingId, 'children' => []],
        ];

        Livewire::test('page-builder.builder')
            ->call('save', $structure)
            ->assertHasNoErrors();

        expect(app(NavMenuSettings::class)->structure)->toBe([
            ['collection' => 'pages', 'id' => $existing->id, 'children' => []],
        ]);
    });

    it('does not error when a disabled collection maps to an invalid class', function (): void {
        config([
            'content-collections.collections' => ['widgets' => stdClass::class] + config('content-collections.collections'),
            'content-collections.disabled' => ['widgets'],
        ]);

        $structure = [
            ['collection' => 'widgets', 'id' => 1, 'children' => []],
        ];

        Livewire::test('page-builder.builder')
            ->call('save', $structure)
            ->assertHasNoErrors();

        expect(app(NavMenuSettings::class)->structure)->toBe($structure);
    });
});
