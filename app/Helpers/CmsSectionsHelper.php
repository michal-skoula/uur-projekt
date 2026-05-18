<?php

namespace App\Helpers;

use App\Contracts\SectionSchema;
use App\Contracts\SectionTemplate;
use App\Exceptions\PageBuilderSectionRenderException;
use App\Exceptions\PageBuilderSectionResolutionException;
use Filament\Forms\Components\Builder\Block;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\HtmlString;
use Livewire\Component as LivewireComponent;
use Livewire\Livewire;

final class PageBuilderHelper
{
    /**
     * @return string[]
     */
    public static function getSectionSlugs(): array
    {
        return array_keys(Config::array('page-builder.sections'));
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws PageBuilderSectionResolutionException
     * @throws PageBuilderSectionRenderException
     */
    public static function renderSection(string $section, array $data): View|HtmlString
    {
        if (! self::isValidSection($section)) {
            throw new PageBuilderSectionResolutionException("Unable to render section '{$section}'.");
        }

        $templateClass = self::sectionTemplate($section);

        if ($templateClass === null) {
            throw new PageBuilderSectionRenderException("Section '{$section}' has no associated frontend template.");
        }

        // Rendering for each type of frontend SectionTemplate
        if(is_subclass_of($templateClass, LivewireComponent::class)) {
            return new HtmlString(Livewire::mount($templateClass, ['data' => $data]));
        }
        else if(is_subclass_of($templateClass, SectionTemplate::class)) {
            return app($templateClass)->prepareData($data)->render();
        }
        else {
            throw new PageBuilderSectionRenderException("Section '{$section}' has no defined rendering strategy.");
        }
    }

    /**
     * @return class-string<SectionSchema>
     *
     * @throws PageBuilderSectionResolutionException
     */
    public static function sectionSchema(string $section): string
    {
        return config("page-builder.sections.{$section}.schema")
            ?? throw new PageBuilderSectionResolutionException("Cannot resolve schema for section {$section}");
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
     * @throws PageBuilderSectionResolutionException
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
