# Content Collections

A content collection is a named, model-backed content type registered in `config/content-collections.php`. The config is the source of truth — it maps slugs to models, and tracks what's disabled or deprecated.

There is no base class or interface. A collection is just a config entry, a model, and whatever controller and Filament resource you build around it.

---

## Workflow

1. Create and migrate the Eloquent model
2. Run `make:cms-collection` to register it and optionally scaffold a controller and Filament resource
3. Write routes manually
4. Implement the controller methods

---

## Scaffolding

```bash
php artisan make:cms-collection {Name} {Model}
```

| Argument / Flag    | Description                                                       |
|--------------------|-------------------------------------------------------------------|
| `Name`             | PascalCase name, e.g. `Contacts`                                  |
| `Model`            | Fully-qualified model class, e.g. `App\Models\Contact`            |
| `--controller`     | Generate `{Name}Controller` with `index()` and `show()` methods   |
| `--filament`       | Run `make:filament-resource` immediately after                    |
| `--no-interaction` | Skip the Filament prompt (for automation)                         |

The command registers the collection in config and optionally generates a controller. The model must exist and be autoloadable before running.

### Generated files

| File | When |
|------|------|
| `app/Http/Controllers/{Name}Controller.php` | `--controller` only |
| Filament resource (~12 files) | `--filament` or interactive prompt |

---

## Routes

Write these manually in `routes/web.php`:

```php
Route::get('/contacts', [ContactsController::class, 'index']);
Route::get('/contact/{contact:slug}', [ContactsController::class, 'show']);
```

---

## The controller

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

---

## Filament resource

Run with `--filament` or answer yes to the interactive prompt. After generation, set `$model` on the resource:

```php
protected static string $model = Contact::class;
```

---

## Config registry

`config/content-collections.php`:

```php
'collections' => [
    'contacts' => \App\Models\Contact::class,
    // @collections-end [DO NOT TOUCH]
],
'disabled' => [],
'deprecated' => [],
```

- **`disabled`** — blocks access immediately. Use for broken or dangerous collections.
- **`deprecated`** — flags for removal, preserves existing behaviour.

Always leave a comment explaining why when disabling or deprecating. Read the registry with `php artisan config:show content-collections`.

Do not edit the `collections` array by hand — use the scaffolding command.

---

## Conventions

- Slug is the kebab-case of the name: `Contacts` → `contacts`
- Controller lives at `app/Http/Controllers/{Name}Controller.php`
- Run `vendor/bin/pint --dirty` after scaffolding
