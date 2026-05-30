<?php

namespace App\Filament\PageBuilder\Sections;

use App\Concerns\BuildsSectionSchema;
use App\Contracts\SectionSchema;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;

final class TextSectionSchema implements SectionSchema
{
    use BuildsSectionSchema;

    public static function getSlug(): string
    {
        return 'text';
    }

    public static function getLabel(): string
    {
        return __('sections/text.label');
    }

    public static function getIcon(): Heroicon
    {
        return Heroicon::DocumentText;
    }

    public static function getSchema(): array
    {
        return [
            TextInput::make('tagline')
                ->label(__('sections/text.tagline'))
                ->columnSpanFull(),

            TextInput::make('heading')
                ->label(__('sections/text.heading'))
                ->required()
                ->columnSpanFull(),

            RichEditor::make('body')
                ->label(__('sections/text.body'))
                ->toolbarButtons([
                    'bold', 'italic', 'underline', 'strike',
                    'link', 'blockquote',
                    'bulletList', 'orderedList',
                    'h2', 'h3',
                    'table',
                ])
                ->columnSpanFull(),
        ];
    }
}
