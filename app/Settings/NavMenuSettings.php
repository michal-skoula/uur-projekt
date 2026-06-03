<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class NavMenuSettings extends Settings
{
    /**
     * Shape: id, slug, children
     */
    public array $structure;

    public static function group(): string
    {
        return 'nav_menu';
    }
}
