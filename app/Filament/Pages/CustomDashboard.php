<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AnalyticsOverview;
use App\Filament\Widgets\MostVisitedPagesTable;
use App\Filament\Widgets\QuickActionsWidget;
use App\Filament\Widgets\TotalVisitsChart;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Enums\Width;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Support\Htmlable;

class CustomDashboard extends BaseDashboard
{
    protected Width|string|null $maxContentWidth = Width::ScreenTwoExtraLarge;

    public function getTitle(): string|Htmlable
    {
        $hour = (int) now()->format('H');
        $user = auth()->user()->name;

        $period = match (true) {
            $hour < 9 => 'morning',
            $hour < 12 => 'day',
            $hour < 18 => 'afternoon',
            default => 'evening',
        };

        return __('dashboard.greeting.'.$period).", {$user}!";
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('dashboard.subtext');
    }

    /**
     * @return array<class-string<Widget>>
     */
    public function getWidgets(): array
    {
        return [
            //            AccountWidget::class,
            QuickActionsWidget::class,
            AnalyticsOverview::class,
            TotalVisitsChart::class,
            MostVisitedPagesTable::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 2;
    }
}
