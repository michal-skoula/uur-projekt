<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
class Page extends Model
{
    use SoftDeletes, HasFactory;

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


    /**
     * Nests the current Page as a child of a parent Page.
     * @param Page|null $parent Parent model or null if top-level page.
     * @return void
     */
    public function nestUnder(self|null $parent): void
    {
        $this->parent_id = $parent->id;
    }
}
