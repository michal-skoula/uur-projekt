<?php

namespace App\Enums;

enum PageBuilderSectionTemplateType: string
{
    case BLADE = 'blade';
    case LIVEWIRE = 'livewire';
    case NONE = 'none';

    /**
     * Returns the `[key => value]` pairs for artisan command
     *
     * @return array<string, string>
     */
    public static function getCommandOptions(): array
    {
        $options = [];

        foreach (PageBuilderSectionTemplateType::cases() as $option) {
            $options[$option->value] = $option->getDescription();
        }

        return $options;
    }

    /**
     * @return string[]
     */
    public static function values(): array
    {
        $values = [];

        foreach (PageBuilderSectionTemplateType::cases() as $option) {
            $values[] = $option->value;
        }

        return $values;
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::BLADE => 'Blade component',
            self::LIVEWIRE => 'Livewire component',
            self::NONE => 'No section template (REST-API only)',
        };
    }
}
