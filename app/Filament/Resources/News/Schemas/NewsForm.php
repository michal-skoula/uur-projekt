<?php

namespace App\Filament\Resources\News\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('title')
                            ->label('Nadpis')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, string $state, callable $set) => $operation === 'create'
                                ? $set('slug', Str::slug($state))
                                : null),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                    ]),

                Textarea::make('excerpt')
                    ->label('Perex')
                    ->rows(3)
                    ->columnSpanFull(),

                RichEditor::make('content')
                    ->label('Obsah')
                    ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList', 'h2', 'h3', 'blockquote'])
                    ->columnSpanFull(),

                FileUpload::make('thumbnail')
                    ->label('Náhledový obrázek')
                    ->image()
                    ->visibility('public')
                    ->directory('news'),

                TextInput::make('author')
                    ->label('Autor'),

                DateTimePicker::make('published_at')
                    ->label('Datum publikace'),
            ]);
    }
}
