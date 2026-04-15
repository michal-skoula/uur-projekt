# Page Builder

PageBuilder is the CMS subsystem. A `Page` model owns a JSON `content` column holding an ordered list of sections. Each section is a `{ type, data }` pair. The registry maps each `type` slug to two handler classes: a **Schema** (Filament admin form) and a **Template** (frontend renderer).

This doc covers building and maintaining sections — the only part of PageBuilder an agent should modify.

## Where section files live

- Schemas: `app/Filament/PageBuilder/Sections/*SectionSchema.php`
- Templates: `app/View/PageBuilder/Sections/*SectionTemplate.php`
- Blade views: `resources/views/page-builder/sections/*.blade.php`
- Registry: `config/page-builder.php` (sections, disabled, deprecated lists)

Do not edit `app/Contracts/` or `app/Concerns/` — those define the section interface and are off-limits.

## Scaffolding a new section

<!-- TODO: implement `php artisan make:page-builder-section` — should prompt for slug, label, icon, and template type (livewire | blade | none), generate the schema + template + blade view, and append the registry entry in config/page-builder.php -->

Run the scaffolder and pick a template type:

```bash
php artisan make:page-builder-section
```

Template-type choices:

- **blade** — static section. Generates a template class that returns `view('page-builder.sections.<slug>', $data)` and a matching blade view.
- **livewire** — interactive section. Generates a template class that returns a blade view containing `<livewire:... :data="$data" />`, plus the Livewire component. The outer `page-builder.page` layout already includes `@livewireStyles` / `@livewireScripts`, so no parent Livewire wrapper is needed.
- **none** — template returns a raw HTML string. Use sparingly.

The command writes the schema, template, view (if applicable), and registers the slug in `config/page-builder.php`. After scaffolding, fill in the schema fields and the view markup.

## Conventions

**`$data` array shape.** Filament serializes dot-notation field names into nested arrays. A form field named `cta.label` becomes `$data['cta']['label']`. Read every field with a `?? null` fallback — stored pages may predate new fields:

```php
public function render(array $data): View
{
    return view('page-builder.sections.quote', [
        'body'   => $data['body']   ?? null,
        'author' => $data['author'] ?? null,
        'ctaLabel' => $data['cta']['label'] ?? null,
    ]);
}
```

**Blade view naming.** The view path mirrors the slug: slug `pull-quote` → `resources/views/page-builder/sections/pull-quote.blade.php`.

**Inline text formatting.** Use `SectionFormatting::hydrateHeading($text)` in blade views to convert editor tokens to HTML:

- `**text**` → `<span class="text-brand">text</span>`
- `<<text>>` → `<span class="text-muted">text</span>`
- `*text*`   → `<em>text</em>`

Escape is on by default — only pass `escape: false` when the input is already trusted. Tailwind must define `text-brand` and `text-muted`.

## Disabling and deprecating sections

- `config/page-builder.disabled` — killswitch. Hidden from the admin picker AND skipped at render time. Use for broken or compromised sections.
- `config/page-builder.deprecated` — hidden from the admin picker, but existing pages continue to render. Use when superseding a section.

Both lists take slugs: `['hero', 'old-quote']`.

## Validation

The `PageBuilderServiceProvider` validates the registry at boot in dev/CI — it fails loudly if a registered class is missing or doesn't implement the right contract.

<!-- TODO: implement `php artisan page-builder:validate` — should run the same checks as the boot-time validator, for use in CI and after slug renames. Document the failure output format once it exists. -->

Run `php artisan page-builder:validate` manually after editing `config/page-builder.php` or renaming classes, and wire it into CI.

## Gotchas

- **File uploads need public visibility.** Filament defaults to private. Add `->visibility('public')` on `FileUpload` when the frontend needs to read the file.
- **Layout components don't span full width by default.** `Grid`, `Section`, `Fieldset` need an explicit `->columnSpanFull()` or `->columns(n)`.
- **Never rename a registered slug without a migration.** Slugs are persisted in `pages.content` — renaming orphans existing content. Add a data migration that rewrites the slug in every page's `content` JSON before changing the registry.
