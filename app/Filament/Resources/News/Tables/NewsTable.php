<?php

namespace App\Filament\Resources\News\Tables;

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                CuratorColumn::make('thumbnail')
                    ->label('Náhled')
                    ->imageSize(96)
                    ->defaultImageUrl(null),

                TextColumn::make('title')
                    ->label('Nadpis')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('author')
                    ->label('Autor')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Stav')
                    ->badge()
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label('Publikováno')
                    ->date('d.m.Y')
                    ->sortable(),

                TextColumn::make('analytics_count')
                    ->label(__('analytics.column.visitors'))
                    ->counts('analytics')
                    ->icon(Heroicon::Eye)
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Vytvořeno')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
