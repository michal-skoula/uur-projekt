<?php

namespace App\Contracts;

use App\Models\Analytics;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

abstract class ContentCollectionModel extends Model implements ContentCollectionItem
{
    //    abstract public static function getIndexPage():
    abstract public function getPermalink(): string;

    /** @return MorphMany<Analytics, $this> */
    public function analytics(): MorphMany
    {
        return $this->morphMany(Analytics::class, 'subject');
    }
}
