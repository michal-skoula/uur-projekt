<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function getAbsoluteUrl(): string
    {
        return '/'.(string) $this->slug;
    }
}
