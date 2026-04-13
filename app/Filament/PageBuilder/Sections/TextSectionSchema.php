<?php

namespace App\Filament\PageBuilder\Sections;

use App\Concerns\BuildsSectionSchema;
use App\Contracts\SectionSchema;
use Filament\Forms\Components\RichEditor;
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
        return 'Text block';
    }

    public static function getIcon(): Heroicon
    {
        return Heroicon::Bars3BottomLeft;
    }

    public static function getSchema(): array
    {
        return [
            RichEditor::make('content')
                ->label('Content')
                ->extraInputAttributes(['class' => 'min-h-48'])
                ->toolbarButtons([
                    ['bold', 'italic', 'underline', 'link'],
                    ['h1', 'h2', 'h3'],
                    ['bulletList', 'orderedList'],
                    ['undo', 'redo'],
                ])
                ->columnSpanFull(),
        ];
    }
}
