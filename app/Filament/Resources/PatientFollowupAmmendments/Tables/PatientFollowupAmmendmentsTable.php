<?php

namespace App\Filament\Resources\PatientFollowupAmmendments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PatientFollowupAmmendmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('iccc_specific_classification')
                    ->label('ICC Specific Classification')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('clinical_stage')
                    ->label('Clinical Stage')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('diagnosis_date')
                    ->label('Date of Diagnosis')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
