<?php

namespace App\Contracts;

interface ConfiguresNavBuilder
{
    /**
     * @var list<array{collection: string, id: int, children: array<int, mixed>}>
     */
    public array $structure { get; set; }

    /**
     * Checks whether the tree contains the given page item.
     *
     * @param  ContentCollectionItem  $page  The page to look for.
     */
    public function containsItem(ContentCollectionItem $page): bool;

    /**
     * Removes any instance of the page item inside the $structure array.
     *
     * @param  ContentCollectionItem  $page  The page to be removed.
     * @return bool Determines whether at least one page was removed.
     */
    public function removeItem(ContentCollectionItem $page): bool;
}
