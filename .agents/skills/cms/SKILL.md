---
name: cms
description: "Apply this skill whenever writing, reviewing, or refactoring any CMS/PageBuilder-specific modules and related code — section schemas, templates, the registry, the frontend router, or the Filament PageResource."
license: Proprietary
metadata:
  author: Michal Skoula
---

# PageBuilder

PageBuilder is the CMS subsystem. A `Page` model owns a JSON `content` column holding an ordered list of sections. Each section is a `{ type, data }` pair. The registry maps each `type` slug to two handler classes:

- **Schema** (`App\Contracts\SectionSchema`) — a Filament Builder block. Defines the admin form.
- **Template** (`App\Contracts\SectionTemplate`) — a renderer. Turns stored data into HTML on the frontend.

## Directory layout

```
app/
  Contracts/
    SectionSchema.php          interface — static form declaration
    SectionTemplate.php        interface — instance renderer
  Concerns/
    BuildsSectionSchema.php    trait — shared Block::make() wiring for schemas
  Helpers/
    PageBuilder.php            static accessor over the registry config
    SectionFormatting.php      **text** / <<text>> / *text* hydrator
  Filament/
    PageBuilder/Sections/      *SectionSchema.php (admin side)
  View/
    PageBuilder/Sections/      *SectionTemplate.php (frontend side)
resources/views/
  page-builder/
    page.blade.php             outer layout rendered by the controller
    sections/*.blade.php       per-section Blade views
config/
  page-builder.php             the registry (sections, disabled, deprecated)
```

## Adding a new section

Example: adding a `quote` section.

**1. Schema — admin form:**

```php
// app/Filament/PageBuilder/Sections/QuoteSectionSchema.php
final class QuoteSectionSchema implements SectionSchema
{
    use BuildsSectionSchema;

    public static function getSlug(): string { return 'quote'; }
    public static function getLabel(): string { return __('Pull quote'); }
    public static function getIcon(): Heroicon { return Heroicon::ChatBubbleLeftEllipsis; }

    public static function getSchema(): array
    {
        return [
            Textarea::make('body')->required()->columnSpanFull(),
            TextInput::make('author'),
        ];
    }
}
```

**2. Template — frontend render:**

```php
// app/View/PageBuilder/Sections/QuoteSectionTemplate.php
final class QuoteSectionTemplate implements SectionTemplate
{
    public function render(array $data): View
    {
        return view('page-builder.sections.quote', [
            'body'   => $data['body']   ?? null,
            'author' => $data['author'] ?? null,
        ]);
    }
}
```

**3. Blade view:** `resources/views/page-builder/sections/quote.blade.php`.

**4. Register in `config/page-builder.php`:**

```php
'quote' => [
    'schema'   => QuoteSectionSchema::class,
    'template' => QuoteSectionTemplate::class,
],
```

The boot-time validator in `PageBuilderServiceProvider` fails loudly in dev/CI if the registry references a class that doesn't implement the right contract.

## Disabling and deprecating sections

- `config/page-builder.disabled` — killswitch. Hidden from the admin add-picker AND skipped at render time. Use for a broken or exploited section.
- `config/page-builder.deprecated` — hidden from the admin add-picker, but existing pages continue to render. Use for a section superseded by a newer one.

Both lists take slugs: `['hero', 'old-quote']`.

## Interface + trait pattern

`SectionSchema` is an interface of static methods. `BuildsSectionSchema` is a trait that provides the shared `make(): Block` wiring. Concrete sections `implements SectionSchema` AND `use BuildsSectionSchema`. The trait redeclares the interface's abstracts — this is structural documentation, not a DRY violation: it pins the trait's dependency on the consumer's methods so the trait is self-describing.

Why static? A section is a declarative definition, not a stateful object — there's no per-instance data. `Section::make()` mirrors Filament's own `make()` convention.

## Livewire sections

Templates return `View|string`. For interactive sections, return a Blade view whose body is a `<livewire:component-name :data="$data" />` tag. The outer layout `page-builder/page.blade.php` includes `@livewireStyles` / `@livewireScripts`, so embedded Livewire components hydrate correctly. No Livewire parent component is required — this avoids the pain of `Livewire::mount()` from a controller.

## How the frontend resolves a request

`routes/web.php` routes everything (minus `/admin`, which Filament owns) to `PageBuilderController`:

1. `ResolvePageFromPath::handle($path)` matches the path directly against `pages.slug` (empty path → null slug / root page).
2. Returns 404 if missing or unpublished.
3. Loops `$page->content`, skipping entries with disabled slugs or missing templates.
4. For each valid entry, instantiates the template (`app($templateClass)`) and calls `->render($data)`.
5. Rendered sections (Views or strings) are passed to `page-builder.page` and echoed with `{!! !!}`.

## Formatting helper

`SectionFormatting::hydrateHeading($text)` converts three inline tokens to HTML:

- `**text**` → `<span class="text-brand">text</span>`
- `<<text>>` → `<span class="text-muted">text</span>`
- `*text*`   → `<em>text</em>`

Define `text-brand` and `text-muted` in Tailwind. Escape is on by default — pass `escape: false` only when the input is already trusted.

## Common gotchas

- **File uploads need public visibility.** Filament defaults to private. Always add `->visibility('public')` on `FileUpload` in a schema when the frontend needs to read the file.
- **Layout components don't span full width by default.** `Grid`, `Section`, `Fieldset` need an explicit `->columnSpanFull()` or `->columns(n)` when you want full width.
- **Dot-notation data binding.** Nested fields like `cta.label` serialize to `['cta' => ['label' => ...]]`. Read the same nested path in the template.
- **Never rename a registered slug without a migration.** Slugs are persisted in `pages.content`. Renaming orphans existing content.
