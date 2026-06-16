<?php

namespace App\Filament\Resources\PatientFollowUpForms\Pages;

use App\Filament\Resources\PatientFollowUpForms\PatientFollowUpFormResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPatientFollowUpForm extends EditRecord
{
    protected static string $resource = PatientFollowUpFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
