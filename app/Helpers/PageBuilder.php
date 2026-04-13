<?php

namespace App\Helpers;

use App\Contracts\SectionSchema;
use App\Contracts\SectionTemplate;
use App\Exceptions\PageBuilderSectionSchemaResolutionException;
use Filament\Forms\Components\Builder\Block;
use Illuminate\Support\Facades\Config;

final class PageBuilder
{
    /**
     * @return string[]
     */
    public static function getSectionSlugs(): array
    {
        return array_keys(Config::array('page-builder.sections'));
    }

    /**
     * @return class-string<SectionSchema>
     *
     * @throws PageBuilderSectionSchemaResolutionException
     */
    public static function sectionSchema(string $section): string
    {
        return config("page-builder.sections.{$section}.schema")
            ?? throw new PageBuilderSectionSchemaResolutionException("Cannot resolve schema for section {$section}");
    }

    /**
     * @return class-string<SectionTemplate>|null
     */
    public static function sectionTemplate(string $section): ?string
    {
        return config("page-builder.sections.{$section}.template");
    }

    /**
     * Filament Builder blocks for every registered, non-disabled, non-deprecated section.
     * Deprecated sections are excluded so they can't be added to new pages,
     * but remain resolvable via sectionSchema() for already-stored content.
     *
     * @return array<Block>
     *
     * @throws PageBuilderSectionSchemaResolutionException
     */
    public static function blocks(): array
    {
        $blocks = [];

        foreach (self::getSectionSlugs() as $slug) {
            if (self::isDisabled($slug) || self::isDeprecated($slug)) {
                continue;
            }

            /** @var class-string<SectionSchema> $schema */
            $schema = self::sectionSchema($slug);
            $blocks[] = $schema::make();
        }

        return $blocks;
    }

    public static function isValidSection(string $section): bool
    {
        return in_array($section, self::getSectionSlugs());
    }

    public static function isDisabled(string $section): bool
    {
        return in_array($section, Config::array('page-builder.disabled'), strict: true);
    }

    public static function isDeprecated(string $section): bool
    {
        return in_array($section, Config::array('page-builder.deprecated'), strict: true);
    }
}
