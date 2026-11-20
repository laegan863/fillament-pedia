<?php

namespace App\Filament\Resources\Signatories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SignatoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Full Name')
                    ->placeholder('e.g. Dr. Juan Dela Cruz, MD')
                    ->required()
                    ->maxLength(255),
                TextInput::make('position')
                    ->label('Position')
                    ->placeholder('e.g. Cancer Coordinator / Cancer Center Head')
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label('Active')
                    ->helperText('Only active signatories appear in the "Noted By" dropdown when generating reports.')
                    ->default(true),
            ]);
    }
}
