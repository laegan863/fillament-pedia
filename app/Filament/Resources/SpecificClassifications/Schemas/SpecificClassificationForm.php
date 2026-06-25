<?php

namespace App\Filament\Resources\SpecificClassifications\Schemas;

use App\Models\GeneralClassification;
use App\Models\ParentClassification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SpecificClassificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Specific Classification')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Select::make('general_classification_id')
                    ->label('General Classification')
                    ->options(fn (): array => GeneralClassification::query()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->native(false),

                Select::make('parent_classification_id')
                    ->label('Parent Classification')
                    ->options(fn (): array => ParentClassification::query()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->native(false)
                    ->nullable(),
            ]);
    }
}
