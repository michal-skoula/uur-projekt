<?php

namespace App\Filament\PageBuilder\Sections;

use App\Concerns\BuildsSectionSchema;
use App\Contracts\SectionSchema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
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
        return __('section-hero.label');
    }

    public static function getIcon(): Heroicon
    {
        return Heroicon::PlayCircle;
    }

    public static function getSchema(): array
    {
        return [
            Section::make(__('section-hero.section_content'))
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label(__('section-hero.title'))
                        ->required(),

                    TextInput::make('description')
                        ->label(__('section-hero.description'))
                        ->required(),

                    TextInput::make('bubble')
                        ->label(__('section-hero.bubble'))
                        ->helperText(__('section-hero.bubble_help')),
                ]),

            Section::make(__('section-hero.section_background'))
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    FileUpload::make('background.img')
                        ->label(__('section-hero.background_img'))
                        ->image()
                        ->visibility('public')
                        ->required(),

                    FileUpload::make('background.video')
                        ->label(__('section-hero.background_video'))
                        ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                        ->visibility('public'),
                ]),

            Section::make(__('section-hero.section_buttons'))
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    Grid::make(2)
                        ->columnSpanFull()
                        ->schema([
                            TextInput::make('button_primary.text')
                                ->label(__('section-hero.button_primary_text'))
                                ->required(),

                            TextInput::make('button_primary.url')
                                ->label(__('section-hero.button_primary_url'))
                                ->url()
                                ->required(),

                            TextInput::make('button_secondary.text')
                                ->label(__('section-hero.button_secondary_text'))
                                ->required(),

                            TextInput::make('button_secondary.url')
                                ->label(__('section-hero.button_secondary_url'))
                                ->url()
                                ->required(),
                        ]),
                ]),
        ];
    }
}
