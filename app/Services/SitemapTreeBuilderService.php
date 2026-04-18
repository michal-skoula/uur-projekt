<?php

namespace App\Services;

use App\Models\Page;
use Hoa\Compiler\Llk\TreeNode;
use Illuminate\Support\Collection;

class SitemapTreeBuilderService
{
    public static function handle(): void
    {
        dump(self::buildFromCollection(Page::all()));
//        dump(self::updateFromTree(self::$testArray));
    }

    /**
     * Takes a simplified tree of page IDs and updates the pages to reflect it.
     *
     * @phpstan-type TreeNode array{id: int, parent_id: int|null, children: list<Page>}>
     *
     * @param list<TreeNode> $tree
     * @return array<int, int> Map of changes `[page_id => new_parent_id]`
     */
    public static function updateFromTree(array $tree): array
    {
        $changes = [];
        self::traverse($tree, $changes);

        return $changes;
    }

    /**
     * @phpstan-type TreeNode array{id: int, children: list<Page>}
     *
     * @param list<TreeNode> $schema
     */
    private static array $testArray = [
        [
            'id' => 1,
            'children' => [
                [
                    'id' => 2,
                    'children' => [],
                ],
                [
                    'id' => 3,
                    'children' => [
                        [
                            'id' => 8,
                            'children' => [],
                        ],
                        [
                            'id' => 9,
                            'children' => [],
                        ],
                    ],
                ],
                [
                    'id' => 32,
                    'children' => [
                        [
                            'id' => 99,
                            'children' => [],
                        ],
                    ],
                ],
            ],
        ],
        [
            'id' => 567,
            'children' => [
                [
                    'id' => 543,
                    'children' => [],
                ],
                [
                    'id' => 88,
                    'children' => [
                        [
                            'id' => 999,
                            'children' => [],
                        ],
                    ],
                ],
            ],
        ],
    ];

    /**
     * Traverses the
     *
     * @phpstan-type TreeNode array{id: int, children: list<TreeNode>}
     *
     * @param list<TreeNode> $schema Subset of the tree to traverse
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
     * Unordered forrest of trees builder
     *
     * @phpstan-type PageModelTreeNode array{page: Page, children: array<int, PageModelTreeNode>}
     *
     * @param Collection<Page> $pages
     * @return array
     */
    private static function buildFromCollection(Collection $pages): array
    {
        /** @var array<int, PageModelTreeNode> $roots */
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
