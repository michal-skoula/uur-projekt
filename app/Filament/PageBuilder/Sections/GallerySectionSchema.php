<?php

namespace App\Filament\PageBuilder\Sections;

use App\Concerns\BuildsSectionSchema;
use App\Contracts\SectionSchema;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

final class GallerySectionSchema implements SectionSchema
{
    use BuildsSectionSchema;

    public static function getSlug(): string
    {
        return 'gallery';
    }

    public static function getLabel(): string
    {
        return __('sections/gallery.label');
    }

    public static function getIcon(): Heroicon
    {
        return Heroicon::Photo;
    }

    public static function getSchema(): array
    {
        return [
            Section::make(__('sections/gallery.section_content'))
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('heading')
                        ->label(__('sections/gallery.heading'))
                        ->required(),

                    TextInput::make('description')
                        ->label(__('sections/gallery.description')),
                ]),

            Section::make(__('sections/gallery.section_gallery'))
                ->columnSpanFull()
                ->schema([
                    CuratorPicker::make('gallery')
                        ->label(__('sections/gallery.gallery'))
                        ->helperText(__('sections/gallery.gallery_help'))
                        ->multiple()
                        ->columnSpanFull(),
                ]),
        ];
    }
}
