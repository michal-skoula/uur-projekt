<?php

namespace App\Filament\PageBuilder\Sections;

use App\Concerns\BuildsSectionSchema;
use App\Contracts\SectionSchema;
use Filament\Forms\Components\FileUpload;
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
        return __('section-gallery.label');
    }

    public static function getIcon(): Heroicon
    {
        return Heroicon::Photo;
    }

    public static function getSchema(): array
    {
        return [
            Section::make(__('section-gallery.section_content'))
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('heading')
                        ->label(__('section-gallery.heading'))
                        ->required(),

                    TextInput::make('description')
                        ->label(__('section-gallery.description')),
                ]),

            Section::make(__('section-gallery.section_gallery'))
                ->columnSpanFull()
                ->schema([
                    FileUpload::make('gallery')
                        ->label(__('section-gallery.gallery'))
                        ->helperText(__('section-gallery.gallery_help'))
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->visibility('public')
                        ->columnSpanFull(),
                ]),
        ];
    }
}
