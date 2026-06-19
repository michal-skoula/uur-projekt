<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Awcodes\Curator\Models\Media;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class AnalyticsOverview extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected function getStats(): array
    {
        $analytics = app(AnalyticsService::class);
        $mostVisited = $analytics->mostVisitedSubject();

        return [
            Stat::make(__('analytics.stats.total_visits'), number_format($analytics->totalVisits()))
                ->icon(Heroicon::Eye)
//                ->chart($analytics->dailyVisits(30))
//                ->chartColor('primary')
                ->description(__('analytics.stats.total_pages_previous_period', [
                    'num' => array_sum($analytics->dailyVisits(30)),
                ])),
            Stat::make(__('analytics.stats.most_visited'), $mostVisited?->getName() ?? __('analytics.stats.none'))
                ->icon(Heroicon::Trophy)
                ->description(__('analytics.stats.most_visited_number', [
                    'num' => $mostVisited->analytics()->count()]
                )),

            Stat::make(__('analytics.stats.total_pages'), number_format($analytics->totalContentItems()))
                ->icon(Heroicon::DocumentText),

            Stat::make(__('analytics.stats.total_files'), number_format(Media::query()->count()))
                ->description(__('analytics.stats.total_files_description', [
                    'size' => Number::fileSize((int) Media::query()->sum('size')),
                ]))
                ->icon(Heroicon::Folder),
        ];
    }
}
