<?php

namespace App\Contracts;

use Filament\Forms\Components\Builder\Block;
use Filament\Support\Icons\Heroicon;

interface SectionSchema
{
    public static function getSlug(): string;

    public static function getLabel(): string;

    public static function getIcon(): Heroicon;

    public static function make(): Block;
}
