<?php

namespace App\Filament\Pages;

use App\Filament\Components\ButtonInput;
use App\Settings\PopupSettings;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class PopupSettingsPage extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Megaphone;

    protected static string $settings = PopupSettings::class;

    public static function getNavigationLabel(): string
    {
        return __('settings/popup.navigation_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('settings/popup.page_title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([

            Section::make(__('settings/popup.general.title'))
                ->aside()
                ->description(__('settings/popup.general.description'))
                ->columns(1)
                ->schema([
                    Toggle::make('enabled')
                        ->label(__('settings/popup.general.enabled.label'))
                        ->helperText(__('settings/popup.general.enabled.helper_text')),
                ]),

            Section::make(__('settings/popup.stripe.title'))
                ->aside()
                ->description(__('settings/popup.stripe.description'))
                ->columns(1)
                ->schema([
                    Toggle::make('stripeEnabled')
                        ->label(__('settings/popup.stripe.enabled.label')),

                    TextInput::make('stripeText')
                        ->label(__('settings/popup.stripe.text.label'))
                        ->placeholder(__('settings/popup.stripe.text.placeholder')),

                    ButtonInput::make('stripeCta', __('settings/popup.stripe.cta.title')),
                ]),

            Section::make(__('settings/popup.popup.title'))
                ->aside()
                ->description(__('settings/popup.popup.description'))
                ->columns(1)
                ->schema([
                    Toggle::make('popupEnabled')
                        ->label(__('settings/popup.popup.enabled.label')),

                    TextInput::make('popupHeading')
                        ->label(__('settings/popup.popup.heading.label')),

                    RichEditor::make('popupContent')
                        ->label(__('settings/popup.popup.content.label'))
                        ->toolbarButtons([
                            ['bold', 'italic', 'underline'],
                            ['link'],
                            ['bulletList', 'orderedList'],
                        ]),

                    ButtonInput::make('popupCta', __('settings/popup.popup.cta.title')),

                    FileUpload::make('popupImage')
                        ->label(__('settings/popup.popup.image.label'))
                        ->helperText(__('settings/popup.popup.image.hint'))
                        ->image()
                        ->visibility('public')
                        ->image(),

                ]),

        ]);
    }
}
