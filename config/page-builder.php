<?php

use App\Filament\PageBuilder\Sections\AboutSectionSchema;
use App\Filament\PageBuilder\Sections\DanceSectionSchema;
use App\Filament\PageBuilder\Sections\GallerySectionSchema;
use App\Filament\PageBuilder\Sections\HeroSectionSchema;
use App\View\PageBuilder\Sections\AboutSectionTemplate;
use App\View\PageBuilder\Sections\DanceSectionTemplate;
use App\View\PageBuilder\Sections\GallerySectionTemplate;
use App\View\PageBuilder\Sections\HeroSectionTemplate;

return [
    /*
    |--------------------------------------------------------------------------
    | Registered PageBuilder Sections
    |--------------------------------------------------------------------------
    |
    | Map of section slug to its handler classes. The slug is persisted in the
    | page's JSON content (`['type' => slug, 'data' => ...]`) and used by the
    | admin and frontend to resolve the section.
    |
    | - schema:   class-string<App\Contracts\SectionSchema> — Filament Builder block
    | - template: class-string<App\Contracts\SectionTemplate>|null — frontend renderer
    | - resource: reserved for headless (API) rendering; omit for now
    |
    | Example:
    |   'hero' => [
    |       'schema'   => \App\Filament\PageBuilder\Sections\HeroSectionSchema::class,
    |       'template' => \App\View\PageBuilder\Sections\HeroSectionTemplate::class,
    |   ],
    |
    */
    'sections' => [
        'hero' => [
            'schema' => HeroSectionSchema::class,
            'template' => HeroSectionTemplate::class,
        ],
        'about' => [
            'schema' => AboutSectionSchema::class,
            'template' => AboutSectionTemplate::class,
        ],
        'gallery' => [
            'schema' => GallerySectionSchema::class,
            'template' => GallerySectionTemplate::class,
        ],
        'dance' => [
            'schema' => DanceSectionSchema::class,
            'template' => DanceSectionTemplate::class,
        ],
        // @sections-end [DO NOT TOUCH]
    ],

    /*
    |--------------------------------------------------------------------------
    | Disabled Sections (kill-switch)
    |--------------------------------------------------------------------------
    |
    | Slugs listed here are hidden from the admin add-picker AND skipped during
    | frontend render. Use when a section has a breaking bug or exploit and
    | you need to take it offline without touching stored page content.
    |
    | Always leave a comment explaining why a section is disabled.
    |
    */
    'disabled' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Deprecated Sections
    |--------------------------------------------------------------------------
    |
    | Slugs listed here are hidden from the admin add-picker but still render
    | on the frontend. Use when a section has been superseded and shouldn't be
    | added to new pages, while existing pages using it continue to work.
    |
    | Always leave a comment explaining the reason for deprecation.
    |
    */
    'deprecated' => [
        //
    ],

];
