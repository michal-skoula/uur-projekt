<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TotalVisitsChart extends ChartWidget
{
    protected static bool $isDiscovered = false;

    public function getHeading(): string
    {
        return __('analytics.chart.heading');
    }

    protected function getData(): array
    {
        $dailyVisits = app(AnalyticsService::class)->dailyVisits(30);

        return [
            'datasets' => [
                [
                    'label' => __('analytics.chart.dataset_label'),
                    'data' => array_values($dailyVisits),
                    'borderColor' => '#eab308',
                    'backgroundColor' => 'rgba(234, 179, 8, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.3,
                ],
            ],
            'labels' => array_map(
                fn (string $date): string => Carbon::parse($date)->format('j.n.'),
                array_keys($dailyVisits),
            ),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
