<?php

namespace App\Filament\PageBuilder\Sections;

use App\Concerns\BuildsSectionSchema;
use App\Contracts\SectionSchema;
use App\Filament\Components\ButtonInput;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
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
        return __('sections/about.label');
    }

    public static function getIcon(): Heroicon
    {
        return Heroicon::UserGroup;
    }

    public static function getSchema(): array
    {
        return [
            Section::make(__('sections/about.section_content'))
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('tagline')
                        ->label(__('sections/about.tagline'))
                        ->required(),

                    TextInput::make('title')
                        ->label(__('sections/about.title'))
                        ->required(),

                    RichEditor::make('description')
                        ->label(__('sections/about.description'))
                        ->columnSpanFull()
                        ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList'])
                        ->required(),

                    TextInput::make('bubble')
                        ->label(__('sections/about.bubble'))
                        ->helperText(__('sections/about.bubble_help'))
                        ->columnSpanFull(),
                ]),

            Section::make(__('sections/about.section_buttons'))
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    ButtonInput::make('button_primary', __('sections/about.button_primary')),
                    ButtonInput::make('button_secondary', __('sections/about.button_secondary')),
                ]),

            Section::make(__('sections/about.section_gallery'))
                ->columnSpanFull()
                ->schema([
                    CuratorPicker::make('gallery')
                        ->label(__('sections/about.gallery'))
                        ->helperText(__('sections/about.gallery_help'))
                        ->multiple()
                        ->columnSpanFull(),
                ]),
        ];
    }
}
