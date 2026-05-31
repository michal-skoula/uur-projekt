<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class GeneralSettingsPage extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Cog6Tooth;

    protected static string $settings = GeneralSettings::class;

    public static function getNavigationLabel(): string
    {
        return __('settings/general.navigation_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('settings/general.page_title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([

            Section::make(__('settings/general.site_identity.title'))
                ->aside()
                ->description(__('settings/general.site_identity.description'))
                ->columns(1)
                ->schema([
                    TextInput::make('name')
                        ->label(__('settings/general.site_identity.name.label'))
                        ->required(),

                    Textarea::make('description')
                        ->label(__('settings/general.site_identity.description_field.label'))
                        ->helperText(__('settings/general.site_identity.description_field.hint'))
                        ->rows(2),

                    FileUpload::make('logo')
                        ->label(__('settings/general.site_identity.logo.label'))
                        ->helperText(__('settings/general.site_identity.logo.hint'))
                        ->visibility('public')
                        ->image(),
                ]),

            Section::make(__('settings/general.favicon.title'))
                ->aside()
                ->description(__('settings/general.favicon.description'))
                ->columns(1)
                ->schema([
                    FileUpload::make('faviconLight')
                        ->label(__('settings/general.favicon.light.label'))
                        ->helperText(__('settings/general.favicon.light.hint'))
                        ->visibility('public')
                        ->image(),

                    FileUpload::make('faviconDark')
                        ->label(__('settings/general.favicon.dark.label'))
                        ->helperText(__('settings/general.favicon.dark.hint'))
                        ->visibility('public')
                        ->image(),
                ]),

        ]);
    }
}
