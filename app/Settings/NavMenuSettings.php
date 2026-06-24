<?php

namespace App\Settings;

use App\Concerns\PerformsNavMenuStructureOperations;
use App\Contracts\ConfiguresNavBuilder;
use Spatie\LaravelSettings\Settings;

class NavMenuSettings extends Settings implements ConfiguresNavBuilder
{
    use PerformsNavMenuStructureOperations;

    /**
     * @phpstan-var list<array{collection: string, id: int, children: array<int, mixed>}>
     */
    public array $structure;

    /**
     * @phpstan-var array{text?: string, link?: array{type?: string, url?: string|int|null}}|null
     */
    public ?array $button_primary;

    /**
     * @phpstan-var array{text?: string, link?: array{type?: string, url?: string|int|null}}|null
     */
    public ?array $button_secondary;

    public static function group(): string
    {
        return 'nav_menu';
    }
}
