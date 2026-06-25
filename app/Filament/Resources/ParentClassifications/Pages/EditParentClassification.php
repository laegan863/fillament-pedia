<?php

namespace App\Filament\Resources\ParentClassifications\Pages;

use App\Filament\Resources\ParentClassifications\ParentClassificationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditParentClassification extends EditRecord
{
    protected static string $resource = ParentClassificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
