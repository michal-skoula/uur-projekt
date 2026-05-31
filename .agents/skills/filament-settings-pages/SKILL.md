---
name: filament-settings-pages
description: "Apply this skill when creating or modifying Filament settings pages — structuring sections with aside layout, writing i18n labels for non-technical users, and organising form fields. Don't use for building page builder sections, collections, or non-settings Filament resources."
license: Proprietary
metadata:
  author: Michal Škoula
---

# Filament Settings Pages

Settings pages in this project extend Filament's `SettingsPage` and are backed by a `spatie/laravel-settings` class. Each page is a single form broken into meaningful subsections — one per concern.

## Page structure

A settings page class lives at `app/Filament/Pages/{Name}SettingsPage.php` and its backing settings class at `app/Settings/{Name}Settings.php`.

```php
class GeneralSettingsPage extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon       = Heroicon::OutlinedCog6Tooth;
    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Cog6Tooth;

    protected static string $settings = GeneralSettings::class;

    public static function getNavigationLabel(): string
    {
        return __('settings/general.navigation_label');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('settings/general.page_title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            // sections go here
        ]);
    }
}
```

## Navigation — icon and label

### Icon

Set both the default (outlined) and active (filled/solid) icon. Use outlined by default, switch to the filled variant when active — this gives the selected item a visual weight that makes it easy to see at a glance.

```php
protected static string|BackedEnum|null $navigationIcon       = Heroicon::OutlinedCog6Tooth;
protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Cog6Tooth;
```

Pick an icon that clearly represents the domain:

| Settings page     | Icon pair                                            |
|-------------------|------------------------------------------------------|
| General / Site    | `OutlinedCog6Tooth` / `Cog6Tooth`                    |
| Contact           | `OutlinedPhone` / `Phone`                            |
| Navigation / Menu | `OutlinedBars3` / `Bars3`                            |
| Users             | `OutlinedUsers` / `Users`                            |
| Email / Mail      | `OutlinedEnvelope` / `Envelope`                      |
| Appearance        | `OutlinedPaintBrush` / `PaintBrush`                  |

### Navigation label and page title

`$navigationLabel` is a static property and cannot call `__()`. Override `getNavigationLabel()` and `getTitle()` as methods instead:

```php
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
```

Add both keys to the lang file at the top level, outside any section group:

```php
// lang/cs/settings/contact.php
return [
    'navigation_label' => 'Contacts',
    'page_title'       => 'Contact settings',

    'contact_info' => [ ... ],
    'social_media'  => [ ... ],
];
```

## Section layout — the required baseline

Every logical group of fields must be wrapped in a `Section` with at minimum:

- heading passed to `Section::make(...)` — short label, use i18n
- `->aside()` — puts the heading/description on the left, fields on the right
- `->description(...)` — plain-language explanation for a non-technical user, use i18n

```php
use Filament\Schemas\Components\Section;

Section::make(__('settings/general.site_identity.title'))
    ->aside()
    ->description(__('settings/general.site_identity.description'))
    ->schema([
        // fields
    ]),
```

Never put bare form fields directly inside the page's top-level `->components([...])`. Everything belongs inside a section.

## Column count

Use exactly **one column** inside every section schema. Settings forms are narrow; two columns create cramped, hard-to-scan layouts.

```php
Section::make(__('settings/general.site_identity.title'))
    ->aside()
    ->description(...)
    ->columns(1)  // explicit, even though 1 is default, for clarity
    ->schema([...]),
```

## i18n — where text lives and how to write it

All user-visible strings must use `__()`. Lang files for settings pages live under:

```
lang/{locale}/settings/{page-slug}.php
```

Examples:
- `lang/cs/settings/general.php`
- `lang/cs/settings/nav-menu.php`

### Writing good labels and descriptions

Assume the user understands how to use a computer but has no technical knowledge. The goal is clarity, not brevity.

**Bad** — too technical or too short:
```php
Section::make('Favicon')
    ->description('Upload a .ico or .png file.')
```

**Good** — tells the user what it is and why it matters:
```php
Section::make(__('settings/general.favicon.title'))
// lang: 'title' => 'Favicon'
    ->description(__('settings/general.favicon.description'))
// lang: 'description' => 'The small icon that appears in browser tabs and next to your site name in Google search results.'
```

More examples:

| Setting         | Good description                                                                                      |
|-----------------|-------------------------------------------------------------------------------------------------------|
| Site name       | The name of your website as it appears in the browser tab, search results, and shared links.          |
| Logo            | Your organisation's logo. Used in the site header and email templates. Recommended size: 200 × 60 px. |
| Favicon (light) | The small icon shown in browser tabs and bookmarks on light-coloured browser themes.                  |
| OG image        | The preview image shown when someone shares a link to your site on social media or in messaging apps. |

### Lang file structure

Group keys by section slug, then by field:

```php
// lang/cs/settings/general.php
return [
    'site_identity' => [
        'title'       => 'Site identity',
        'description' => 'The basic details that identify your website.',
        'name'        => [
            'label' => 'Site name',
        ],
        'description_field' => [
            'label'       => 'Site description',
            'placeholder' => 'A short sentence about what your site is about.',
            'hint'        => 'Used in search engine result snippets.',
        ],
    ],
    'favicon' => [
        'title'       => 'Favicon',
        'description' => 'The small icon shown in browser tabs and bookmarks.',
        'light' => [
            'label' => 'Favicon — light theme',
            'hint'  => 'Shown when the user\'s browser or OS uses a light colour scheme.',
        ],
        'dark' => [
            'label' => 'Favicon — dark theme',
            'hint'  => 'Shown on dark colour schemes. If not set, the light favicon is used.',
        ],
    ],
];
```

## Hint actions — use sparingly

A `HintAction` opening a modal can provide extra context, but only when a one-sentence description isn't enough. Do not default to adding them.

A good candidate: an OG image field where the user needs to see the expected dimensions, aspect ratio, and an example of how it appears when shared.

A bad candidate: a site name field — the description is already self-explanatory.

```php
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->label(__('settings/general.site_identity.name.label'))
    ->hintAction(
        Action::make('help')
            ->icon(Heroicon::OutlinedQuestionMarkCircle)
            ->modalHeading(__('settings/general.site_identity.name.help_heading'))
            ->modalContent(view('filament.hints.site-name'))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('filament::actions/modal.actions.close.label'))
    ),
```

## Reusable field components

Check `app/Filament/Components/` for reusable field components (e.g. `ButtonInput`, `LinkInput`) before building custom field combinations.

## File uploads

Always add `->visibility('public')` when the uploaded file must be accessible on the frontend:

```php
FileUpload::make('logo')
    ->label(__('settings/general.site_identity.logo.label'))
    ->visibility('public')
    ->image(),
```

## Reference examples

- `references/general-settings-example.md` — site identity and favicon sections, file uploads with public visibility, full lang file
- `references/contact-configuration-example.md` — two sections (contact details + social media repeater), full i18n and lang file

## Checklist before finishing

- [ ] CTA / button fields use `ButtonInput::make()` rather than hand-rolled text + link pairs
- [ ] `$navigationIcon` uses an outlined Heroicon; `$activeNavigationIcon` uses the filled variant
- [ ] `getNavigationLabel()` and `getTitle()` are method overrides using `__()`
- [ ] Every group of related fields is in its own `Section::make(heading)->aside()->description()`
- [ ] All visible strings use `__()` with keys under `lang/{locale}/settings/`
- [ ] All descriptions explain the setting in plain language a non-technical user can understand
- [ ] Section schemas use one column
- [ ] File upload fields have `->visibility('public')` where frontend access is needed
- [ ] Run `vendor/bin/pint --dirty --format agent` and `composer analyze -- app/Filament/Pages/YourSettingsPage.php`
