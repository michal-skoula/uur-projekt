<?php

namespace App\Contracts;

use App\Enums\ContentStatus;
use App\Models\Analytics;
use App\Observers\ContentCollectionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property ContentStatus $status
 */
#[ObservedBy(ContentCollectionObserver::class)]
abstract class ContentCollectionModel extends Model implements ContentCollectionItem
{
    //    abstract public static function getIndexPage():
    abstract public function getPermalink(): string;

    public function getStatus(): ContentStatus
    {
        return $this->status;
    }

    /**
     * Limits the query to publicly visible (published) items.
     *
     * @param  Builder<static>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', ContentStatus::PUBLISHED);
    }

    /** @return MorphMany<Analytics, $this> */
    public function analytics(): MorphMany
    {
        return $this->morphMany(Analytics::class, 'subject');
    }
}
