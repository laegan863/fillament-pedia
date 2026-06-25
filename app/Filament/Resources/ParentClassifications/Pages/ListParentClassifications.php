<?php

namespace App\Filament\Resources\ParentClassifications\Pages;

use App\Filament\Resources\ParentClassifications\ParentClassificationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListParentClassifications extends ListRecords
{
    protected static string $resource = ParentClassificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
