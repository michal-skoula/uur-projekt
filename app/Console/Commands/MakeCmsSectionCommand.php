<?php

namespace App\Console\Commands;

use App\Enums\PageBuilderSectionTemplateType as SectionTemplateType;
use App\Helpers\CmsSectionsHelper;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

use function Laravel\Prompts\select;

class MakePageBuilderSectionCommand extends Command implements PromptsForMissingInput
{
    protected $signature =
        'make:cms-section '.
        '{name : PascalCase name of the section (e.g. Hero, NewsFeature)} '.
        '{--t|type= : Type of frontend template rendering the section data} '.
        '{--T|test : Generate a test file alongside the Livewire component (livewire type only)}';

    protected $description = 'Scaffolds a new CMS section and registers it in the page-builder config file.';

    private const string NAME_PATTERN = '/^[A-Z][A-Za-z0-9]*$/';

    private const string CONFIG_LOCATOR = '// @sections-end [DO NOT TOUCH]';

    private string $schemasDirectory = 'app/Filament/PageBuilder/Sections';

    private string $templatesDirectory = 'app/View/PageBuilder/Sections';

    private string $viewsDirectory = 'resources/views/page-builder/sections';

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
            'name' => ['What is the PascalCase name of this section?', 'E.g. Hero, NewsFeature'],
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

        if (CmsSectionsHelper::isValidSection($args['slug'])) {
            $this->components->error('Section "'.$args['slug'].'" already exists.');

            return self::INVALID;
        }

        $collisions = array_values(array_filter(
            $this->plannedPaths($args['name'], $args['slug'], $args['type'], $args['test']),
            callback: fn (string $path): bool => file_exists($path),
        ));

        if ($collisions !== []) {
            $this->components->error(
                "Cannot scaffold: the following files already exist:\n  - ".implode("\n  - ", $collisions)
            );

            return self::INVALID;
        }

        $configPath = config_path('page-builder.php');
        $configSnapshot = @file_get_contents($configPath); // note: `@` suppresses all stderr stuff
        if ($configSnapshot === false) {
            $this->components->error('Unable to read config file at '.$configPath);

            return self::FAILURE;
        }

        /**
         * Stores all created FQCNs passed to it via &reference, to be able to roll back later.
         * basically a slightly cleaner way than returning and writing `created[] = ...` every time.
         */
        $created = [];

        try {
            $schemaFqcn = $this->generateSchema($args['name'], $args['slug'], $created);

            $templateFqcn = match ($args['type']) {
                SectionTemplateType::BLADE => $this->generateBladeTemplate($args['name'], $args['slug'], $created),
                SectionTemplateType::LIVEWIRE => $this->generateLiveWireTemplate($args['name'], $args['slug'], $args['test'], $created),
                SectionTemplateType::NONE => null,
            };

            $this->addSectionToConfig($args['slug'], $schemaFqcn, $templateFqcn, $configPath, $configSnapshot);

            $this->components->success('🎉  New section scaffolded successfully!');

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->rollback($created, $configPath, $configSnapshot);
            $this->components->error('Scaffolding failed and was rolled back: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * Full list of paths the command intends to create, used for pre-flight collision check.
     *
     * @return list<string>
     */
    private function plannedPaths(string $name, string $slug, SectionTemplateType $type, bool $withTest): array
    {
        $paths = [
            base_path("{$this->schemasDirectory}/{$name}SectionSchema.php"),
        ];

        if ($type === SectionTemplateType::BLADE) {
            $paths[] = base_path("{$this->templatesDirectory}/{$name}SectionTemplate.php");
            $paths[] = base_path("{$this->viewsDirectory}/{$slug}.blade.php");
        }

        if ($type === SectionTemplateType::LIVEWIRE) {
            $kebab = Str::kebab($name);
            $paths[] = app_path("Livewire/{$name}.php");
            $paths[] = resource_path("views/livewire/{$kebab}.blade.php");
            if ($withTest) {
                $paths[] = base_path("tests/Feature/Livewire/{$name}Test.php");
            }
        }

        return $paths;
    }

    /**
     * Generates scaffolding for the section backend (filament schema).
     *
     * @param  list<string>  $created
     */
    private function generateSchema(string $name, string $slug, array &$created): string
    {
        $schemaPath = base_path("{$this->schemasDirectory}/{$name}SectionSchema.php");

        $this->writeFileFromStub($schemaPath, 'section-schema', [
            'name' => $name,
            'slug' => $slug,
        ], "schema {$name}SectionSchema");

        $created[] = $schemaPath;

        return $this->getFullyQualifiedClassNameFromPath($schemaPath);
    }

    /**
     * Generates scaffolding for the `blade` frontend option.
     *
     * @param  list<string>  $created
     */
    private function generateBladeTemplate(string $name, string $slug, array &$created): string
    {
        $classPath = base_path($this->templatesDirectory.'/'.$name.'SectionTemplate.php');
        $viewPath = base_path($this->viewsDirectory.'/'.$slug.'.blade.php');

        $this->writeFileFromStub($classPath, 'section-template-blade', [
            'name' => $name,
            'slug' => $slug,
        ], "template {$name}SectionTemplate");
        $created[] = $classPath;

        $this->writeFileFromStub($viewPath, 'section-view-blade', [
            'name' => $name,
            'slug' => $slug,
        ], "view {$slug}.blade.php");
        $created[] = $viewPath;

        return $this->getFullyQualifiedClassNameFromPath($classPath);
    }

    /**
     * Generates scaffolding for the `livewire` frontend option.
     *
     * @param  list<string>  $created
     */
    private function generateLiveWireTemplate(string $name, string $slug, bool $withTest, array &$created): string
    {
        $kebab = Str::kebab($name);
        $classPath = app_path("Livewire/{$name}.php");
        $viewPath = resource_path("views/livewire/{$kebab}.blade.php");

        $this->writeFileFromStub($classPath, 'section-template-livewire', [
            'name' => $name,
            'livewireTag' => $kebab,
        ], "component {$name}");
        $created[] = $classPath;

        $this->writeFileFromStub($viewPath, 'section-view-livewire', [
            'name' => $name,
            'slug' => $slug,
        ], "view livewire/{$kebab}.blade.php");
        $created[] = $viewPath;

        if ($withTest) {
            // make:livewire will warn about the existing component but still generates the test file.
            // We use our own stub for the component to include SectionTemplate and prepareData().
            $exitCode = Artisan::call('make:livewire', [
                'name' => $name,
                '--class' => true,
                '--test' => true,
                '--quiet' => true,
            ]);

            if ($exitCode !== 0) {
                throw new RuntimeException(
                    "make:livewire --test failed (exit {$exitCode}):\n".trim(Artisan::output())
                );
            }

            $created[] = base_path("tests/Feature/Livewire/{$name}Test.php");
            $this->components->success("Generated test {$name}Test");
        }

        return $this->getFullyQualifiedClassNameFromPath($classPath);
    }

    /**
     * Hydrates and writes a new file to a location from a predefined stub with placeholders.
     *
     * @param  string  $targetPath  Full system file path from root
     * @param  string  $stubSlug  kebab-case slug from /stubs
     * @param  array<string, string>  $placeholders  `[placeholder_slug => value]` pairs
     * @param  string  $label  What gets displayed on the response message
     */
    private function writeFileFromStub(string $targetPath, string $stubSlug, array $placeholders, string $label): void
    {
        $this->files->ensureDirectoryExists(dirname($targetPath));

        $content = $this->hydrateStub($this->getStubsPath($stubSlug), $placeholders);
        $this->files->put($targetPath, $content);
        $this->components->success("Generated {$label}");
    }

    /**
     * Resolves the fqcn of a file under `app/` (default namespace).
     *
     * @throws RuntimeException when the path is not under app_path().
     */
    private function getFullyQualifiedClassNameFromPath(string $fullPath): string
    {
        $appPath = app_path().DIRECTORY_SEPARATOR;

        if (! Str::endsWith($fullPath, '.php')) {
            throw new RuntimeException(".php file path required for getting the fqcn, $fullPath provided");
        }

        if (! Str::startsWith($fullPath, $appPath)) {
            throw new RuntimeException("Path {$fullPath} is not under ".app_path());
        }

        $relative = Str::after($fullPath, $appPath);

        return app()->getNamespace().str_replace(
            ['/', '\\', '.php'],
            ['\\', '\\', ''],
            $relative
        );
    }

    /**
     * Resolves a stub from a given slug.
     *
     * @param  string  $stubSlug  kebab-case name of the stub under /stubs
     * @return string The system path to the stub.
     */
    private function getStubsPath(string $stubSlug): string
    {
        $path = base_path('stubs/page-builder/'.$stubSlug.'.stub');

        if (! file_exists($path)) {
            throw new RuntimeException("Unable to find stub: $stubSlug.");
        }

        return $path;
    }

    /**
     * @param  array<string, string>  $data  `[placeholder_slug => value]` pairs
     * @return string The hydrated stub.
     *
     * @throws RuntimeException Throws when the stub file is unreadable or empty.
     */
    private function hydrateStub(string $stub_path, array $data): string
    {
        $stubContent = @file_get_contents($stub_path);

        if ($stubContent === false) {
            throw new RuntimeException('Unable to read stub file at: '.$stub_path);
        }

        if ($stubContent === '') {
            throw new RuntimeException('Stub file is empty: '.$stub_path);
        }

        foreach ($data as $placeholder => $value) {
            $stubContent = str_replace("{{{$placeholder}}}", $value, $stubContent);
        }

        return $stubContent;
    }

    /**
     * @return array{name:string,slug:string,type:SectionTemplateType,test:bool}
     *
     * @throws RuntimeException
     */
    private function getValidatedArguments(): array
    {
        $sectionName = (string) $this->argument('name');
        if (preg_match(self::NAME_PATTERN, $sectionName) !== 1) {
            throw new RuntimeException(
                "Invalid section name: \"{$sectionName}\". Must be PascalCase (e.g. Hero, NewsFeature)."
            );
        }

        $sectionSlug = Str::kebab($sectionName);

        $sectionType = $this->option('type');
        if (! $sectionType) {
            if (! $this->input->isInteractive()) {
                throw new RuntimeException(
                    '--type is required in non-interactive mode. Allowed: '.
                    implode(', ', SectionTemplateType::values())
                );
            }

            $sectionType = select(
                'What should the Section\'s frontend use?',
                SectionTemplateType::getCommandOptions(),
            );
        }

        $type = SectionTemplateType::tryFrom($sectionType);
        if ($type === null) {
            throw new RuntimeException(
                "Unknown section type: \"{$sectionType}\". Allowed: ".
                implode(', ', SectionTemplateType::values())
            );
        }

        return [
            'name' => $sectionName,
            'slug' => $sectionSlug,
            'type' => $type,
            'test' => (bool) $this->option('test'),
        ];
    }

    /**
     * Inserts the new section entry above the locator comment in the config file.
     * Detects the locator's leading whitespace to produce correctly-indented output
     * regardless of the config file's indent style.
     */
    private function addSectionToConfig(
        string $slug,
        string $schemaFqcn,
        ?string $templateFqcn,
        string $configPath,
        string $configContents,
    ): void {
        $pattern = '/^([ \t]*)'.preg_quote(self::CONFIG_LOCATOR, '/').'$/m';

        if (preg_match($pattern, $configContents, $match) !== 1) {
            throw new RuntimeException('Unable to write config: no end-of-section locator found.');
        }

        $indent = $match[1];
        $schemaClass = $this->toClassString($schemaFqcn);
        $templateClass = $this->toClassString($templateFqcn);
        $locator = self::CONFIG_LOCATOR;

        $block = <<<CONFIG
        '{$slug}' => [
            'schema' => {$schemaClass},
            'template' => {$templateClass},
        ],
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

        $this->components->success('Registered section in config file');
    }

    /**
     * Appends ::class to a valid fqcn, or returns the literal `null`.
     */
    private function toClassString(?string $fqcn): string
    {
        return $fqcn === null ? 'null' : '\\'.ltrim($fqcn, '\\').'::class';
    }

    /**
     * Deletes any files created during this run and restores the config file.
     *
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
}
