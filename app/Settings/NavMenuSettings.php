<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class NavMenuSettings extends Settings
{
    public array $structure;

    public static function group(): string
    {
        return 'nav_menu';
    }
}
