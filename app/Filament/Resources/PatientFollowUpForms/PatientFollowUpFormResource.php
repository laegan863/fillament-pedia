<?php

namespace App\Filament\Resources\PatientFollowUpForms;

use App\Filament\Resources\PatientFollowUpForms\Pages\CreatePatientFollowUpForm;
use App\Filament\Resources\PatientFollowUpForms\Pages\EditPatientFollowUpForm;
use App\Filament\Resources\PatientFollowUpForms\Pages\ListPatientFollowUpForms;
use App\Filament\Resources\PatientFollowUpForms\Schemas\PatientFollowUpFormForm;
use App\Filament\Resources\PatientFollowUpForms\Tables\PatientFollowUpFormsTable;
use App\Models\PatientFollowUpForm;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PatientFollowUpFormResource extends Resource
{
    protected static ?string $model = PatientFollowUpForm::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'patient follow up form';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return PatientFollowUpFormForm::configure($schema);
    }

    public function Requests(Request $request): void
    {
        $this->formSchema = PatientFollowUpFormForm::configure($this->formSchema, $request);
    }

    public static function table(Table $table): Table
    {
        return PatientFollowUpFormsTable::configure($table);
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
            'index' => ListPatientFollowUpForms::route('/'),
            'create' => CreatePatientFollowUpForm::route('/create/'),
            'edit' => EditPatientFollowUpForm::route('/{record}/edit'),
        ];
    }
}
