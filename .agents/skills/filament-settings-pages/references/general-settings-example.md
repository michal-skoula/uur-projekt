# Reference: General Settings Page

A settings page covering site identity and favicons — demonstrates file uploads with public visibility, multi-section layout, and the full lang file structure.

## PHP — GeneralSettingsPage

```php
class GeneralSettingsPage extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon       = Heroicon::OutlinedCog6Tooth;
    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Cog6Tooth;

    protected static string $settings = GeneralSettings::class;

    public static function getNavigationLabel(): string
    {
        return __('settings/general.navigation_label');
        // 'General'
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('settings/general.page_title');
        // 'General settings'
    }

    public function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([

            Section::make(__('settings/general.site_identity.title'))
                // 'Site identity'
                ->aside()
                ->description(__('settings/general.site_identity.description'))
                // 'The basic details that identify your website across the browser, search engines, and shared links.'
                ->columns(1)
                ->schema([
                    TextInput::make('name')
                        ->label(__('settings/general.site_identity.name.label'))
                        // 'Site name'
                        ->required(),

                    Textarea::make('description')
                        ->label(__('settings/general.site_identity.description_field.label'))
                        // 'Site description'
                        ->helperText(__('settings/general.site_identity.description_field.hint'))
                        // 'Used in search engine result snippets.'
                        ->rows(2),

                    FileUpload::make('logo')
                        ->label(__('settings/general.site_identity.logo.label'))
                        // 'Logo'
                        ->helperText(__('settings/general.site_identity.logo.hint'))
                        // 'Displayed in the site header and email templates. Recommended size: 200 × 60 px.'
                        ->visibility('public')
                        ->image(),
                ]),

            Section::make(__('settings/general.favicon.title'))
                // 'Favicon'
                ->aside()
                ->description(__('settings/general.favicon.description'))
                // 'The small icon shown in browser tabs and bookmarks. Upload separate versions for light and dark browser themes.'
                ->columns(1)
                ->schema([
                    FileUpload::make('faviconLight')
                        ->label(__('settings/general.favicon.light.label'))
                        // 'Favicon — light theme'
                        ->helperText(__('settings/general.favicon.light.hint'))
                        // 'Shown when the visitor\'s browser or OS uses a light colour scheme.'
                        ->visibility('public')
                        ->image(),

                    FileUpload::make('faviconDark')
                        ->label(__('settings/general.favicon.dark.label'))
                        // 'Favicon — dark theme'
                        ->helperText(__('settings/general.favicon.dark.hint'))
                        // 'Shown on dark colour schemes. If not set, the light favicon is used as a fallback.'
                        ->visibility('public')
                        ->image(),
                ]),

        ]);
    }
}
```

## Lang — lang/cs/settings/general.php

```php
return [
    'navigation_label' => 'General',
    'page_title'       => 'General settings',

    'site_identity' => [
        'title'       => 'Site identity',
        'description' => 'The basic details that identify your website across the browser, search engines, and shared links.',
        'name' => [
            'label' => 'Site name',
        ],
        'description_field' => [
            'label' => 'Site description',
            'hint'  => 'Used in search engine result snippets.',
        ],
        'logo' => [
            'label' => 'Logo',
            'hint'  => 'Displayed in the site header and email templates. Recommended size: 200 × 60 px.',
        ],
    ],

    'favicon' => [
        'title'       => 'Favicon',
        'description' => 'The small icon shown in browser tabs and bookmarks. Upload separate versions for light and dark browser themes.',
        'light' => [
            'label' => 'Favicon — light theme',
            'hint'  => 'Shown when the visitor\'s browser or OS uses a light colour scheme.',
        ],
        'dark' => [
            'label' => 'Favicon — dark theme',
            'hint'  => 'Shown on dark colour schemes. If not set, the light favicon is used as a fallback.',
        ],
    ],
];
```
