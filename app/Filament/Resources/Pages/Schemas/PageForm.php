<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Helpers\CmsSectionsHelper;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->aside()
                    ->heading()
                    ->description('Page Configuration')
                    ->columns(1)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->unique(table: 'pages', column: 'slug', ignoreRecord: true)
                            ->placeholder('Keep empty for homepage'),
                        Toggle::make('is_published')
                            ->inline(),
                    ]),
                Builder::make('content')
                    ->blocks(CmsSectionsHelper::blocks())
                    ->collapsible()
                    ->blockNumbers(false)
                    ->columnSpanFull(),
            ]);
    }
}
