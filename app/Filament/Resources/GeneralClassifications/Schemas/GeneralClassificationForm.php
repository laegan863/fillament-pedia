<?php

namespace App\Filament\Resources\GeneralClassifications\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GeneralClassificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('General Classification')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
            ]);
    }
}
