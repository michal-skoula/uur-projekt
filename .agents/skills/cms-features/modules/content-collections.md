# Content Collections

A ContentCollection is a typed descriptor for a model-backed content type (e.g. Contacts, Services, Team Members). It tells the CMS which Eloquent model backs the collection, what uniquely identifies a record, and optionally which controller handles its frontend pages.

Think of it as a router stub: it carries configuration, but has no behaviour of its own. The CMS uses it to wire things up.

---

## Workflow

1. Create and migrate the Eloquent model
2. Run `make:cms-collection` — scaffolds the collection class, optionally a controller and Filament resource
3. Customize the collection class (slugs, base path, identifying column)
4. Implement the controller methods

---

## Scaffolding

```bash
php artisan make:cms-collection {Name} {Model}
```

| Argument / Flag    | Description                                                 |
|--------------------|-------------------------------------------------------------|
| `Name`             | PascalCase name, e.g. `Contacts`                            |
| `Model`            | Fully-qualified model class, e.g. `App\Models\Contact`      |
| `--index`          | Implement `HasIndexPage` — adds `index()` to the controller |
| `--items`          | Implement `HasItemPages` — adds `show()` to the controller  |
| `--filament`       | Run `make:filament-resource` immediately after              |
| `--no-interaction` | Skip the Filament prompt (for automation)                   |

A controller is only generated when `--index` or `--items` is passed. The model must exist and be autoloadable before running the command.

**What gets registered:** the command inserts an entry into `config/content-collections.php` automatically. Do not edit the `collections` array by hand.

### Generated files

| File                                          | Always?                          |
|-----------------------------------------------|----------------------------------|
| `app/ContentCollections/{Name}Collection.php` | Yes                              |
| `app/Http/Controllers/{Name}Controller.php`   | Only with `--index` or `--items` |
| Filament resource (12 files)                  | Only with `--filament` or prompt |

---

## The collection class

Generated at `app/ContentCollections/{Name}Collection.php`. The two abstract methods must always be filled in:

```php
public static function getModel(): string
{
    return Contact::class; // the backing Eloquent model
}

public static function getUniquelyIdentifyingColumnName(): int|string
{
    return 'slug'; // column used to look up individual items (slug, id, etc.)
}
```

### HasIndexPage / HasItemPages

Implement these interfaces when the collection has frontend pages:

| Interface      | When to use                          | Methods added                                              |
|----------------|--------------------------------------|------------------------------------------------------------|
| `HasIndexPage` | Collection has a listing page        | `getIndexSlug()`, `getControllerIndexResolverMethodName()` |
| `HasItemPages` | Collection has individual item pages | `getItemsSlug()`, `getControllerItemResolverMethodName()`  |

Both extend `HasController`, which requires `getController()` and `getBasePath()`.

```php
// URL structure example for Contacts with both interfaces:
// getBasePath()  = '/'
// getIndexSlug() = 'contacts'    →  /contacts
// getItemsSlug() = 'contact'     →  /contact/{slug}

public static function getBasePath(): string
{
    return '/';
}

public static function getIndexSlug(): string
{
    return 'contacts';
}

public static function getItemsSlug(): string
{
    return 'contact'; // defaults to singular of the collection slug
}
```

The `getControllerIndexResolverMethodName()` and `getControllerItemResolverMethodName()` return the controller method names (`'index'` and `'show'` by default). Change them only if you renamed the controller methods.

---

## The controller

Generated at `app/Http/Controllers/{Name}Controller.php` when `--index` or `--items` is passed:

```php
public function index(): mixed
{
    // render the listing page
}

public function show(mixed $item): mixed
{
    // render the individual item page
}
```

Implement these methods to return the appropriate response (view, JSON, etc.). Route binding is not yet active — `$item` will need to be resolved manually for now.

---

## Filament resource

Run with `--filament` or answer yes to the interactive prompt. This calls `php artisan make:filament-resource {Name}` which generates ~12 files under `app/Filament/Resources/{Name}/`.

After generation, set `$model` on the resource to the collection's model:

```php
protected static string $model = Contact::class;
// or derive it:
protected static string $model = ContactsCollection::getModel();
```

The collection class and Filament resource are independent — the collection describes routing, the resource describes the admin UI. They share only the model.

---

## Config registry

`config/content-collections.php` has three keys:

```php
'collections' => [
    'contacts' => \App\ContentCollections\ContactsCollection::class,
    // @collections-end [DO NOT TOUCH]
],
'disabled' => [],
'deprecated' => [],
```

- **`disabled`** — collection is immediately inaccessible everywhere. Use for broken or dangerous collections.
- **`deprecated`** — preserved for existing use but no longer active for new work.

Always leave a comment on disabled/deprecated entries explaining why.

Read the registry: `php artisan config:show content-collections`

---

## Conventions

- Class name: `{Name}Collection` — e.g. `ContactsCollection`
- Directory: `app/ContentCollections/`
- `getItemsSlug()` defaults to `Str::singular()` of the index slug. Override it if Laravel's singularization produces the wrong word.
- Run `vendor/bin/pint --dirty` after scaffolding — the stub includes all `use` imports; Pint removes the unused ones.
- The model must exist before running the command (`class_exists` is checked).
