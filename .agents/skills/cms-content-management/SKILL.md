---
name: cms-content-management
description: "Apply this skill when populating CMS pages with section content — reading section schemas to understand data shape, writing content via tinker, or verifying rendered output. Don't use for building page builder sections or scaffolding content collections."
license: Proprietary
metadata:
  author: Michal Škoula
---

# CMS Content Management

This skill covers writing and managing content in the CMS — populating pages with section data. For building sections themselves (schemas, templates, views), use the `cms-page-builder` skill instead.

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

## Workflow

**Step 1: Identify the target page**

Look up the page slug in the database or via tinker to confirm the page exists before writing.

**Step 2: Read the section schema before writing any data**

Do not guess the data shape. Before writing content:

1. Look up the slug in `config/page-builder.php` to find the schema class.
2. Open the schema class and read `getSchema()` — the Filament form fields define exactly what keys exist and their types.
3. Note that Filament nested fields (e.g. a field named `cta.label`) serialize to nested arrays (`['cta' => ['label' => '...']]`).

**Step 3: Write content via tinker**

> **Current status:** Dev environment only. No production database exists yet.

```bash
php artisan tinker --execute '
$page = \App\Models\Page::where("slug", "home")->first();
$page->content = [
    ["type" => "hero", "data" => ["heading" => "Welcome", "body" => "..."]],
];
$page->save();
'
```

**Step 4: Verify in the browser**

Always verify after writing. Visit the page and confirm the section renders without errors. A rendering error means the data shape doesn't match what `prepareData()` expects — fix the data, not the template.

## Boundaries

- **Dev only.** Do not use tinker to write content in production.
- **Read schemas, don't modify them.** If the schema needs changes, that's a `cms-page-builder` task.
- **Don't invent fields.** Only use keys that exist in `getSchema()`. Extra keys sit as dead data in the JSON forever.

## Error Handling

- If a section renders with errors, re-read `getSchema()` and verify every key in the `data` payload matches the field name exactly.
- If the page is not found via tinker, confirm the slug matches what's in the database — use `php artisan tinker --execute '\App\Models\Page::pluck("slug");'` to list all slugs.
- If a nested field is missing, check for Filament's dot-notation serialization: `cta.label` in the schema → `$data['cta']['label']` in the array.
