<?php

namespace App\Filament\Resources\PatientFollowUpForms\Pages;

use App\Filament\Resources\PatientFollowUpForms\PatientFollowUpFormResource;
use App\Filament\Resources\FormDemographics\FormDemographicsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPatientFollowUpForms extends ListRecords
{
    protected static string $resource = PatientFollowUpFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Return')
            ->icon('heroicon-o-arrow-uturn-left')
            ->url(fn (): string => FormDemographicsResource::getUrl('index')),
            CreateAction::make()->label('New patient follow up form')
            ->url(fn (): string => static::getResource()::getUrl('create', [
                'formId' => request()->query('formId'),
            ])),
        ];
    }
}
