<?php

namespace App\Filament\Pages;

use App\Filament\Components\ButtonInput;
use App\Filament\Enums\AdminPanelNavigation;
use App\Settings\ContactSettings;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Guava\IconPicker\Forms\Components\IconPicker;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class ContactSettingsPage extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhone;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Phone;

    protected static string|UnitEnum|null $navigationGroup = AdminPanelNavigation::Settings;

    protected static ?int $navigationSort = 2;

    protected static string $settings = ContactSettings::class;

    public static function getNavigationLabel(): string
    {
        return __('settings/contact.navigation_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('settings/contact.page_title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([

            Section::make(__('settings/contact.social_media.title'))
                ->aside()
                ->description(__('settings/contact.social_media.description'))
                ->columns(1)
                ->schema([
                    Repeater::make('socials')
                        ->label(__('settings/contact.social_media.channels.label'))
                        ->addActionLabel(__('settings/contact.social_media.channels.add'))
                        ->addActionAlignment(Alignment::Start)
                        ->columns(2)
                        ->schema([
                            IconPicker::make('icon')
                                ->label(__('settings/contact.social_media.channels.icon'))
                                ->sets(['fontawesome-brands'])
                                ->required(),
                            TextInput::make('name')
                                ->label(__('settings/contact.social_media.channels.name'))
                                ->required(),
                            TextInput::make('url')
                                ->label(__('settings/contact.social_media.channels.url'))
                                ->columnSpanFull()
                                ->prefixIcon(Heroicon::Link)
                                ->url()
                                ->required(),
                        ]),
                ]),

            Section::make(__('settings/contact.footer_nav.title'))
                ->aside()
                ->description(__('settings/contact.footer_nav.description'))
                ->columns(1)
                ->schema([
                    Repeater::make('footerNav')
                        ->label(__('settings/contact.footer_nav.columns.label'))
                        ->addActionLabel(__('settings/contact.footer_nav.columns.add'))
                        ->addActionAlignment(Alignment::Start)
                        ->columns(1)
                        ->schema([
                            TextInput::make('heading')
                                ->label(__('settings/contact.footer_nav.columns.heading'))
                                ->required(),
                            Repeater::make('items')
                                ->label(__('settings/contact.footer_nav.columns.items.label'))
                                ->addActionLabel(__('settings/contact.footer_nav.columns.items.add'))
                                ->addActionAlignment(Alignment::Start)
                                ->columns(1)
                                ->schema([
                                    ButtonInput::make('item', __('settings/contact.footer_nav.columns.items.item'), isRequired: true),
                                ]),
                        ]),
                ]),

            Section::make(__('settings/contact.error_report.title'))
                ->aside()
                ->description(__('settings/contact.error_report.description'))
                ->columns(1)
                ->schema([
                    ButtonInput::make('errorReportButton', __('settings/contact.error_report.button.label')),
                ]),

        ]);
    }
}
