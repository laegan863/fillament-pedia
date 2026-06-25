<?php

namespace App\Filament\Resources\GeneralClassifications;

use App\Filament\Resources\GeneralClassifications\Pages\CreateGeneralClassification;
use App\Filament\Resources\GeneralClassifications\Pages\EditGeneralClassification;
use App\Filament\Resources\GeneralClassifications\Pages\ListGeneralClassifications;
use App\Filament\Resources\GeneralClassifications\Schemas\GeneralClassificationForm;
use App\Filament\Resources\GeneralClassifications\Tables\GeneralClassificationsTable;
use App\Models\GeneralClassification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class GeneralClassificationResource extends Resource
{
    protected static UnitEnum|string|null $navigationGroup = 'Classifications';

    protected static ?int $navigationSort = 3;

    protected static ?string $model = GeneralClassification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Folder;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return GeneralClassificationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GeneralClassificationsTable::configure($table);
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
            'index' => ListGeneralClassifications::route('/'),
            'create' => CreateGeneralClassification::route('/create'),
            'edit' => EditGeneralClassification::route('/{record}/edit'),
        ];
    }
}
