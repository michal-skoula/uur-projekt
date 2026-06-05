<?php

namespace App\Providers\Filament;

use App\Settings\GeneralSettings;
use Awcodes\Curator\CuratorPlugin;
use Awcodes\Curator\Models\Media;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Wezlo\FilamentSearchSpotlight\FilamentSearchSpotlightPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName(fn (GeneralSettings $s) => $s->name)
            ->brandLogo(fn (GeneralSettings $s) => Media::find($s->logo)?->url)
            ->favicon(fn (GeneralSettings $s) => Media::find($s->getFaviconForDarkMode())?->url)
            ->brandLogoHeight('5rem')
            ->globalSearch()
            ->darkMode(isForced: true)
            ->login()
            ->colors([
                'primary' => Color::Yellow,
                'gray' => Color::Olive,
                'info' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Amber,
                'danger' => Color::Red,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->assets([
                //
            ])
            ->plugins([
                CuratorPlugin::make()
                    ->label('Soubor')
                    ->pluralLabel('Soubory')
                    ->navigationIcon('heroicon-o-folder'),
                FilamentSearchSpotlightPlugin::make(),
            ])
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}
