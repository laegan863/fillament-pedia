<?php

namespace App\Filament\Resources\GeneralClassifications\Pages;

use App\Filament\Resources\GeneralClassifications\GeneralClassificationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGeneralClassification extends EditRecord
{
    protected static string $resource = GeneralClassificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
