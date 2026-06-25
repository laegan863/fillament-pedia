<?php

namespace App\Filament\Resources\GeneralClassifications\Pages;

use App\Filament\Resources\GeneralClassifications\GeneralClassificationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGeneralClassifications extends ListRecords
{
    protected static string $resource = GeneralClassificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
