<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Support\Collection;

class SitemapService
{
    /**
     * Builds the full Sitemap from database Page records adjacency list.
     *
     * @return array<int, array{page: Page, children: array}> Tree of pages and their children.
     */
    public function fullSitemap(): array
    {
        $pages = Page::all();

        return static::buildFromCollection($pages);
    }



    /**
     * Updates a page's hierarchy to be nested under a different parent.
     *
     * @param Page $page The page to change.
     * @param Page|null $parent Parent page. If `null`, becomes root level page.
     *
     * @return void Updates the record directly in database.
     */
    public function updatePage(Page $page, Page|null $parent): void
    {
        $page->parent_id = $parent?->id;
        $page->save();
    }

    /**
     * Traverses the
     *
     * @param list<array{id: int, children: mixed> $schema Subset of the tree to traverse
     * @param array<int, int> $changes Reference to a new map of changes `[page_id => parent_id]`
     * @param int|null $parentId ID of the current node's parent
     *
     * @return void Result is written to passed in variable `$changes`.
     */
    private static function traverse(array|null $schema, array &$changes = [], ?int $parentId = null): void
    {
        foreach ($schema as $item)
        {
            $pageId = $item['id'];
            $children = $item['children'];

            // Duplicate page, a sitemap can only have one page in one place
            if(in_array($pageId, array_keys($changes))) {
                throw new \RuntimeException("MR PRESIDENT WE'RE TIRED OF WINNING ON $pageId");
            }

            // Adds a new change
            $changes[$pageId] = $parentId;

            // End of tree
            if(! $children) {
                continue;
            }

            // Go 1 level deeper
            self::traverse($children, $changes, $pageId);
        }
    }

    /**
     * @param Collection<Page> $pages
     * @return array
     */
    private static function buildFromCollection(Collection $pages): array
    {
        /** @var array<int, array{page: Page, children: array}> $roots */
        $roots = [];

        /** @var array<int, array{page: Page, children: array}> $nodes */
        $nodes = [];

        foreach ($pages as $page) {
            $nodes[$page->id] = ['page' => $page, 'children' => []];
        }

        foreach ($nodes as $id => &$node)
        {
            $page = $node['page'];

            if($page->parent_id === null) {
                $roots[$id] = &$node;
            }
            else {
                $nodes[$page->parent_id]['children'][$id] = $node;
            }
        }

        return $roots;
    }
}
