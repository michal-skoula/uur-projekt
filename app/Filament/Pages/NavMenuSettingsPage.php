<?php

namespace App\Filament\Pages;

use App\Filament\Components\ButtonInput;
use App\Filament\Enums\AdminPanelNavigation;
use App\Filament\Widgets\NavMenuBuilderWidget;
use App\Settings\NavMenuSettings;
use BackedEnum;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class NavMenuSettingsPage extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    protected static string|UnitEnum|null $navigationGroup = AdminPanelNavigation::Website;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Bars3;

    protected static string $settings = NavMenuSettings::class;

    public static function getNavigationLabel(): string
    {
        return __('settings/nav-menu.navigation_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('settings/nav-menu.page_title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([

            Section::make(__('settings/nav-menu.button_primary.title'))
                ->aside()
                ->description(__('settings/nav-menu.button_primary.description'))
                ->columns(1)
                ->schema([
                    ButtonInput::make('button_primary', __('settings/nav-menu.button_primary.title')),
                ]),

            Section::make(__('settings/nav-menu.button_secondary.title'))
                ->aside()
                ->description(__('settings/nav-menu.button_secondary.description'))
                ->columns(1)
                ->schema([
                    ButtonInput::make('button_secondary', __('settings/nav-menu.button_secondary.title')),
                ]),

        ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            NavMenuBuilderWidget::class,
        ];
    }
}
