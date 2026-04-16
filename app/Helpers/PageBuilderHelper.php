<?php

namespace App\Helpers;

use App\Contracts\SectionTemplate;
use App\Exceptions\PageBuilderSectionRenderException;
use App\Exceptions\PageBuilderSectionResolutionException;
use Filament\Forms\Components\Builder\Block;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Livewire\Component as LivewireComponent;
use Illuminate\View\Component as BladeComponent;

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
     * @param string $section kebab-case slug of section
     * @param array<string, mixed> $data
     *
     * @return View
     * @throws PageBuilderSectionResolutionException Unable to find section in config registry
     * @throws PageBuilderSectionRenderException Section does not support rendering
     */
    public static function renderSection(string $section, array $data): View
    {
        if(! self::isValidSection($section)) {
            throw new PageBuilderSectionResolutionException("Unable to render section '{$section}'.");
        }

        if(! self::sectionTemplate($section)) {
            throw new PageBuilderSectionRenderException("Section '{$section}' has no associated frontend template.");
        }

        $templateClass = self::sectionTemplate($section);
        $template = app(self::sectionTemplate($section));

//        return match()
        switch($template) {
            case is_subclass_of($templateClass, LivewireComponent::class):
                echo 'livewire';
                break;
            case is_subclass_of($templateClass, BladeComponent::class):
                echo 'blade';
                break;
            default:
                throw new PageBuilderSectionRenderException("Section {$section} has no defined hydration strategy");
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
