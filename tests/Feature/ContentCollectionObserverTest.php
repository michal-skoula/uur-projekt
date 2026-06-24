<?php

use App\Models\Page;
use App\Settings\NavMenuSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('deleted', function (): void {
    it('removes a deleted item from a nav menu that contains it', function (): void {
        $page = Page::factory()->create();

        $settings = app(NavMenuSettings::class);
        $settings->structure = [
            ['collection' => 'pages', 'id' => $page->id, 'children' => []],
        ];
        $settings->save();

        $page->delete();

        expect(app(NavMenuSettings::class)->structure)->toBe([]);
    });

    it('removes a deleted item nested deep in the menu tree', function (): void {
        $parent = Page::factory()->create();
        $child = Page::factory()->create();

        $settings = app(NavMenuSettings::class);
        $settings->structure = [
            ['collection' => 'pages', 'id' => $parent->id, 'children' => [
                ['collection' => 'pages', 'id' => $child->id, 'children' => []],
            ]],
        ];
        $settings->save();

        $child->delete();

        expect(app(NavMenuSettings::class)->structure)->toBe([
            ['collection' => 'pages', 'id' => $parent->id, 'children' => []],
        ]);
    });

    it('leaves the menu untouched when the deleted item is not in it', function (): void {
        $inMenu = Page::factory()->create();
        $notInMenu = Page::factory()->create();

        $structure = [
            ['collection' => 'pages', 'id' => $inMenu->id, 'children' => []],
        ];

        $settings = app(NavMenuSettings::class);
        $settings->structure = $structure;
        $settings->save();

        $notInMenu->delete();

        expect(app(NavMenuSettings::class)->structure)->toBe($structure);
    });

    it('does nothing when no menu settings are configured', function (): void {
        config(['settings.menu_configuration_settings' => []]);

        $page = Page::factory()->create();

        $page->delete();

        expect($page->trashed())->toBeTrue();
    });
});
