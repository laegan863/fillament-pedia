<?php

namespace App\Filament\Resources\ParentClassifications;

use App\Filament\Resources\ParentClassifications\Pages\CreateParentClassification;
use App\Filament\Resources\ParentClassifications\Pages\EditParentClassification;
use App\Filament\Resources\ParentClassifications\Pages\ListParentClassifications;
use App\Filament\Resources\ParentClassifications\Schemas\ParentClassificationForm;
use App\Filament\Resources\ParentClassifications\Tables\ParentClassificationsTable;
use App\Models\ParentClassification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ParentClassificationResource extends Resource
{

    protected static ?string $model = ParentClassification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Folder;

    protected static UnitEnum|string|null $navigationGroup = 'Classifications';
    protected static ?string $navigationLabel = 'Parent Classifications';
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ParentClassificationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ParentClassificationsTable::configure($table);
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
            'index' => ListParentClassifications::route('/'),
            'create' => CreateParentClassification::route('/create'),
            'edit' => EditParentClassification::route('/{record}/edit'),
        ];
    }
}
