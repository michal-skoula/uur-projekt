<?php

namespace App\Filament\PageBuilder\Sections;

use App\Concerns\BuildsSectionSchema;
use App\Contracts\SectionSchema;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

final class NewsSectionSchema implements SectionSchema
{
    use BuildsSectionSchema;

    public static function getSlug(): string
    {
        return 'news';
    }

    public static function getLabel(): string
    {
        return __('section-news.label');
    }

    public static function getIcon(): Heroicon
    {
        return Heroicon::Newspaper;
    }

    public static function getSchema(): array
    {
        return [
            TextInput::make('tagline')
                ->label(__('section-news.tagline'))
                ->columnSpanFull(),

            TextInput::make('title')
                ->label(__('section-news.title'))
                ->required()
                ->columnSpanFull(),

            TextInput::make('button_text')
                ->label(__('section-news.button_text'))
                ->required()
                ->columnSpanFull(),
        ];
    }
}
