<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $name;

    public string $description;

    public ?string $logo;

    public ?string $faviconLight;

    public ?string $faviconDark;


    public static function group(): string
    {
        return 'general';
    }
}
