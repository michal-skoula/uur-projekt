<?php

namespace App\Concerns;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Builder\Block;
use Filament\Schemas\Components\Component;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

trait BuildsSectionSchema
{
    abstract public static function getSlug(): string;

    abstract public static function getLabel(): string;

    abstract public static function getIcon(): Heroicon;

    /** @return array<Component | Action | ActionGroup | string | Htmlable> */
    abstract public static function getSchema(): array;

    public static function make(): Block
    {
        return Block::make(static::getSlug())
            ->label(static::getLabel())
            ->icon(static::getIcon())
            ->schema(static::getSchema());
    }
}
