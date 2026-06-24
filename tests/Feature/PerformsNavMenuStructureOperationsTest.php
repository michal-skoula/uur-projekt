<?php

use App\Concerns\PerformsNavMenuStructureOperations;
use App\Models\Page;

/**
 * Minimal concrete host exposing the trait's behaviour for testing in isolation.
 */
class NavMenuStructureHost
{
    use PerformsNavMenuStructureOperations;

    public function save(): self
    {
        return $this;
    }
}

/**
 * @param  list<array{collection: string, id: int, children: array<int, mixed>}>  $structure
 */
function navMenuWithStructure(array $structure): NavMenuStructureHost
{
    $host = new NavMenuStructureHost;
    $host->structure = $structure;

    return $host;
}

function pageWithId(int $id): Page
{
    return (new Page)->forceFill(['id' => $id]);
}

describe('containsPage', function (): void {
    it('returns true when the page is a top-level node', function (): void {
        $menu = navMenuWithStructure([
            ['collection' => 'pages', 'id' => 1, 'children' => []],
        ]);

        expect($menu->containsItem(pageWithId(1)))->toBeTrue();
    });

    it('returns true when the page is nested deep within children', function (): void {
        $menu = navMenuWithStructure([
            ['collection' => 'pages', 'id' => 1, 'children' => [
                ['collection' => 'pages', 'id' => 2, 'children' => [
                    ['collection' => 'pages', 'id' => 3, 'children' => []],
                ]],
            ]],
        ]);

        expect($menu->containsItem(pageWithId(3)))->toBeTrue();
    });

    it('finds a page within a later sibling subtree', function (): void {
        $menu = navMenuWithStructure([
            ['collection' => 'pages', 'id' => 1, 'children' => []],
            ['collection' => 'pages', 'id' => 2, 'children' => [
                ['collection' => 'pages', 'id' => 3, 'children' => []],
            ]],
        ]);

        expect($menu->containsItem(pageWithId(3)))->toBeTrue();
    });

    it('returns false when the page is absent from the structure', function (): void {
        $menu = navMenuWithStructure([
            ['collection' => 'pages', 'id' => 1, 'children' => []],
            ['collection' => 'pages', 'id' => 2, 'children' => []],
        ]);

        expect($menu->containsItem(pageWithId(3)))->toBeFalse();
    });

    it('returns false for an empty structure', function (): void {
        $menu = navMenuWithStructure([]);

        expect($menu->containsItem(pageWithId(1)))->toBeFalse();
    });

    it('returns false when the id matches but the collection differs', function (): void {
        $menu = navMenuWithStructure([
            ['collection' => 'news', 'id' => 1, 'children' => []],
        ]);

        expect($menu->containsItem(pageWithId(1)))->toBeFalse();
    });

    it('returns false when the collection matches but the id differs', function (): void {
        $menu = navMenuWithStructure([
            ['collection' => 'pages', 'id' => 2, 'children' => []],
        ]);

        expect($menu->containsItem(pageWithId(1)))->toBeFalse();
    });
});
