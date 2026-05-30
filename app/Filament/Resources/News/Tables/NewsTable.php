<?php

namespace App\Filament\Resources\News\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('')
                    ->width(64)
                    ->height(40)
                    ->defaultImageUrl(null),

                TextColumn::make('title')
                    ->label('Nadpis')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('author')
                    ->label('Autor')
                    ->searchable(),

                TextColumn::make('published_at')
                    ->label('Publikováno')
                    ->date('d.m.Y')
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
