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
        '{--controller : Generate a controller with index() and show() methods} '.
        '{--filament : Scaffold a Filament resource via make:filament-resource}';

    protected $description = 'Registers a content collection and optionally scaffolds a controller and Filament resource.';

    private const string NAME_PATTERN = '/^[A-Z][A-Za-z0-9]*$/';

    private const string CONFIG_LOCATOR = '// @collections-end [DO NOT TOUCH]';

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
            'model' => ['What is the fully-qualified model class?', 'E.g. App\Models\Contact'],
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

        if ($args['controller'] && file_exists(app_path("Http/Controllers/{$args['name']}Controller.php"))) {
            $this->components->error("Cannot scaffold: app/Http/Controllers/{$args['name']}Controller.php already exists.");

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
            if ($args['controller']) {
                $this->generateController($args['name'], $created);
            }

            $this->addToConfig($slug, $args['model'], $configPath, $configSnapshot);

            $this->components->success('🎉  Collection registered successfully!');

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
     * @param  list<string>  $created
     */
    private function generateController(string $name, array &$created): void
    {
        $path = app_path("Http/Controllers/{$name}Controller.php");

        $this->files->ensureDirectoryExists(dirname($path));
        $content = $this->hydrateStub($this->getStubPath('content-collection-controller'), ['name' => $name]);
        $this->files->put($path, $content);

        $this->components->success("Generated controller {$name}Controller");
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

    private function addToConfig(string $slug, string $modelFqcn, string $configPath, string $configContents): void
    {
        $pattern = '/^([ \t]*)'.preg_quote(self::CONFIG_LOCATOR, '/').'$/m';

        if (preg_match($pattern, $configContents, $match) !== 1) {
            throw new RuntimeException('Unable to write config: no end-of-collections locator found.');
        }

        $indent = $match[1];
        $locator = self::CONFIG_LOCATOR;

        $block = "'{$slug}' => \\{$modelFqcn}::class,\n{$indent}{$locator}";

        $newContents = str_replace($indent.$locator, $indent.$block, $configContents);

        if ($this->files->put($configPath, $newContents) === false) {
            throw new RuntimeException('Failed to write updated config file at '.$configPath);
        }

        $this->components->success('Registered collection in config');
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
     * @return array{name: string, model: string, controller: bool, filament: bool}
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
            'controller' => (bool) $this->option('controller'),
            'filament' => (bool) $this->option('filament'),
        ];
    }
}
