<?php

namespace App\Filament\Resources\CancerDiagnoses\Pages;

use App\Filament\Resources\CancerDiagnoses\CancerDiagnoseResource;
use App\Models\FormDemographics;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Url;
use Filament\Actions\Action;

class CreateCancerDiagnose extends CreateRecord
{
    protected static string $resource = CancerDiagnoseResource::class;

    #[Url(as: 'formId')]
    public ?int $formId = null;

    public function mount(): void
    {
        $this->formId ??= request()->integer('formId') ?: null;

        parent::mount();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (
            ! $this->formId ||
            ! FormDemographics::query()->whereKey($this->formId)->exists()
        ) {
            throw ValidationException::withMessages([
                'data.form_demographic_id' => 'The selected patient demographics record is invalid.',
            ]);
        }

        $data['form_demographic_id'] = $this->formId;

        return $data;
    }

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index', ['formId' => $this->formId]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
            ->label('Return') 
            ->icon('heroicon-o-arrow-uturn-left')
            ->extraAttributes(['class' => 'me-auto'])
            ->url(fn (): string => static::getResource()::getUrl('index', ['formId' => $this->formId])),
        ];
    }
}


