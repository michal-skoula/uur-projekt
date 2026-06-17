<?php

use App\Models\Page;
use App\Services\PageBuilderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('buildAvailableGroups', function (): void {
    it('returns a group per enabled collection implementing ContentCollectionItem', function (): void {
        Page::factory()->count(2)->create();

        $titles = [];
        $groups = PageBuilderService::buildAvailableGroups($titles);

        expect($groups)->toHaveCount(1)
            ->and($groups[0]['label'])->toBe('Pages')
            ->and($groups[0]['items'])->toHaveCount(2);
    });

    it('populates the savePageTitlesTo map with id => title', function (): void {
        $page = Page::factory()->create(['title' => 'My Page']);

        $titles = [];
        PageBuilderService::buildAvailableGroups($titles);

        expect($titles)->toHaveKey($page->id, 'My Page');
    });

    it('populates savePageTitlesTo for every item across all groups', function (): void {
        $pages = Page::factory()->count(3)->create();

        $titles = [];
        PageBuilderService::buildAvailableGroups($titles);

        foreach ($pages as $page) {
            expect($titles)->toHaveKey($page->id, $page->title);
        }
    });

    it('skips disabled collections', function (): void {
        Page::factory()->count(2)->create();
        config(['content-collections.disabled' => ['pages']]);

        $titles = [];
        $groups = PageBuilderService::buildAvailableGroups($titles);

        expect($groups)->toBeEmpty()
            ->and($titles)->toBeEmpty();
    });

    it('skips collections that do not implement ContentCollectionItem', function (): void {
        $titles = [];
        $groups = PageBuilderService::buildAvailableGroups($titles);

        $labels = array_column($groups, 'label');
        expect($labels)->not->toContain('News');
    });

    it('returns an empty array when no collections are registered', function (): void {
        config(['content-collections.collections' => []]);

        $titles = [];
        $groups = PageBuilderService::buildAvailableGroups($titles);

        expect($groups)->toBeEmpty();
    });

    it('maps each item to id and title', function (): void {
        $page = Page::factory()->create(['title' => 'About Us']);

        $titles = [];
        $groups = PageBuilderService::buildAvailableGroups($titles);

        expect($groups[0]['items'][0])->toBe([
            'id' => $page->id,
            'title' => 'About Us',
        ]);
    });
});

describe('findIds', function (): void {
    it('returns ids from a flat list', function (): void {
        $items = [
            ['id' => 1, 'children' => []],
            ['id' => 2, 'children' => []],
            ['id' => 3, 'children' => []],
        ];

        expect(PageBuilderService::buildCollectionListsFromTree($items))->toBe([1, 2, 3]);
    });

    it('returns ids from a nested tree', function (): void {
        $items = [
            ['id' => 1, 'children' => [
                ['id' => 2, 'children' => [
                    ['id' => 3, 'children' => []],
                ]],
            ]],
        ];

        expect(PageBuilderService::buildCollectionListsFromTree($items))->toBe([1, 2, 3]);
    });

    it('collects ids from multiple branches', function (): void {
        $items = [
            ['id' => 1, 'children' => [
                ['id' => 2, 'children' => []],
                ['id' => 3, 'children' => []],
            ]],
            ['id' => 4, 'children' => [
                ['id' => 5, 'children' => []],
            ]],
        ];

        expect(PageBuilderService::buildCollectionListsFromTree($items))->toBe([1, 2, 3, 4, 5]);
    });

    it('returns an empty array for empty input', function (): void {
        expect(PageBuilderService::buildCollectionListsFromTree([]))->toBe([]);
    });

    it('casts ids to int', function (): void {
        $items = [['id' => '7', 'children' => []]];

        expect(PageBuilderService::buildCollectionListsFromTree($items))->toBe([7]);
    });
});
