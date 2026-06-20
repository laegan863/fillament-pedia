<?php

namespace App\Filament\Resources\CancerDiagnoses\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;


class CancerDiagnosesTable
{

    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                    return $query
                        ->with('formDemographic')
                        ->when(
                            request()->filled('formId'),
                            fn (Builder $query) => $query->where(
                                'form_demographic_id',
                                request()->integer('formId')
                            )
                        );
                })
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('formDemographic.health_facility_id_no')
                    ->label('Patient ID')
                    ->formatStateUsing(function ($state): string {
                        if (blank($state)) {
                            return 'N/A';
                        }

                        return str_pad((string) $state, 10, '0', STR_PAD_LEFT);
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')->label('Created Date')->sortable()->searchable()->dateTime(),
                TextColumn::make('iccc_specific_classification')->label('ICC Specific Classification')
                ->sortable()->searchable(),
                TextColumn::make('clinical_stage')->label('Clinical Stage')->sortable()->searchable(),
                TextColumn::make('diagnosis_date')->label('Date of Diagnosis')->sortable()->searchable()->dateTime(),
            ])
            ->filters([
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),
                Action::make('viewForm')
                    ->label('View Form')
                    ->icon('heroicon-o-eye')
                    ->openUrlInNewTab()
                    ->url(fn ($record): string => route('form-details', ['cancerDiagnose' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
