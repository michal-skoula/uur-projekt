<?php

namespace App\Filament\PageBuilder\Sections;

use App\Concerns\BuildsSectionSchema;
use App\Contracts\SectionSchema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
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
        return 'Page header';
    }

    public static function getIcon(): Heroicon
    {
        return Heroicon::Sparkles;
    }

    public static function getSchema(): array
    {
        return [
            TextInput::make('heading')
                ->label('Heading (H1)')
                ->helperText('Use **text** for brand color, <<text>> for muted, *text* for italic.')
                ->columnSpanFull(),
            Textarea::make('description')
                ->label('Description')
                ->rows(3)
                ->columnSpanFull(),
            FileUpload::make('image')
                ->label('Hero Image')
                ->image()
                ->disk('public')
                ->visibility('public')
                ->directory('sections/hero')
                ->imageEditor()
                ->imageEditorAspectRatios([null, '16:9', '4:3', '1:1'])
                ->columnSpanFull(),
            Section::make('Call to action')
                ->columns(2)
                ->schema([
                    TextInput::make('cta.label')
                        ->label('Button label'),
                    TextInput::make('cta.url')
                        ->label('Button URL')
                        ->url(),
                ]),
        ];
    }
}
