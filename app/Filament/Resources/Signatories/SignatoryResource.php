<?php

namespace App\Filament\Resources\Signatories;

use App\Filament\Resources\Signatories\Pages\CreateSignatory;
use App\Filament\Resources\Signatories\Pages\EditSignatory;
use App\Filament\Resources\Signatories\Pages\ListSignatories;
use App\Filament\Resources\Signatories\Schemas\SignatoryForm;
use App\Filament\Resources\Signatories\Tables\SignatoriesTable;
use App\Models\Signatory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SignatoryResource extends Resource
{
    protected static ?string $model = Signatory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static UnitEnum|string|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Noted By';

    protected static ?string $modelLabel = 'Noted By Signatory';

    protected static ?string $pluralModelLabel = 'Noted By Signatories';

    protected static ?string $slug = 'noted-by';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SignatoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SignatoriesTable::configure($table);
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
            'index' => ListSignatories::route('/'),
            'create' => CreateSignatory::route('/create'),
            'edit' => EditSignatory::route('/{record}/edit'),
        ];
    }
}
