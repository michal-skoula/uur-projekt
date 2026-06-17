<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Helpers\CmsSectionsHelper;
use App\Models\Page;
use App\Rules\UniqueHomepageSlug;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('resources/page.sections.configuration'))
                    ->aside()
                    ->columns(1)
                    ->schema([
                        TextInput::make('title')
                            ->label(__('resources/page.fields.title'))
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, ?string $state, callable $set) => $operation === 'create'
                                ? $set('slug', Str::slug($state ?? ''))
                                : null)
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->default('')
                            ->label(__('resources/page.fields.slug'))
                            ->placeholder(__('resources/page.fields.slug_placeholder'))
                            ->helperText(__('resources/page.fields.slug_helper'))
                            ->unique(ignoreRecord: true)
                            ->rule(function (Field $component): UniqueHomepageSlug {
                                $record = $component->getRecord();

                                return new UniqueHomepageSlug($record instanceof Page ? $record->id : null);
                            })
                            ->regex('/^([a-z0-9]+(?:-[a-z0-9]+)*)?$/')
                            ->maxLength(255)
                            ->mutateStateForValidationUsing(fn (?string $state): string => $state ?? '')
                            ->dehydrateStateUsing(fn (?string $state): string => $state ?? '')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                if ($state === null || $state === '') {
                                    $set('parent_id', null);
                                }
                            }),
                        Select::make('parent_id')
                            ->label(__('resources/page.fields.parent'))
                            ->relationship('parent', 'title')
                            ->placeholder(__('resources/page.fields.parent_placeholder'))
                            ->helperText(__('resources/page.fields.parent_helper'))
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get): bool => filled($get('slug'))),
                        Toggle::make('is_published')
                            ->label(__('resources/page.fields.is_published'))
                            ->inline(),
                    ]),
                Builder::make('content')
                    ->blocks(CmsSectionsHelper::blocks())
                    ->collapsible()
                    ->collapsed()
                    ->blockNumbers(false)
                    ->addActionAlignment(Alignment::Start)
                    ->columnSpanFull(),
            ]);
    }
}
