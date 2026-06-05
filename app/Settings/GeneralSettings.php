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

    public function getFaviconForDarkMode(): ?string
    {
        return $this->faviconLight;
    }

    public function getFaviconForLightMode(): ?string
    {
        return $this->faviconDark;
    }

    public static function group(): string
    {
        return 'general';
    }
}
