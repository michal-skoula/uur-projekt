<?php

namespace App\Providers;

use App\Contracts\SectionSchema;
use App\Contracts\SectionTemplate;
use App\Exceptions\PageBuilderException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class PageBuilderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            return;
        }

        $this->validateRegistry();
    }

    /**
     * @throws PageBuilderException
     */
    private function validateRegistry(): void
    {
        foreach (Config::array('page-builder.sections') as $slug => $entry) {
            if (! is_string($slug) || ! is_array($entry)) {
                throw new PageBuilderException("Section registry entry is malformed for key: {$slug}");
            }

            $schema = $entry['schema'] ?? null;
            if (! is_string($schema) || ! is_subclass_of($schema, SectionSchema::class)) {
                throw new PageBuilderException("Section '{$slug}' has an invalid schema: ".var_export($schema, true));
            }

            $template = $entry['template'] ?? null;
            if ($template !== null && (! is_string($template) || ! is_subclass_of($template, SectionTemplate::class))) {
                throw new PageBuilderException("Section '{$slug}' has an invalid template: ".var_export($template, true));
            }
        }
    }
}
