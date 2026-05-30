<?php

namespace App\Filament\PageBuilder\Sections;

use App\Concerns\BuildsSectionSchema;
use App\Contracts\SectionSchema;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

final class DanceSectionSchema implements SectionSchema
{
    use BuildsSectionSchema;

    public static function getSlug(): string
    {
        return 'dance';
    }

    public static function getLabel(): string
    {
        return __('section-dance.label');
    }

    public static function getIcon(): Heroicon
    {
        return Heroicon::MusicalNote;
    }

    public static function getSchema(): array
    {
        return [
            Section::make(__('section-dance.section_content'))
                ->columnSpanFull()
                ->schema([
                    TextInput::make('heading')
                        ->label(__('section-dance.heading'))
                        ->required(),

                    TextInput::make('motto')
                        ->label(__('section-dance.motto'))
                        ->helperText(__('section-dance.motto_help'))
                        ->required(),
                ]),

            Section::make(__('section-dance.section_styles'))
                ->columnSpanFull()
                ->schema([
                    Repeater::make('dance_styles')
                        ->label(__('section-dance.dance_styles'))
                        ->simple(
                            TextInput::make('label')
                                ->label(__('section-dance.style_label'))
                                ->required(),
                        )
                        ->reorderable()
                        ->columnSpanFull(),
                ]),

            Section::make(__('section-dance.section_text'))
                ->columnSpanFull()
                ->schema([
                    Grid::make(2)
                        ->columnSpanFull()
                        ->schema([
                            RichEditor::make('text_left')
                                ->label(__('section-dance.text_left'))
                                ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList', 'h3'])
                                ->required(),

                            RichEditor::make('text_right')
                                ->label(__('section-dance.text_right'))
                                ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList', 'h3'])
                                ->required(),
                        ]),
                ]),
        ];
    }
}
