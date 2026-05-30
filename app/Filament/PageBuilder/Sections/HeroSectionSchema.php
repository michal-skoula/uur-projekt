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
        return 'Úvodní sekce';
    }

    public static function getIcon(): Heroicon
    {
        return Heroicon::PlayCircle;
    }

    public static function getSchema(): array
    {
        return [
            Section::make('Obsah')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Nadpis')
                        ->required(),

                    TextInput::make('description')
                        ->label('Popis')
                        ->required(),

                    TextInput::make('bubble')
                        ->label('Bublina (volitelné)')
                        ->helperText('Krátký text zobrazený v kruhové bublině. Podporuje nové řádky pomocí \\n.'),
                ]),

            Section::make('Pozadí')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    FileUpload::make('background.img')
                        ->label('Obrázek pozadí')
                        ->image()
                        ->visibility('public')
                        ->required(),

                    FileUpload::make('background.video')
                        ->label('Video pozadí (volitelné)')
                        ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg'])
                        ->visibility('public'),
                ]),

            Section::make('Tlačítka')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    Grid::make(2)
                        ->columnSpanFull()
                        ->schema([
                            TextInput::make('button_primary.text')
                                ->label('Primární tlačítko – text')
                                ->required(),

                            TextInput::make('button_primary.url')
                                ->label('Primární tlačítko – URL')
                                ->url()
                                ->required(),

                            TextInput::make('button_secondary.text')
                                ->label('Sekundární tlačítko – text')
                                ->required(),

                            TextInput::make('button_secondary.url')
                                ->label('Sekundární tlačítko – URL')
                                ->url()
                                ->required(),
                        ]),
                ]),
        ];
    }
}
