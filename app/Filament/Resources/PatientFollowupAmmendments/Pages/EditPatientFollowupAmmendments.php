<?php

namespace App\Filament\Resources\PatientFollowupAmmendments\Pages;

use App\Filament\Resources\PatientFollowupAmmendments\PatientFollowupAmmendmentsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPatientFollowupAmmendments extends EditRecord
{
    protected static string $resource = PatientFollowupAmmendmentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
