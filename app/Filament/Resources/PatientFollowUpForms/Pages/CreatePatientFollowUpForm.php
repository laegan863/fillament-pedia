<?php

namespace App\Filament\Resources\PatientFollowUpForms\Pages;

use App\Filament\Resources\PatientFollowUpForms\PatientFollowUpFormResource;
use App\Models\FormDemographics;
use App\Models\PatientFollowUpForm;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Url;
use Filament\Actions\CreateAction;

class CreatePatientFollowUpForm extends CreateRecord
{
    protected static string $resource = PatientFollowUpFormResource::class;

    #[Url(as: 'formId')]
    public ?int $formId = null;

    public function mount(): void
    {
        $this->formId ??= request()->integer('formId') ?: null;

        parent::mount();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (
            $this->formId &&
            ! FormDemographics::query()->whereKey($this->formId)->exists()
        ) {
            throw ValidationException::withMessages([
                'data.form_demographic_id' => 'The selected patient demographics record is invalid.',
            ]);
        }

        $data['form_demographic_id'] = $this->formId;

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var PatientFollowUpForm $record */
        $record = $this->record;

        $record->syncTreatmentRecords(collect($this->form->getRawState())->toArray());
    }

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index', ['formId' => $this->formId]);
    }

    public function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Return') 
            ->icon('heroicon-o-arrow-uturn-left')
            ->extraAttributes(['class' => 'me-auto'])
            ->url(fn (): string => static::getResource()::getUrl('index', ['formId' => request()->query('formId')])),
        ];
    }
}
