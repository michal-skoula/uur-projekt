<?php

namespace App\Contracts;

interface ContentCollectionItem
{
    /**
     * @return string Display name of the item.
     */
    public function getName(): string;

    /**
     * @return string Parent collection this item belongs to.
     */
    public function getCollectionSlug(): string;

    /**
     * @return int Id of the item.
     */
    public function getIdentifier(): int;
}
