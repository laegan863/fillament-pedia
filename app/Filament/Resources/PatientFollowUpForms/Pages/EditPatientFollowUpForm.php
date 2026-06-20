<?php

namespace App\Filament\Resources\PatientFollowUpForms\Pages;

use App\Filament\Resources\PatientFollowUpForms\PatientFollowUpFormResource;
use App\Filament\Resources\FormDemographics\FormDemographicsResource;
use App\Models\PatientFollowUpForm;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\CreateAction;

class EditPatientFollowUpForm extends EditRecord
{
    protected static string $resource = PatientFollowUpFormResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var PatientFollowUpForm $record */
        $record = $this->getRecord();

        return [
            ...$data,
            ...$record->treatmentRecordsForForm(),
        ];
    }

    protected function afterSave(): void
    {
        /** @var PatientFollowUpForm $record */
        $record = $this->getRecord();

        $record->syncTreatmentRecords(collect($this->form->getRawState())->toArray());
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Return')
            ->color('primary')
            ->icon('heroicon-o-arrow-uturn-left')
            ->url(fn (): string => self::getResource()::getUrl('index', [
                'formId' => $this->record->form_demographic_id,
            ])),
            DeleteAction::make(),
        ];
    }
}
