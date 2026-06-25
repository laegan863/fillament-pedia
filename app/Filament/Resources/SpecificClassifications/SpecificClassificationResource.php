<?php

namespace App\Filament\Resources\SpecificClassifications;

use App\Filament\Resources\SpecificClassifications\Pages\CreateSpecificClassification;
use App\Filament\Resources\SpecificClassifications\Pages\EditSpecificClassification;
use App\Filament\Resources\SpecificClassifications\Pages\ListSpecificClassifications;
use App\Filament\Resources\SpecificClassifications\Schemas\SpecificClassificationForm;
use App\Filament\Resources\SpecificClassifications\Tables\SpecificClassificationsTable;
use App\Models\SpecificClassification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SpecificClassificationResource extends Resource
{
    protected static UnitEnum|string|null $navigationGroup = 'Classifications';

    protected static ?int $navigationSort = 1;

    protected static ?string $model = SpecificClassification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Folder;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SpecificClassificationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SpecificClassificationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSpecificClassifications::route('/'),
            'create' => CreateSpecificClassification::route('/create'),
            'edit' => EditSpecificClassification::route('/{record}/edit'),
        ];
    }
}
