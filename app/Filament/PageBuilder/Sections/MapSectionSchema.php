<?php

namespace App\Filament\PageBuilder\Sections;

use App\Concerns\BuildsSectionSchema;
use App\Contracts\SectionSchema;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

final class MapSectionSchema implements SectionSchema
{
    use BuildsSectionSchema;

    public static function getSlug(): string
    {
        return 'map';
    }

    public static function getLabel(): string
    {
        return __('section-map.label');
    }

    public static function getIcon(): Heroicon
    {
        return Heroicon::MapPin;
    }

    public static function getSchema(): array
    {
        return [
            TextInput::make('title')
                ->label(__('section-map.title'))
                ->required()
                ->columnSpanFull(),

            RichEditor::make('text')
                ->label(__('section-map.text'))
                ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                ->columnSpanFull(),

            TextInput::make('map_url')
                ->label(__('section-map.map_url'))
                ->helperText(__('section-map.map_url_help'))
                ->url()
                ->columnSpanFull(),
        ];
    }
}
