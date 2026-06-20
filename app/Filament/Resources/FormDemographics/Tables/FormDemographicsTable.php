<?php

namespace App\Filament\Resources\FormDemographics\Tables;

use App\Filament\Resources\CancerDiagnoses\CancerDiagnoseResource;
use App\Filament\Resources\PatientFollowUpForms\PatientFollowUpFormResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Livewire\Component as LivewireComponent;

class FormDemographicsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('health_facility_id_no')->label('Patient Hospital No.')->sortable()->searchable()->formatStateUsing(fn ($state) => str_pad($state, 10, '0', STR_PAD_LEFT)),
                TextColumn::make('patient_first_name')->label('First Name')->sortable()->searchable(),
                TextColumn::make('patient_surname')->label('Surname')->sortable()->searchable(),
                TextColumn::make('patient_middle_name')->label('Middle Name')->sortable()->searchable(),
                TextColumn::make('sex_at_birth')->label('Sex at Birth')->sortable()->searchable(),
                TextColumn::make('date_of_birth')->label('Date of Birth')->sortable()->searchable(),
            ])
            ->filters([

            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make(),

                Action::make('selectForm')
                    ->label('Select Form')
                    ->icon('heroicon-o-check')
                    ->modalHeading('Select Form')
                    ->modalDescription('Choose which form you want to open.')
                    ->modalSubmitActionLabel('Open Form')
                    ->form([
                        Select::make('form')
                            ->label('Form')
                            ->options([
                                'form1' => 'FORM 1A - PRIMARY CANCER DIAGNOSE FORM',
                                'follow_up' => 'FORM 2 - PATIENT FOLLOW-UP FORM',
                                // 'amendment' => 'FORM 2A - PRIMARY CANCER AMENDMENT FORM',
                                // 'surgery' => 'FORM 2B - SURGERY DATA',
                                // 'drug_therapy' => 'FORM 2C - ANTI-CANCER DRUG THERAPY',
                                // 'radiotherapy' => 'FORM 2D - RADIOTHERAPY',
                                // 'theranostics' => 'FORM 2E - THERANOSTICS',
                                // 'palliative_care' => 'FORM 2F - PALLIATIVE CARE',
                                // 'other_therapies' => 'FORM 2G - OTHER CANCER-DIRECTED THERAPIES',
                            ])
                            ->native(false)
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data, $record, LivewireComponent $livewire) {
                        $url = match ($data['form'] ?? null) {
                            'form1' => CancerDiagnoseResource::getUrl('index', [
                                'formId' => $record->id,
                            ]),

                            'follow_up' => PatientFollowUpFormResource::getUrl('index', [
                                'formId' => $record->id,
                            ]),

                            // 'amendment' => AmendmentFormResource::getUrl('index', [
                            //     'formId' => $record->id,
                            // ]),

                            // 'surgery' => SurgeryDataFormResource::getUrl('index', [
                            //     'formId' => $record->id,
                            // ]),

                            // 'drug_therapy' => DrugTherapyFormResource::getUrl('index', [
                            //     'formId' => $record->id,
                            // ]),

                            // 'radiotherapy' => RadiotherapyFormResource::getUrl('index', [
                            //     'formId' => $record->id,
                            // ]),

                            // 'theranostics' => TheranosticsFormResource::getUrl('index', [
                            //     'formId' => $record->id,
                            // ]),

                            // 'palliative_care' => PalliativeCareFormResource::getUrl('index', [
                            //     'formId' => $record->id,
                            // ]),

                            // 'other_therapies' => OtherCancerDirectedTherapiesFormResource::getUrl('index', [
                            //     'formId' => $record->id,
                            // ]),

                            default => null,
                        };

                        if (! $url) {
                            return;
                        }

                        return $livewire->redirect($url, navigate: true);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
