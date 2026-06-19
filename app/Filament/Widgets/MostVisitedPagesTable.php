<?php

namespace App\Filament\Widgets;

use App\Contracts\ContentCollectionModel;
use App\Models\Analytics;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class MostVisitedPagesTable extends TableWidget
{
    protected static bool $isDiscovered = false;

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('analytics.table.heading'))
            ->query(fn (): Builder => Analytics::query()
                ->whereNotNull('subject_id')
                ->selectRaw('MIN(id) as id, subject_type, subject_id, COUNT(*) as visitors')
                ->groupBy('subject_type', 'subject_id')
                ->orderByDesc('visitors'))
            ->defaultKeySort(false)
            ->columns([
                TextColumn::make('name')
                    ->label(__('analytics.table.name'))
                    ->state(fn (Analytics $record): string => $record->subject instanceof ContentCollectionModel
                        ? $record->subject->getName()
                        : (string) __('analytics.stats.none')),

                TextColumn::make('subject_collection')
                    ->label(__('analytics.table.collection'))
                    ->badge()
                    ->state(fn (Analytics $record): ?string => $record->subject instanceof ContentCollectionModel
                        ? (string) __('analytics.collections.'.$record->subject->getCollectionSlug())
                        : null),

                TextColumn::make('visitors')
                    ->label(__('analytics.table.visitors'))
                    ->icon(Heroicon::Eye)
                    ->numeric(),
            ])
            ->paginated(false);
    }
}
