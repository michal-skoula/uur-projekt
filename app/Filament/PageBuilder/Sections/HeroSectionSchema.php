<?php

namespace App\Filament\PageBuilder\Sections;

use App\Concerns\BuildsSectionSchema;
use App\Contracts\SectionSchema;
use App\Filament\Components\ButtonInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

final class HeroSectionSchema implements SectionSchema
{
    use BuildsSectionSchema;

    public static function getSlug(): string
    {
        return 'hero';
    }

    public static function getLabel(): string
    {
        return __('sections/hero.label');
    }

    public static function getIcon(): Heroicon
    {
        return Heroicon::PlayCircle;
    }

    public static function getSchema(): array
    {
        return [
            Section::make(__('sections/hero.section_content'))
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label(__('sections/hero.title'))
                        ->required(),

                    TextInput::make('description')
                        ->label(__('sections/hero.description'))
                        ->required(),

                    TextInput::make('bubble')
                        ->label(__('sections/hero.bubble'))
                        ->helperText(__('sections/hero.bubble_help')),
                ]),

            Section::make(__('sections/hero.section_background'))
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    FileUpload::make('background.img')
                        ->label(__('sections/hero.background_img'))
                        ->image()
                        ->visibility('public')
                        ->required(),

                    FileUpload::make('background.video')
                        ->label(__('sections/hero.background_video'))
                        ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                        ->visibility('public'),
                ]),

            Section::make(__('sections/hero.section_buttons'))
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    ButtonInput::make('button_primary', __('sections/hero.button_primary')),
                    ButtonInput::make('button_secondary', __('sections/hero.button_secondary')),
                ]),
        ];
    }
}
