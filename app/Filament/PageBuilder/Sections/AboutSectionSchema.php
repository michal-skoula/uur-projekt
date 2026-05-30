<?php

namespace App\Filament\PageBuilder\Sections;

use App\Concerns\BuildsSectionSchema;
use App\Contracts\SectionSchema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

final class AboutSectionSchema implements SectionSchema
{
    use BuildsSectionSchema;

    public static function getSlug(): string
    {
        return 'about';
    }

    public static function getLabel(): string
    {
        return __('section-about.label');
    }

    public static function getIcon(): Heroicon
    {
        return Heroicon::UserGroup;
    }

    public static function getSchema(): array
    {
        return [
            Section::make(__('section-about.section_content'))
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('tagline')
                        ->label(__('section-about.tagline'))
                        ->required(),

                    TextInput::make('title')
                        ->label(__('section-about.title'))
                        ->required(),

                    RichEditor::make('description')
                        ->label(__('section-about.description'))
                        ->columnSpanFull()
                        ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                        ->required(),

                    TextInput::make('bubble')
                        ->label(__('section-about.bubble'))
                        ->helperText(__('section-about.bubble_help'))
                        ->columnSpanFull(),
                ]),

            Section::make(__('section-about.section_buttons'))
                ->columnSpanFull()
                ->schema([
                    Grid::make(2)
                        ->columnSpanFull()
                        ->schema([
                            TextInput::make('button_primary.text')
                                ->label(__('section-about.button_primary_text'))
                                ->required(),

                            TextInput::make('button_primary.url')
                                ->label(__('section-about.button_primary_url'))
                                ->url()
                                ->required(),

                            TextInput::make('button_secondary.text')
                                ->label(__('section-about.button_secondary_text'))
                                ->required(),

                            TextInput::make('button_secondary.url')
                                ->label(__('section-about.button_secondary_url'))
                                ->url()
                                ->required(),
                        ]),
                ]),

            Section::make(__('section-about.section_gallery'))
                ->columnSpanFull()
                ->schema([
                    FileUpload::make('gallery')
                        ->label(__('section-about.gallery'))
                        ->helperText(__('section-about.gallery_help'))
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->visibility('public')
                        ->columnSpanFull(),
                ]),
        ];
    }
}
