<?php

namespace App\Filament\Resources\PatientFollowupAmmendments\Pages;

use App\Filament\Resources\PatientFollowupAmmendments\PatientFollowupAmmendmentsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPatientFollowupAmmendments extends ListRecords
{
    protected static string $resource = PatientFollowupAmmendmentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
