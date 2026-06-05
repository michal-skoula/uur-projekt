<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class NavMenuSettings extends Settings
{
    /**
     * @phpstan-var array<int, array{id: int, children: array<int, mixed>}>
     */
    public array $structure;

    public static function group(): string
    {
        return 'nav_menu';
    }
}
