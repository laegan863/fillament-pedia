<?php

namespace App\Filament\Resources\ParentClassifications\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ParentClassificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Parent Classification')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
            ]);
    }
}
