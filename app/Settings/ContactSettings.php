<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ContactSettings extends Settings
{
    /** @phpstan-var array<int, array{icon: string, name: string, url: string}> */
    public array $socials = [];

    /** @phpstan-var array<int, array{heading: string, items: array<int, array{title: string, url: string}>}> */
    public array $footerNav = [];

    /** @phpstan-var array{text?: string, link?: array{type?: string, url?: string}} */
    public array $errorReportButton = [];

    public static function group(): string
    {
        return 'contact';
    }
}
