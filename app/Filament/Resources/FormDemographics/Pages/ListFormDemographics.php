<?php

namespace App\Filament\Resources\FormDemographics\Pages;

use App\Filament\Resources\FormDemographics\FormDemographicsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFormDemographics extends ListRecords
{
    protected static string $resource = FormDemographicsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
