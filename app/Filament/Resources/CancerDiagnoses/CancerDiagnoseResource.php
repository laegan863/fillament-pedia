<?php

namespace App\Filament\Resources\CancerDiagnoses;

use App\Filament\Resources\CancerDiagnoses\Pages\CreateCancerDiagnose;
use App\Filament\Resources\CancerDiagnoses\Pages\EditCancerDiagnose;
use App\Filament\Resources\CancerDiagnoses\Pages\ListCancerDiagnoses;
use App\Filament\Resources\CancerDiagnoses\Schemas\CancerDiagnoseForm;
use App\Filament\Resources\CancerDiagnoses\Tables\CancerDiagnosesTable;
use App\Models\CancerDiagnose;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CancerDiagnoseResource extends Resource
{
    protected static ?string $model = CancerDiagnose::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $recordTitleAttribute = 'cancer diagnose';

    public static function form(Schema $schema): Schema
    {
        return CancerDiagnoseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CancerDiagnosesTable::configure($table);
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
            'index' => ListCancerDiagnoses::route('/'),
            'create' => CreateCancerDiagnose::route('/create/{formId?}'),
            'edit' => EditCancerDiagnose::route('/{record}/edit'),
        ];
    }
}
