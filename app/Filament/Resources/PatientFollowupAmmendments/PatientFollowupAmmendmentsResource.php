<?php

namespace App\Filament\Resources\PatientFollowupAmmendments;

use App\Filament\Resources\PatientFollowupAmmendments\Pages\CreatePatientFollowupAmmendments;
use App\Filament\Resources\PatientFollowupAmmendments\Pages\EditPatientFollowupAmmendments;
use App\Filament\Resources\PatientFollowupAmmendments\Pages\ListPatientFollowupAmmendments;
use App\Filament\Resources\PatientFollowupAmmendments\Schemas\PatientFollowupAmmendmentsForm;
use App\Filament\Resources\PatientFollowupAmmendments\Tables\PatientFollowupAmmendmentsTable;
use App\Models\PatientFollowupAmmendments;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PatientFollowupAmmendmentsResource extends Resource
{
    protected static ?string $model = PatientFollowupAmmendments::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'iccc_specific_classification';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return PatientFollowupAmmendmentsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PatientFollowupAmmendmentsTable::configure($table);
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
            'index' => ListPatientFollowupAmmendments::route('/'),
            'create' => CreatePatientFollowupAmmendments::route('/create'),
            'edit' => EditPatientFollowupAmmendments::route('/{record}/edit'),
        ];
    }
}
