# Content Management

This module covers writing and managing content in the CMS — populating pages with section data. For building sections themselves (schemas, templates, views), see `page-builder.md`.

## How content is stored

A `Page` model has a JSON `content` column containing an ordered array of sections:

```json
[
    { "type": "hero", "data": { "heading": "Welcome", "body": "..." } },
    { "type": "quote", "data": { "text": "...", "author": "..." } }
]
```

- `type` — the section's registered slug (see `config/page-builder.php`)
- `data` — the payload, shaped by the section's Filament schema

## Understanding the data shape

Before writing any content, you **must** read the section's schema to understand the expected data shape:

1. Look up the slug in `config/page-builder.php` to find the schema class
2. Open the schema class and read `getSchema()` — the Filament form fields define exactly what keys exist and their types
3. Nested Filament fields (e.g. a field named `cta.label`) serialize to nested arrays (`['cta' => ['label' => '...']]`)

Do not guess the shape. Read the schema.

## Writing content (dev environment)

> **Current status:** Dev environment only. No production database exists yet. Use tinker.
>
> **Future:** A validated write path (CMS MCP server or Laravel API Resource) will replace tinker. The goal is an interface that reads the section's Filament schema definition, validates incoming data against it, and rejects malformed payloads before they touch the database. Until that exists, tinker is the only option.

### Via tinker

```bash
php artisan tinker --execute '
$page = \App\Models\Page::where("slug", "home")->first();
$page->content = [
    ["type" => "hero", "data" => ["heading" => "Welcome", "body" => "..."]],
];
$page->save();
'
```

### Verification

**Always verify after writing.** Visit the page in the browser and confirm the section renders without errors. A rendering error means the data shape doesn't match what `prepareData()` expects — fix the data, not the template.

## Boundaries

- **Dev only.** Do not use tinker to write content in production. When a production environment exists, content writes will only be allowed through the validated CMS API.
- **Read schemas, don't modify them.** This module is about populating content, not changing the structure. If the schema needs changes, that's a `page-builder.md` task.
- **Don't invent fields.** Only use keys that exist in `getSchema()`. Extra keys won't break anything but they'll sit as dead data in the JSON forever.
