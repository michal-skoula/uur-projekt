<?php

namespace App\Concerns;

use App\Contracts\ContentCollectionItem;
use App\Services\PageBuilderService;

trait PerformsNavMenuStructureOperations
{
    /**
     * @var list<array{collection: string, id: int, children: array<int, mixed>}>
     */
    public array $structure;

    abstract public function save();

    public function containsItem(ContentCollectionItem $page): bool
    {
        foreach ($this->structure as $node) {
            if ($this->recursivelyCheckForPage($node, $page)) {
                return true;
            }
        }

        return false;
    }

    public function removeItem(ContentCollectionItem $page): bool
    {
        $lists = PageBuilderService::buildCollectionListsFromTree($this->structure);

        /** @var list<array{string, int}> $toRemove */
        $toRemove = [];

        foreach ($lists as $collectionSlug => $ids) {
            foreach ($ids as $id) {
                if ($id !== $page->getIdentifier() || $collectionSlug !== $page->getCollectionSlug()) {
                    continue;
                }

                $toRemove[] = [$collectionSlug, $id];
            }
        }

        $this->structure = PageBuilderService::removeInvalidItemsFromTree($this->structure, $toRemove);
        static::save();

        return empty($toRemove);
    }

    /**
     * @param  array  $node  list<array{collection: string, id: int, children: array<int, mixed>}>
     */
    private function recursivelyCheckForPage(array $node, ContentCollectionItem $page): bool
    {
        if ($node['collection'] === $page->getCollectionSlug() && (int) $node['id'] === $page->getIdentifier()) {
            return true;
        }

        foreach ($node['children'] as $child) {
            $result = $this->recursivelyCheckForPage($child, $page);
            if ($result === true) {
                return true;
            }
        }

        return false;
    }
}
