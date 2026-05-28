<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Helpers\CmsSectionsHelper;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('slug'),
                Toggle::make('is_published')
                    ->columnSpanFull(),
                Builder::make('content')
                    ->blocks(CmsSectionsHelper::blocks())
                    ->collapsible()
                    ->blockNumbers(false)
                    ->columnSpanFull(),
            ]);
    }
}
