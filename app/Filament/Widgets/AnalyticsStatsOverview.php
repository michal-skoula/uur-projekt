<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsStatsOverview extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    /**
     * Collection slug to scope the stats to. Injected by the host list page via
     * getWidgetData(); null shows site-wide stats.
     */
    public ?string $collectionSlug = null;

    protected function getStats(): array
    {
        $analytics = new AnalyticsService($this->collectionSlug);
        $dailyVisits = $analytics->dailyVisits(30);
        $mostVisited = $analytics->mostVisitedSubject();

        $mostVisitedName = $mostVisited?->getName() ?? __('analytics.stats.none');

        return [
            Stat::make(__('analytics.stats.visitors_30d'), number_format($analytics->visitsInLastDays(30)))
                ->icon(Heroicon::Eye),
            //                ->chart(array_map(floatval(...), array_values($dailyVisits)))
            //                ->chartColor('primary'),

            Stat::make(
                __('analytics.stats.most_visited'),
                strlen($mostVisitedName) > 20
                    ? trim(substr($mostVisitedName, offset: 0, length: 20)).'...'
                    : $mostVisitedName
            )->icon(Heroicon::Trophy),

            Stat::make(__('analytics.stats.total_pages'), number_format($analytics->totalContentItems()))
                ->icon(Heroicon::DocumentText),
        ];
    }
}
