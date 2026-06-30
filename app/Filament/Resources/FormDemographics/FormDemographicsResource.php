<?php

namespace App\Filament\Resources\FormDemographics;

use App\Filament\Resources\FormDemographics\Pages\CreateFormDemographics;
use App\Filament\Resources\FormDemographics\Pages\EditFormDemographics;
use App\Filament\Resources\FormDemographics\Pages\ListFormDemographics;
use App\Filament\Resources\FormDemographics\Schemas\FormDemographicsForm;
use App\Filament\Resources\FormDemographics\Tables\FormDemographicsTable;
use App\Models\FormDemographics;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FormDemographicsResource extends Resource
{
    protected static ?string $model = FormDemographics::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Folder;

    protected static UnitEnum|string|null $navigationGroup = 'Main';

    protected static ?string $navigationLabel = 'Patient Records';

    protected static ?string $modelLabel = 'Patient Record';

    protected static ?string $pluralModelLabel = 'Patient Records';

    // protected static ?string $recordTitleAttribute = 'form demographics profile';

    public static function form(Schema $schema): Schema
    {
        return FormDemographicsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FormDemographicsTable::configure($table);
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
            'index' => ListFormDemographics::route('/'),
            'create' => CreateFormDemographics::route('/create'),
            'edit' => EditFormDemographics::route('/{record}/edit'),
        ];
    }
}
