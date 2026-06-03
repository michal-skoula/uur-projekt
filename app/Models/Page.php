<?php

namespace App\Models;

use App\Concerns\ContentCollectionModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property ?int $parent_id
 * @property string $title
 * @property ?string $slug
 * @property list<array{type: string, data: array<string, mixed>}> $content
 * @property bool $is_published
 */
// todo: install laravel-ide-helper and give AI access to it, and add it into the coding pipeline

class Page extends ContentCollectionModel
{
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

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
        // todo: add observer to calculate this
        return 'WORK_IN_PROGRESS';
    }
}
