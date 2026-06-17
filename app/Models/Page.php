<?php

namespace App\Models;

use App\Contracts\ContentCollectionModel;
use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property ?int $parent_id
 * @property string $title
 * @property string $slug
 * @property list<array{type: string, data: array<string, mixed>}> $content
 * @property bool $is_published
 */
class Page extends ContentCollectionModel
{
    /** @use HasFactory<PageFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_published',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content' => 'array',
            'is_published' => 'boolean',
        ];
    }

    /** @return BelongsTo<Page, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** @return HasMany<Page, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function canView(?User $user): bool
    {
        return true; // todo: implement access control for admins
    }

    public function getName(): string
    {
        return $this->title;
    }

    public function getIdentifier(): int
    {
        return $this->id;
    }

    public function getCollectionSlug(): string
    {
        return 'pages';
    }

    public function getPermalink(): string
    {
        return '/'.$this->getPathSegments()->implode('/');
    }

    public function getAbsoluteUrl(): string
    {
        return url($this->getPermalink());
    }

    /**
     * Builds the slug path by walking up the parent chain. Empty slugs
     * (the homepage) contribute no segment.
     *
     * @return Collection<int, string>
     */
    private function getPathSegments(): Collection
    {
        $segments = collect();

        for ($page = $this; $page !== null; $page = $page->parent) {
            if ($page->slug !== '') {
                $segments->prepend($page->slug);
            }
        }

        return $segments;
    }
}
