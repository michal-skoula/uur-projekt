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

        $byLabel = collect($groups)->keyBy('label');

        expect($groups)->toHaveCount(2)
            ->and($byLabel->keys()->all())->toEqualCanonicalizing(['Pages', 'News'])
            ->and($byLabel['Pages']['slug'])->toBe('pages')
            ->and($byLabel['Pages']['items'])->toHaveCount(2);
    });

    it('populates the savePageTitlesTo map with id => title', function (): void {
        $page = Page::factory()->create(['title' => 'My Page']);

        $titles = [];
        PageBuilderService::buildAvailableGroups($titles);

        expect($titles)->toHaveKey("pages:{$page->id}", 'My Page');
    });

    it('populates savePageTitlesTo for every item across all groups', function (): void {
        $pages = Page::factory()->count(3)->create();

        $titles = [];
        PageBuilderService::buildAvailableGroups($titles);

        foreach ($pages as $page) {
            expect($titles)->toHaveKey("pages:{$page->id}", $page->title);
        }
    });

    it('skips disabled collections', function (): void {
        Page::factory()->count(2)->create();
        config(['content-collections.disabled' => ['pages']]);

        $titles = [];
        $groups = PageBuilderService::buildAvailableGroups($titles);

        $labels = array_column($groups, 'label');

        expect($labels)->not->toContain('Pages')
            ->and($titles)->toBeEmpty();
    });

    it('skips collections that do not implement ContentCollectionItem', function (): void {
        config(['content-collections.collections' => [
            'pages' => Page::class,
            'invalid' => stdClass::class,
        ]]);

        $titles = [];
        $groups = PageBuilderService::buildAvailableGroups($titles);

        $labels = array_column($groups, 'label');

        expect($labels)->toContain('Pages')
            ->and($labels)->not->toContain('Invalid');
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
            'collection' => 'pages',
            'id' => $page->id,
            'title' => 'About Us',
        ]);
    });
});

describe('buildCollectionListsFromTree', function (): void {
    it('groups ids by collection from a flat list', function (): void {
        $items = [
            ['collection' => 'pages', 'id' => 1, 'children' => []],
            ['collection' => 'pages', 'id' => 2, 'children' => []],
            ['collection' => 'pages', 'id' => 3, 'children' => []],
        ];

        expect(PageBuilderService::buildCollectionListsFromTree($items))->toBe(['pages' => [1, 2, 3]]);
    });

    it('collects ids from a nested tree under the right collection', function (): void {
        $items = [
            ['collection' => 'pages', 'id' => 1, 'children' => [
                ['collection' => 'pages', 'id' => 2, 'children' => [
                    ['collection' => 'pages', 'id' => 3, 'children' => []],
                ]],
            ]],
        ];

        expect(PageBuilderService::buildCollectionListsFromTree($items))->toBe(['pages' => [1, 2, 3]]);
    });

    it('separates ids across multiple collections', function (): void {
        $items = [
            ['collection' => 'pages', 'id' => 1, 'children' => [
                ['collection' => 'news', 'id' => 10, 'children' => []],
            ]],
            ['collection' => 'news', 'id' => 11, 'children' => []],
            ['collection' => 'pages', 'id' => 2, 'children' => []],
        ];

        expect(PageBuilderService::buildCollectionListsFromTree($items))->toBe([
            'pages' => [1, 2],
            'news' => [10, 11],
        ]);
    });

    it('returns an empty array for empty input', function (): void {
        expect(PageBuilderService::buildCollectionListsFromTree([]))->toBe([]);
    });

    it('casts ids to int', function (): void {
        $items = [['collection' => 'pages', 'id' => '7', 'children' => []]];

        expect(PageBuilderService::buildCollectionListsFromTree($items))->toBe(['pages' => [7]]);
    });
});

describe('removeInvalidItemsFromTree', function (): void {
    it('removes a matching top-level item', function (): void {
        $tree = [
            ['collection' => 'pages', 'id' => 1, 'children' => []],
            ['collection' => 'pages', 'id' => 2, 'children' => []],
        ];

        expect(PageBuilderService::removeInvalidItemsFromTree($tree, [['pages', 1]]))->toBe([
            ['collection' => 'pages', 'id' => 2, 'children' => []],
        ]);
    });

    it('removes a nested item while keeping its parent and siblings', function (): void {
        $tree = [
            ['collection' => 'pages', 'id' => 1, 'children' => [
                ['collection' => 'pages', 'id' => 2, 'children' => []],
                ['collection' => 'news', 'id' => 5, 'children' => []],
            ]],
        ];

        expect(PageBuilderService::removeInvalidItemsFromTree($tree, [['pages', 2]]))->toBe([
            ['collection' => 'pages', 'id' => 1, 'children' => [
                ['collection' => 'news', 'id' => 5, 'children' => []],
            ]],
        ]);
    });

    it('removes the entire subtree when a parent matches', function (): void {
        $tree = [
            ['collection' => 'pages', 'id' => 1, 'children' => [
                ['collection' => 'pages', 'id' => 2, 'children' => [
                    ['collection' => 'pages', 'id' => 3, 'children' => []],
                ]],
            ]],
        ];

        expect(PageBuilderService::removeInvalidItemsFromTree($tree, [['pages', 1]]))->toBe([]);
    });

    it('only removes items matching both collection and id', function (): void {
        $tree = [
            ['collection' => 'pages', 'id' => 1, 'children' => []],
            ['collection' => 'news', 'id' => 1, 'children' => []],
        ];

        expect(PageBuilderService::removeInvalidItemsFromTree($tree, [['pages', 1]]))->toBe([
            ['collection' => 'news', 'id' => 1, 'children' => []],
        ]);
    });

    it('removes multiple items across different levels', function (): void {
        $tree = [
            ['collection' => 'pages', 'id' => 1, 'children' => [
                ['collection' => 'pages', 'id' => 2, 'children' => []],
            ]],
            ['collection' => 'news', 'id' => 10, 'children' => []],
        ];

        expect(PageBuilderService::removeInvalidItemsFromTree($tree, [['pages', 2], ['news', 10]]))->toBe([
            ['collection' => 'pages', 'id' => 1, 'children' => []],
        ]);
    });

    it('returns the tree unchanged when no items are passed', function (): void {
        $tree = [
            ['collection' => 'pages', 'id' => 1, 'children' => [
                ['collection' => 'pages', 'id' => 2, 'children' => []],
            ]],
        ];

        expect(PageBuilderService::removeInvalidItemsFromTree($tree, []))->toBe($tree);
    });

    it('returns an empty array for an empty tree', function (): void {
        expect(PageBuilderService::removeInvalidItemsFromTree([], [['pages', 1]]))->toBe([]);
    });
});
