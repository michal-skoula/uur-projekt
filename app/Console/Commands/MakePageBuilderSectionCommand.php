<?php

namespace App\Console\Commands;

use App\Enums\PageBuilderSectionTemplateType as SectionTemplateType;
use App\Helpers\PageBuilder;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use RuntimeException;

use function Laravel\Prompts\select;

class MakePageBuilderSectionCommand extends Command implements PromptsForMissingInput
{
    protected $signature =
        'make:cms-section '.
        '{name : UpperCamelCase name of the section} '.
        '{slug : Kebab-case slug of the section} '.
        '{--t|type= : Type of frontend template rendering the section data} '.
        '{--T|test : Generate a test file alongside the Livewire component (livewire type only)}';

    protected $description = 'Scaffolds a new CMS section and registers it in the page-builder config file.';

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
            'name' => ['What is the UpperCamelCase name of this section?', 'E.g. Hero, NewsFeature'],
            'slug' => ['What is the kebab-case slug of this section?', 'E.g. hero, news-feature'],
        ];
    }

    public function handle(): int
    {
        try {
            $args = $this->getValidatedArguments();

            if (PageBuilder::isValidSection($args['slug'])) {
                $this->components->error('Section "'.$args['slug'].'" already exists.');

                return 2;
            }

            $schemaClassString = $this->generateSchema($args['name'], $args['slug']);

            $templateClassString = match ($args['type']) {
                SectionTemplateType::BLADE => $this->generateBladeTemplate($args['name'], $args['slug']),
                SectionTemplateType::LIVEWIRE => $this->generateLiveWireTemplate($args['name'], $args['slug'], $args['test']),
                SectionTemplateType::NONE => null,
            };

            $this->addSectionToConfig($args['slug'], $schemaClassString, $templateClassString);

            $this->components->success('🎉  New section scaffolded successfully!');
        } catch (RuntimeException $e) {
            $this->components->error($e->getMessage());

            return 1;
        }

        return 0;
    }

    /**
     * Generates scaffolding for the section backend (filament schema)
     */
    private function generateSchema(string $name, string $slug): string
    {
        $schemaPath = base_path("{$this->schemasDirectory}/{$name}SectionSchema.php");

        $this->writeFileFromStub($schemaPath, 'section-schema', [
            'name' => $name,
            'slug' => $slug,
        ], 'section schema');

        return $this->getFullyQualifiedClassNameFromPath($schemaPath);
    }

    /**
     * Generates scaffolding for the `blade` frontend option.
     */
    private function generateBladeTemplate(string $name, string $slug): string
    {
        $classPath = base_path($this->templatesDirectory.'/'.$name.'SectionTemplate.php');
        $viewPath = base_path($this->viewsDirectory.'/'.$slug.'.blade.php');

        $this->writeFileFromStub($classPath, 'section-template-blade', [
            'name' => $name,
            'slug' => $slug,
        ], "template {$name}SectionTemplate");

        $this->writeFileFromStub($viewPath, 'section-view-blade', [
            'name' => $name,
            'slug' => $slug,
        ], "view {$slug}.blade.php");

        return $this->getFullyQualifiedClassNameFromPath($classPath);
    }

    /**
     * Generates scaffolding for the `livewire` frontend option.
     */
    private function generateLiveWireTemplate(string $name, string $slug, bool $withTest): string
    {
        $classPath = base_path($this->templatesDirectory.'/'.$name.'SectionTemplate.php');
        $viewPath = base_path($this->viewsDirectory.'/'.$slug.'.blade.php');

        $this->writeFileFromStub($classPath, 'section-template-livewire', [
            'name' => $name,
            'slug' => $slug,
        ], "template {$name}SectionTemplate");

        $this->writeFileFromStub($viewPath, 'section-view-livewire', [
            'slug' => $slug,
            'livewireTag' => Str::kebab($name),
        ], "view {$slug}.blade.php");

        Artisan::call('make:livewire', [
            'name' => $name,
            '--test' => $withTest,
            '--quiet' => true,
        ]);

        $label = $withTest ? "Livewire component & test file {$name}" : "Livewire component {$name}";
        $this->components->success("👍  Generated {$label}");

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

        if (file_exists($targetPath)) {
            $this->components->warn("{$label} already exists at {$targetPath}, skipping.");

            return;
        }

        $content = $this->hydrateStub($this->getStubsPath($stubSlug), $placeholders);
        $this->files->put($targetPath, $content);
        $this->components->success("👍  Generated {$label}");
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
            ['/', '.php'],
            ['\\', ''],
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
     * @throws RuntimeException Throws when no stub file is found at the location.
     */
    private function hydrateStub(string $stub_path, array $data): string
    {
        $stubContent = file_get_contents($stub_path);

        if ($stubContent === false || ! file_exists($stub_path)) {
            throw new RuntimeException('No stub file found at: '.$stub_path);
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
        $sectionName = $this->argument('name');
        if (! $sectionName) {
            throw new RuntimeException('Invalid or missing section name.');
        }

        $sectionSlug = $this->argument('slug');
        if (! $sectionSlug) {
            throw new RuntimeException('Invalid or missing section slug.');
        }

        $sectionType = $this->option('type') ?:
            select('What should the Section\'s frontend use?', SectionTemplateType::getCommandOptions());

        if (! $sectionType) {
            throw new RuntimeException('Invalid or missing section type.');
        }

        $type = SectionTemplateType::tryFrom($sectionType);
        if ($type === null) {
            throw new RuntimeException("Unknown section type: $sectionType, allowed options: ".
                implode(', ', SectionTemplateType::values()));
        }

        return [
            'name' => $sectionName,
            'slug' => $sectionSlug,
            'type' => $type,
            'test' => (bool) $this->option('test'),
        ];
    }

    private function addSectionToConfig(string $slug, string $schemaFqcn, ?string $templateFqcn = null): void
    {
        $locator = '// @sections-end [DO NOT TOUCH]';
        $path = config_path('page-builder.php');
        $config = file_get_contents($path);

        if (! $config) {
            throw new RuntimeException('Unable to read config file.');
        }

        // Note: the indentation here is mangled intentionally.
        // first line works properly, then it seems to go from the start of the line instead of
        // same indentation as line above, meaning that its two tabs off. This solution offsets
        // that issue, but is quite brittle. This is an acceptable tradeoff
        $configMarkup = <<<CONFIG
            '{$slug}' => [
                        'schema' => {$this->toClassString($schemaFqcn)},
                        'template' => {$this->toClassString($templateFqcn)},
                    ],
                    $locator
            CONFIG;

        if (! str_contains($config, $locator)) {
            throw new RuntimeException('Unable write config: no end-of-section locator found.');
        }

        $this->files->put($path, str_replace($locator, $configMarkup, $config));
    }

    /**
     * Appends ::class to valid fqcn or returns back null.
     */
    private function toClassString(?string $fqcn): ?string
    {
        return ! $fqcn ? null : $fqcn.'::class';
    }
}
