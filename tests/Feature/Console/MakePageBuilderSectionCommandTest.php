<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->configPath = config_path('page-builder.php');
    $this->configBackup = File::get($this->configPath);
    $this->trackedFiles = [];
});

afterEach(function () {
    File::put($this->configPath, $this->configBackup);

    foreach ($this->trackedFiles as $path) {
        if (File::exists($path)) {
            File::delete($path);
        }
    }
});

function trackFiles($ctx, array $paths): void
{
    $ctx->trackedFiles = array_merge($ctx->trackedFiles ?? [], $paths);
}

function sectionPaths(string $name): array
{
    $slug = Str::kebab($name);

    return [
        'schema' => base_path("app/Filament/PageBuilder/Sections/{$name}SectionSchema.php"),
        'template' => base_path("app/View/PageBuilder/Sections/{$name}SectionTemplate.php"),
        'view' => base_path("resources/views/page-builder/sections/{$slug}.blade.php"),
        'livewireClass' => app_path("Livewire/{$name}.php"),
        'livewireView' => resource_path("views/livewire/{$slug}.blade.php"),
        'livewireTest' => base_path("tests/Feature/Livewire/{$name}Test.php"),
    ];
}

it('scaffolds a blade section end-to-end', function () {
    $paths = sectionPaths('FixtureBlade');
    trackFiles($this, array_values($paths));

    $this->artisan('make:cms-section', [
        'name' => 'FixtureBlade',
        '--type' => 'blade',
    ])->assertExitCode(0);

    expect(File::exists($paths['schema']))->toBeTrue();
    expect(File::exists($paths['template']))->toBeTrue();
    expect(File::exists($paths['view']))->toBeTrue();

    $config = File::get($this->configPath);
    expect($config)->toContain("'fixture-blade' => [");
    expect($config)->toContain('\\App\\Filament\\PageBuilder\\Sections\\FixtureBladeSectionSchema::class');
    expect($config)->toContain('\\App\\View\\PageBuilder\\Sections\\FixtureBladeSectionTemplate::class');
    expect($config)->toContain('// @sections-end [DO NOT TOUCH]');
});

it('scaffolds a livewire section including the livewire component', function () {
    $paths = sectionPaths('FixtureLive');
    trackFiles($this, array_values($paths));

    $this->artisan('make:cms-section', [
        'name' => 'FixtureLive',
        '--type' => 'livewire',
    ])->assertExitCode(0);

    expect(File::exists($paths['schema']))->toBeTrue();
    expect(File::exists($paths['template']))->toBeTrue();
    expect(File::exists($paths['view']))->toBeTrue();
    expect(File::exists($paths['livewireClass']))->toBeTrue();
    expect(File::exists($paths['livewireView']))->toBeTrue();
    expect(File::exists($paths['livewireTest']))->toBeFalse();
});

it('scaffolds a livewire section with a test file when --test is passed', function () {
    $paths = sectionPaths('FixtureLiveT');
    trackFiles($this, array_values($paths));

    $this->artisan('make:cms-section', [
        'name' => 'FixtureLiveT',
        '--type' => 'livewire',
        '--test' => true,
    ])->assertExitCode(0);

    expect(File::exists($paths['livewireClass']))->toBeTrue();
    expect(File::exists($paths['livewireTest']))->toBeTrue();
});

it('scaffolds a none-type section with template null in config', function () {
    $paths = sectionPaths('FixtureNone');
    trackFiles($this, array_values($paths));

    $this->artisan('make:cms-section', [
        'name' => 'FixtureNone',
        '--type' => 'none',
    ])->assertExitCode(0);

    expect(File::exists($paths['schema']))->toBeTrue();
    expect(File::exists($paths['template']))->toBeFalse();
    expect(File::exists($paths['view']))->toBeFalse();

    $config = File::get($this->configPath);
    expect($config)->toContain("'fixture-none' => [");
    expect($config)->toContain("'template' => null,");
});

it('rejects a non-PascalCase name', function () {
    $this->artisan('make:cms-section', [
        'name' => 'fixtureBad',
        '--type' => 'none',
    ])->assertExitCode(1);
});

it('rejects when slug is already registered in config', function () {
    $paths = sectionPaths('FixtureDup');
    trackFiles($this, array_values($paths));

    $this->artisan('make:cms-section', [
        'name' => 'FixtureDup',
        '--type' => 'none',
    ])->assertExitCode(0);

    $this->artisan('make:cms-section', [
        'name' => 'FixtureDup',
        '--type' => 'none',
    ])->assertExitCode(2);
});

it('rejects when a target file already exists on disk', function () {
    $paths = sectionPaths('FixturePre');
    trackFiles($this, array_values($paths));

    File::ensureDirectoryExists(dirname($paths['schema']));
    File::put($paths['schema'], '<?php // placeholder');

    $this->artisan('make:cms-section', [
        'name' => 'FixturePre',
        '--type' => 'none',
    ])->assertExitCode(2);

    expect(File::get($paths['schema']))->toBe('<?php // placeholder');
});

it('rolls back files and config when the locator is missing', function () {
    $paths = sectionPaths('FixtureRoll');
    trackFiles($this, array_values($paths));

    $tampered = str_replace(
        '// @sections-end [DO NOT TOUCH]',
        '// locator-removed-for-test',
        $this->configBackup,
    );
    File::put($this->configPath, $tampered);

    $this->artisan('make:cms-section', [
        'name' => 'FixtureRoll',
        '--type' => 'blade',
    ])->assertExitCode(1);

    expect(File::exists($paths['schema']))->toBeFalse();
    expect(File::exists($paths['template']))->toBeFalse();
    expect(File::exists($paths['view']))->toBeFalse();
    expect(File::get($this->configPath))->toBe($tampered);
});

it('fails gracefully in non-interactive mode when --type is omitted', function () {
    $this->artisan('make:cms-section', [
        'name' => 'FixtureNoType',
        '--no-interaction' => true,
    ])->assertExitCode(1);
});

it('rejects an unknown --type value', function () {
    $this->artisan('make:cms-section', [
        'name' => 'FixtureBadType',
        '--type' => 'vue',
    ])->assertExitCode(1);
});
