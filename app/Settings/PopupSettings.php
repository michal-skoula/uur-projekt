<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PopupSettings extends Settings
{
    public bool $enabled;

    public bool $stripeEnabled;

    public string $stripeText;

    /** @phpstan-var array{text: string, link: array{type: string, url: int|string|null}}|null */
    public ?array $stripeCta;

    public bool $popupEnabled;

    public ?string $popupImage;

    public ?string $popupHeading;

    public ?string $popupContent;

    /** @phpstan-var array{text: string, link: array{type: string, url: int|string|null}}|null */
    public ?array $popupCta;

    public static function group(): string
    {
        return 'popup';
    }
}
