<?php

namespace App\Contracts;

use App\Enums\ContentStatus;

interface ContentCollectionItem
{
    /**
     * @return string Display name of the item.
     */
    public function getName(): string;

    /**
     * @return ContentStatus Publication status of the item.
     */
    public function getStatus(): ContentStatus;

    /**
     * @return string Parent collection this item belongs to.
     */
    public function getCollectionSlug(): string;

    /**
     * @return int Id of the item.
     */
    public function getIdentifier(): int;
}
