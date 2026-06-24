<?php

namespace App\Filament\Resources\News\Schemas;

use App\Enums\ContentStatus;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('resources/news.sections.basic.title'))
                    ->aside()
                    ->description(__('resources/news.sections.basic.description'))
                    ->columns(1)
                    ->schema([
                        TextInput::make('title')
                            ->label(__('resources/news.fields.title'))
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, ?string $state, callable $set) => $operation === 'create'
                                ? $set('slug', Str::slug($state ?? ''))
                                : null),
                        TextInput::make('slug')
                            ->label(__('resources/news.fields.slug'))
                            ->helperText(__('resources/news.fields.slug_helper'))
                            ->required()
                            ->unique(ignoreRecord: true),
                        Textarea::make('excerpt')
                            ->columnSpanFull()
                            ->label(__('resources/news.fields.excerpt'))
                            ->helperText(__('resources/news.fields.excerpt_helper'))
                            ->rows(3),
                        CuratorPicker::make('thumbnail')
                            ->label(__('resources/news.fields.thumbnail'))
                            ->directory('news'),
                    ]),
                Section::make(__('resources/news.sections.publishing.title'))
                    ->aside()
                    ->description(__('resources/news.sections.publishing.description'))
                    ->columns(1)
                    ->schema([
                        Select::make('status')
                            ->label(__('resources/news.fields.status'))
                            ->options(ContentStatus::class)
                            ->default(ContentStatus::DRAFT)
                            ->selectablePlaceholder(false)
                            ->required(),
                        TextInput::make('author')
                            ->label(__('resources/news.fields.author')),
                        DateTimePicker::make('published_at')
                            ->label(__('resources/news.fields.published_at'))
                            ->helperText(__('resources/news.fields.published_at_helper')),
                    ]),
                RichEditor::make('content')
                    ->label(__('resources/news.fields.content'))
                    ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList', 'h2', 'h3', 'blockquote']),
            ]);
    }
}
