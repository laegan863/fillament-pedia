<?php

namespace App\Filament\Resources\PatientFollowUpForms\Pages;

use App\Filament\Resources\PatientFollowUpForms\PatientFollowUpFormResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPatientFollowUpForms extends ListRecords
{
    protected static string $resource = PatientFollowUpFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New patient follow up form')
            ->url(fn (): string => static::getResource()::getUrl('create', [
                'formId' => request()->query('formId'),
            ])),
        ];
    }
}
