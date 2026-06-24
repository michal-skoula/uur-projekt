<?php

namespace App\Filament\Resources\Pages\Tables;

use App\Enums\ContentStatus;
use App\Models\Page;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('resources/page.fields.title'))
                    ->prefix(fn (Page $record): string => $record->slug === '' ? __('resources/page.home_prefix') : '')
                    ->searchable(),
                TextColumn::make('slug')
                    ->label(__('resources/page.fields.url'))
                    ->color('gray')
                    ->formatStateUsing(fn (Page $record): string => $record->getPermalink())
                    ->default('/')
                    ->searchable(),
                SelectColumn::make('status')
                    ->label(__('resources/page.fields.status'))
                    ->options(ContentStatus::class)
                    ->selectablePlaceholder(false),
                TextColumn::make('analytics_count')
                    ->label(__('analytics.column.visitors'))
                    ->counts('analytics')
                    ->icon(Heroicon::Eye)
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('view-live-site')
                    ->label(__('resources/page.actions.visit'))
                    ->color('gray')
                    ->icon(Heroicon::ArrowUpRight)
                    ->url(fn (Page $record): string => $record->getAbsoluteUrl())
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
