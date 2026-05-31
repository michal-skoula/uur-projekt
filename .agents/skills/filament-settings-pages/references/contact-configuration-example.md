# Reference: Contact Configuration Page

A real settings page with two sections — contact details and social media channels.
Shows how to handle a `Repeater` inside a section while keeping the outer column count at 1.

## PHP — ContactSettingsPage

```php
class ContactSettingsPage extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon       = Heroicon::OutlinedPhone;
    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Phone;

    protected static ?int $navigationSort = 2;

    protected static string $settings = ContactConfiguration::class;

    public static function getNavigationLabel(): string
    {
        return __('settings/contact.navigation_label');
        // 'Contacts'
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('settings/contact.page_title');
        // 'Contact settings'
    }

    public function form(Schema $schema): Schema
    {
    return $schema
        ->columns(1)
        ->components([

            Section::make(__('settings/contact.contact_info.title'))
                // 'Contact information'
                ->aside()
                ->description(__('settings/contact.contact_info.description'))
                // 'Phone number, email address, and business contacts shown to visitors across the website.'
                ->columns(1)
                ->schema([
                    TextInput::make('tel')
                        ->label(__('settings/contact.contact_info.tel.label'))
                        // 'Phone number'
                        ->prefixIcon(Heroicon::OutlinedPhone)
                        ->tel(),
                    TextInput::make('email')
                        ->label(__('settings/contact.contact_info.email.label'))
                        // 'Email address'
                        ->prefixIcon(Heroicon::OutlinedAtSymbol)
                        ->email(),
                    TextInput::make('address')
                        ->label(__('settings/contact.contact_info.address.label'))
                        // 'Postal address'
                        ->prefixIcon(Heroicon::OutlinedMapPin),
                    TextInput::make('ic')
                        ->label(__('settings/contact.contact_info.ic.label'))
                        // 'Company registration number (IČO)'
                        ->helperText(__('settings/contact.contact_info.ic.hint'))
                        // 'The number assigned to your organisation by the Czech Business Register.'
                        ->prefixIcon(Heroicon::OutlinedBuildingOffice),
                    TextInput::make('data_box')
                        ->label(__('settings/contact.contact_info.data_box.label'))
                        // 'Data box ID'
                        ->helperText(__('settings/contact.contact_info.data_box.hint'))
                        // 'Your organisation\'s data box identifier for official electronic communication (Datová schránka).'
                        ->prefix('DS'),
                ]),

            Section::make(__('settings/contact.social_media.title'))
                // 'Social media'
                ->aside()
                ->description(__('settings/contact.social_media.description'))
                // 'Links to your social media profiles, shown in the website footer and on the contact page.'
                ->columns(1)
                ->schema([
                    Repeater::make('socials')
                        ->label(__('settings/contact.social_media.channels.label'))
                        // 'Channels'
                        ->addActionLabel(__('settings/contact.social_media.channels.add'))
                        // 'Add channel'
                        ->addActionAlignment(Alignment::Start)
                        ->columns(2)
                        ->schema([
                            IconPicker::make('icon')
                                ->label(__('settings/contact.social_media.channels.icon'))
                                // 'Icon'
                                ->sets(['fontawesome-brands'])
                                ->required(),
                            TextInput::make('name')
                                ->label(__('settings/contact.social_media.channels.name'))
                                // 'Platform name'
                                ->required(),
                            TextInput::make('url')
                                ->label(__('settings/contact.social_media.channels.url'))
                                // 'Profile URL'
                                ->columnSpan('full')
                                ->prefixIcon(Heroicon::Link)
                                ->url()
                                ->required(),
                        ]),
                ]),

        ]);
}
```

## Lang — lang/cs/settings/contact.php

```php
return [
    'navigation_label' => 'Contacts',
    'page_title'       => 'Contact settings',

    'contact_info' => [
        'title'       => 'Contact information',
        'description' => 'Phone number, email address, and postal address shown to visitors across the website.',
        'tel' => [
            'label' => 'Phone number',
        ],
        'email' => [
            'label' => 'Email address',
        ],
        'address' => [
            'label' => 'Postal address',
        ],
        'ic' => [
            'label' => 'Company registration number (IČO)',
            'hint'  => 'The number assigned to your organisation by the Czech Business Register.',
        ],
        'data_box' => [
            'label' => 'Data box ID',
            'hint'  => 'Your organisation\'s data box identifier for official electronic communication (Datová schránka).',
        ],
    ],
    'social_media' => [
        'title'       => 'Social media',
        'description' => 'Links to your social media profiles, shown in the website footer and on the contact page.',
        'channels' => [
            'label' => 'Channels',
            'add'   => 'Add channel',
            'icon'  => 'Icon',
            'name'  => 'Platform name',
            'url'   => 'Profile URL',
        ],
    ],
];
```

## Notes

- The `Repeater` inner schema uses `->columns(2)` so icon and name sit side-by-side — that's fine because the repeater controls its own internal grid, independent of the section column count.
- The outer section is still `->columns(1)`. The repeater itself is a single field occupying that one column.
- `->addActionAlignment(Alignment::Start)` keeps the "Add channel" button left-aligned, consistent with the left-to-right reading flow of the repeater rows.
