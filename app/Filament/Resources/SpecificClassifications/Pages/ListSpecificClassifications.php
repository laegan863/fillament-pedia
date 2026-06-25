<?php

namespace App\Filament\Resources\SpecificClassifications\Pages;

use App\Filament\Resources\SpecificClassifications\SpecificClassificationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSpecificClassifications extends ListRecords
{
    protected static string $resource = SpecificClassificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
