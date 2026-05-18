<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

use function Laravel\Prompts\confirm;

class MakeCmsCollectionCommand extends Command implements PromptsForMissingInput
{
    protected $signature =
        'make:cms-collection '.
        '{name : PascalCase name of the collection (e.g. Contacts, Services)} '.
        '{model : Fully-qualified model class (e.g. App\Models\Contact)} '.
        '{--index : Implement HasIndexPage (generates controller index method)} '.
        '{--items : Implement HasItemPages (generates controller item method)} '.
        '{--filament : Scaffold a Filament resource via make:filament-resource}';

    protected $description = 'Scaffolds a new ContentCollection and registers it in the content-collections config file.';

    private const string NAME_PATTERN = '/^[A-Z][A-Za-z0-9]*$/';

    private const string CONFIG_LOCATOR = '// @collections-end [DO NOT TOUCH]';

    // todo: make this configurable in the config file
    //       - handle missing config
    //       - update to be resolved in the constructor instead of a const
    private const string COLLECTIONS_DIRECTORY = 'app/ContentCollections';

    protected Filesystem $files;

    public function __construct()
    {
        parent::__construct();

        $this->files = app('files');
    }

    /**
     * @return array<string, string|string[]>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => ['What is the PascalCase name of this collection?', 'E.g. Contacts, Services'],
            'model' => ['What is the Model represented by collection?', 'E.g. App\\Models\\Contacts'],
        ];
    }

    public function handle(): int
    {
        try {
            $args = $this->getValidatedArguments();
        } catch (RuntimeException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        $slug = Str::kebab($args['name']);

        if ($this->isRegistered($slug)) {
            $this->components->error('Collection "'.$slug.'" is already registered.');

            return self::INVALID;
        }

        $needsController = $args['index'] || $args['items'];

        $collisions = array_values(array_filter(
            $this->plannedPaths($args['name'], $needsController),
            fn (string $path): bool => file_exists($path),
        ));

        if ($collisions !== []) {
            $this->components->error(
                "Cannot scaffold: the following files already exist:\n  - ".implode("\n  - ", $collisions)
            );

            return self::INVALID;
        }

        $configPath = config_path('content-collections.php');
        $configSnapshot = @file_get_contents($configPath);
        if ($configSnapshot === false) {
            $this->components->error('Unable to read config file at '.$configPath);

            return self::FAILURE;
        }

        $created = [];

        try {
            $this->generateCollection($args['name'], $slug, $args['model'], $args['index'], $args['items'], $created);

            if ($needsController) {
                $this->generateController($args['name'], $args['index'], $args['items'], $created);
            }

            $this->addCollectionToConfig($slug, $args['name'], $configPath, $configSnapshot);

            $this->components->success('🎉  New collection scaffolded successfully!');

            $withFilament = $args['filament'] || (
                $this->input->isInteractive() &&
                confirm('Scaffold a Filament resource for this collection?', default: false)
            );

            if ($withFilament) {
                $this->scaffoldFilamentResource($args['name']);
            }

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->rollback($created, $configPath, $configSnapshot);
            $this->components->error('Scaffolding failed and was rolled back: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * @return list<string>
     */
    private function plannedPaths(string $name, bool $needsController): array
    {
        $paths = [
            base_path(self::COLLECTIONS_DIRECTORY."/{$name}Collection.php"),
        ];

        if ($needsController) {
            $paths[] = app_path("Http/Controllers/{$name}Controller.php");
        }

        return $paths;
    }

    /**
     * @param  list<string>  $created
     */
    private function generateCollection(
        string $name,
        string $slug,
        string $modelFqcn,
        bool $withIndex,
        bool $withItems,
        array &$created,
    ): void {
        $path = base_path(self::COLLECTIONS_DIRECTORY."/{$name}Collection.php");
        $modelClass = class_basename($modelFqcn);

        $implementsList = array_filter([
            $withIndex ? 'HasIndexPage' : null,
            $withItems ? 'HasItemPages' : null,
        ]);

        $implements = $implementsList !== [] ? ' implements '.implode(', ', $implementsList) : '';

        $controllerMethods = ($withIndex || $withItems) ? $this->buildControllerMethodsBlock($name) : '';
        $indexMethods = $withIndex ? $this->buildIndexMethodsBlock($slug) : '';
        $itemMethods = $withItems ? $this->buildItemMethodsBlock($slug) : '';

        $this->writeFileFromStub($path, 'content-collection', [
            'name' => $name,
            'modelFqcn' => $modelFqcn,
            'modelClass' => $modelClass,
            'implements' => $implements,
            'controllerMethods' => $controllerMethods,
            'indexMethods' => $indexMethods,
            'itemMethods' => $itemMethods,
        ], "collection {$name}Collection");

        $created[] = $path;
    }

    /**
     * @param  list<string>  $created
     */
    private function generateController(string $name, bool $withIndex, bool $withItems, array &$created): void
    {
        $path = app_path("Http/Controllers/{$name}Controller.php");

        $indexMethod = $withIndex ? $this->buildControllerIndexMethod() : '';
        $itemMethod = $withItems ? $this->buildControllerItemMethod() : '';

        $this->writeFileFromStub($path, 'content-collection-controller', [
            'name' => $name,
            'indexMethod' => $indexMethod,
            'itemMethod' => $itemMethod,
        ], "controller {$name}Controller");

        $created[] = $path;
    }

    private function scaffoldFilamentResource(string $name): void
    {
        $exitCode = Artisan::call('make:filament-resource', [
            'name' => $name,
            '--no-interaction' => true,
        ]);

        if ($exitCode !== 0) {
            $this->components->warn(
                "make:filament-resource failed (exit {$exitCode}):\n".trim(Artisan::output()).
                "\nRun it manually: php artisan make:filament-resource {$name}"
            );

            return;
        }

        $this->components->success("Scaffolded Filament resource {$name}Resource");
    }

    private function buildControllerMethodsBlock(string $name): string
    {
        return $this->hydrateStub($this->getStubPath('collection-controller-methods'), [
            'name' => $name,
        ]);
    }

    private function buildIndexMethodsBlock(string $slug): string
    {
        return $this->hydrateStub($this->getStubPath('collection-index-methods'), [
            'slug' => $slug,
        ]);
    }

    private function buildItemMethodsBlock(string $slug): string
    {
        return $this->hydrateStub($this->getStubPath('collection-item-methods'), [
            'itemSlug' => Str::singular($slug),
        ]);
    }

    private function buildControllerIndexMethod(): string
    {
        return $this->hydrateStub($this->getStubPath('collection-controller-index-method'), []);
    }

    private function buildControllerItemMethod(): string
    {
        return $this->hydrateStub($this->getStubPath('collection-controller-item-method'), []);
    }

    /**
     * @param  array<string, string>  $placeholders
     */
    private function writeFileFromStub(string $targetPath, string $stubSlug, array $placeholders, string $label): void
    {
        $this->files->ensureDirectoryExists(dirname($targetPath));

        $content = $this->hydrateStub($this->getStubPath($stubSlug), $placeholders);
        $this->files->put($targetPath, $content);
        $this->components->success("Generated {$label}");
    }

    private function getStubPath(string $stubSlug): string
    {
        $path = base_path("stubs/content-collections/{$stubSlug}.stub");

        if (! file_exists($path)) {
            throw new RuntimeException("Unable to find stub: {$stubSlug}.");
        }

        return $path;
    }

    /**
     * @param  array<string, string>  $data
     */
    private function hydrateStub(string $stubPath, array $data): string
    {
        $content = @file_get_contents($stubPath);

        if ($content === false) {
            throw new RuntimeException("Unable to read stub file at: {$stubPath}");
        }

        if ($content === '') {
            throw new RuntimeException("Stub file is empty: {$stubPath}");
        }

        foreach ($data as $placeholder => $value) {
            $content = str_replace("{{{$placeholder}}}", $value, $content);
        }

        return $content;
    }

    private function addCollectionToConfig(
        string $slug,
        string $name,
        string $configPath,
        string $configContents,
    ): void {
        $pattern = '/^([ \t]*)'.preg_quote(self::CONFIG_LOCATOR, '/').'$/m';

        if (preg_match($pattern, $configContents, $match) !== 1) {
            throw new RuntimeException('Unable to write config: no end-of-collections locator found.');
        }

        $indent = $match[1];
        $fqcn = '\\App\\ContentCollections\\'.$name.'Collection';
        $locator = self::CONFIG_LOCATOR;

        $block = <<<CONFIG
        '{$slug}' => {$fqcn}::class,
        {$locator}
        CONFIG;

        $indentedBlock = preg_replace('/^/m', $indent, $block);
        if ($indentedBlock === null) {
            throw new RuntimeException('Unable to apply indentation to config block.');
        }

        $newContents = str_replace($indent.$locator, $indentedBlock, $configContents);

        if ($this->files->put($configPath, $newContents) === false) {
            throw new RuntimeException('Failed to write updated config file at '.$configPath);
        }

        $this->components->success('Registered collection in config file');
    }

    /**
     * @param  list<string>  $created
     */
    private function rollback(array $created, string $configPath, string $configSnapshot): void
    {
        foreach ($created as $path) {
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        if (@file_get_contents($configPath) !== $configSnapshot) {
            @file_put_contents($configPath, $configSnapshot);
        }
    }

    private function isRegistered(string $slug): bool
    {
        return array_key_exists($slug, config('content-collections.collections', []));
    }

    /**
     * @return array{name: string, model: string, index: bool, items: bool, filament: bool}
     *
     * @throws RuntimeException
     */
    private function getValidatedArguments(): array
    {
        $name = (string) $this->argument('name');
        if (preg_match(self::NAME_PATTERN, $name) !== 1) {
            throw new RuntimeException(
                "Invalid collection name: \"{$name}\". Must be PascalCase (e.g. Contacts, Services)."
            );
        }

        $model = (string) $this->argument('model');
        if ($model === '') {
            throw new RuntimeException(
                'Model is required. Provide the fully-qualified model class (e.g. App\Models\Contact).'
            );
        }

        if (! class_exists($model)) {
            throw new RuntimeException(
                "Model class \"{$model}\" could not be found. Ensure it exists and is autoloadable."
            );
        }

        if (! is_subclass_of($model, Model::class)) {
            throw new RuntimeException(
                "Model class \"{$model}\" must extend Illuminate\\Database\\Eloquent\\Model."
            );
        }

        return [
            'name' => $name,
            'model' => $model,
            'index' => (bool) $this->option('index'),
            'items' => (bool) $this->option('items'),
            'filament' => (bool) $this->option('filament'),
        ];
    }
}
