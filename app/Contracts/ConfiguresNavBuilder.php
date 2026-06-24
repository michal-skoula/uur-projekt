<?php

namespace App\Contracts;

interface ConfiguresNavBuilder
{
    /**
     * @var list<array{collection: string, id: int, children: array<int, mixed>}>
     */
    public array $structure { get; set; }

    /**
     * Checks whether the tree contains the given page.
     *
     * @param ContentCollectionModel $page The page to look for.
     * @return bool
     */
    public function containsPage(ContentCollectionModel $page): bool;

    /**
     * Removes any instance of the page inside the $structure array.
     *
     * @param ContentCollectionModel $page The page to be removed.
     * @return bool Determines whether at least one page was removed.
     */
    public function removePage(ContentCollectionModel $page): bool;
}
