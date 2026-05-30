<?php

namespace App\Filament\Components;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

/**
 * A reusable button editor that bundles a text field and a link picker.
 *
 * Usage in a schema:
 *   ButtonInput::make('button_primary', __('section-foo.button_primary'))
 *
 * Stored shape: { text: string, link: { type, url } }
 * The link key is resolved via LinkInput::resolve() in the section template.
 */
final class ButtonInput
{
    /**
     * Returns a Section containing a text input and a LinkInput.
     * Does not set columnSpanFull so the parent grid controls placement.
     */
    public static function make(string $name, string $label = ''): Section
    {
        return Section::make($label)
            // Lift the background one step on both sides of the color scale:
            ->extraAttributes(['class' => '[&_.fi-section]:bg-gray-100! dark:[&_.fi-section]:bg-gray-800!'])
            ->schema([
                TextInput::make("{$name}.text")
                    ->label(__('components/button.text_label'))
                    ->required(),

                LinkInput::make("{$name}.link"),
            ]);
    }
}
