<?php

namespace App\Filament\Resources\SpecificClassifications\Pages;

use App\Filament\Resources\SpecificClassifications\SpecificClassificationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSpecificClassification extends EditRecord
{
    protected static string $resource = SpecificClassificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
