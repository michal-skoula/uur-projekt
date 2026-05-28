---
name: cms-collections
description: "Apply this skill when writing, reviewing, or refactoring content collection code — scaffolding new collections with make:cms-collection, registering collections in config, building collection controllers, or adding Filament resources for model-backed content types. Don't use for building page builder sections, managing page section data, or modifying the CMS rendering pipeline."
license: Proprietary
metadata:
  author: Michal Škoula
---

# CMS Collections

A content collection is a named, model-backed content type registered in `config/content-collections.php`. The config is the source of truth — it maps slugs to models and tracks what's disabled or deprecated.

There is no base class or interface. A collection is just a config entry, a model, and whatever controller and Filament resource is built around it.

## Workflow

**Step 1: Create and migrate the Eloquent model**

Create the model and run its migration before touching the scaffolder.

**Step 2: Scaffold the collection**

```bash
php artisan make:cms-collection {Name} {Model} --no-interaction
```

| Argument / Flag    | Description                                                       |
|--------------------|-------------------------------------------------------------------|
| `Name`             | PascalCase name, e.g. `Contacts`                                  |
| `Model`            | Fully-qualified model class, e.g. `App\Models\Contact`            |
| `--controller`     | Generate `{Name}Controller` with `index()` and `show()` methods   |
| `--filament`       | Run `make:filament-resource` immediately after                    |
| `--no-interaction` | Skip interactive prompts (required for automation)                |

The command registers the collection in config and optionally generates a controller. The model must exist and be autoloadable before running.

Generated files:

| File                                        | When                |
|---------------------------------------------|---------------------|
| `app/Http/Controllers/{Name}Controller.php` | `--controller` only |
| Filament resource (~12 files)               | `--filament` flag   |

**Step 3: Write routes manually**

Add routes to `routes/web.php` wrapped in the `collection.accessible` middleware, passing the collection slug. This returns 404 automatically if the collection is disabled in config:

```php
Route::middleware('collection.accessible:contacts')->group(function () {
    Route::get('/contacts', [ContactsController::class, 'index']);
    Route::get('/contact/{contact:slug}', [ContactsController::class, 'show']);
});
```

Include only the routes the collection needs. Index-only or item-only collections use a single route.

**Step 4: Implement the controller**

Generated at `app/Http/Controllers/{Name}Controller.php`:

```php
public function index(): mixed
{
    //
}

public function show(mixed $item): mixed
{
    //
}
```

Implement these to return the appropriate response. Remove whichever method the collection doesn't need.

**Step 5: Wire the Filament resource**

If `--filament` was used, set `$model` on the generated resource:

```php
protected static string $model = Contact::class;
```

**Step 6: Format and verify**

```bash
vendor/bin/pint --dirty --format agent
composer analyze
php artisan test --compact
```

All three must pass before considering the work done.

## Config Registry

`config/content-collections.php`:

```php
'collections' => [
    'contacts' => \App\Models\Contact::class,
    // @collections-end [DO NOT TOUCH]
],
'disabled' => [],
'deprecated' => [],
```

- **`disabled`** — `collection.accessible` returns 404. Use when a collection needs to be taken offline immediately.
- **`deprecated`** — informational only, does not gate routing. Flags the collection for eventual removal.

Always leave a comment explaining why when disabling or deprecating. Inspect the registry with:

```bash
php artisan config:show content-collections
```

Do not edit the `collections` array by hand — use the scaffolding command.

## Conventions

- Slug is the kebab-case of the name: `Contacts` → `contacts`
- Controller lives at `app/Http/Controllers/{Name}Controller.php`
- The model must exist before running the scaffolder

## Error Handling

- If `make:cms-collection` fails with "class not found", verify the model exists and is autoloadable (`composer dump-autoload` if recently created).
- If the `collection.accessible` middleware returns 404 unexpectedly, check that the slug in the route matches the key in `config/content-collections.php` exactly.
- If Larastan reports issues after scaffolding, fix type hints before finalizing — do not ignore errors.
